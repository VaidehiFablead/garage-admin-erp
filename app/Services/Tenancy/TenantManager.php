<?php

namespace App\Services\Tenancy;

use App\Exceptions\TenantResolutionException;
use Illuminate\Cache\FileStore;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Maps the current request (or console process) to one client of the
 * admin-products panel and points the database, storage, cache and
 * session at that client's own resources.
 */
class TenantManager
{
    private ?array $tenant = null;

    private string $sharedStoragePath;

    private ?string $baseSessionCookie = null;

    private ?Repository $lookupCache = null;

    public function __construct()
    {
        $this->sharedStoragePath = storage_path();
    }

    public function enabled(): bool
    {
        return (bool) config('tenant.enabled');
    }

    public function current(): ?array
    {
        return $this->tenant;
    }

    public function id(): ?int
    {
        return isset($this->tenant['id']) ? (int) $this->tenant['id'] : null;
    }

    public function sharedStoragePath(string $path = ''): string
    {
        return $this->sharedStoragePath.($path !== '' ? DIRECTORY_SEPARATOR.ltrim($path, '\\/') : '');
    }

    public static function normalizeHost(string $host): string
    {
        $host = strtolower(trim($host));
        $host = preg_replace('#^[a-z][a-z0-9+.-]*://#', '', $host);
        $host = preg_replace('#[/?\#].*$#', '', $host);
        $host = preg_replace('#:\d+$#', '', $host);

        return preg_replace('#^www\.#', '', $host);
    }

    /**
     * @param  bool  $fresh  skip the short-lived cache (used at login so a
     *                       suspension in admin-products applies immediately)
     */
    public function resolveByHost(string $host, bool $fresh = false): array
    {
        $host = static::normalizeHost($host);
        $key = 'host:'.sha1($host.'|'.config('tenant.product_code').'|'.config('tenant.project_code'));
        $cache = $this->lookupCache();

        if (! $fresh && is_array($cached = $cache->get($key))) {
            return $this->activate($cached);
        }

        try {
            $response = $this->sendApiRequest('post', '/resolve', [
                'domain' => $host,
                'product_code' => config('tenant.product_code'),
                'project_code' => config('tenant.project_code') ?: null,
            ]);
        } catch (ConnectionException $e) {
            return $this->activateStaleOrFail($key, $e->getMessage());
        }

        if ($response->serverError()) {
            return $this->activateStaleOrFail($key, 'HTTP '.$response->status());
        }

        $tenant = $this->tenantFromResponse($response, $key);

        $cache->put($key, $tenant, max(1, (int) config('tenant.cache_seconds')));
        $cache->put($key.':stale', $tenant, max(1, (int) config('tenant.grace_seconds')));

        return $this->activate($tenant);
    }

    /**
     * Resolve tenant by user email (for central login).
     */
    public function resolveByEmail(string $email, bool $fresh = false): array
    {
        $email = trim(strtolower($email));
        $key = 'user_email:'.sha1($email.'|'.config('tenant.product_code').'|'.config('tenant.project_code'));
        $cache = $this->lookupCache();

        if (! $fresh && is_array($cached = $cache->get($key))) {
            return $this->activate($cached);
        }

        try {
            $response = $this->sendApiRequest('post', '/resolve-user', array_filter([
                'email' => $email,
                'username' => $email,
                'login' => $email,
                'product_code' => config('tenant.product_code'),
                'project_code' => config('tenant.project_code') ?: null,
            ]));
        } catch (ConnectionException $e) {
            return $this->activateStaleOrFail($key, $e->getMessage());
        }

        if ($response->serverError()) {
            return $this->activateStaleOrFail($key, 'HTTP '.$response->status());
        }

        $tenant = $this->tenantFromResponse($response, $key);

        $cache->put($key, $tenant, max(1, (int) config('tenant.cache_seconds')));
        $cache->put($key.':stale', $tenant, max(1, (int) config('tenant.grace_seconds')));

        return $this->activate($tenant);
    }

    /**
     * Every client database of this product/project (for console commands).
     *
     * @return array<int, array>
     */
    public function all(?int $tenantId = null): array
    {
        try {
            $response = $this->sendApiRequest('get', '/list', array_filter([
                'product_code' => config('tenant.product_code'),
                'project_code' => config('tenant.project_code') ?: null,
                'tenant_id' => $tenantId,
            ]));
        } catch (ConnectionException $e) {
            throw new TenantResolutionException('UNREACHABLE', 'Unable to reach the admin panel: '.$e->getMessage(), 503);
        }

        if (! $response->successful() || ! $response->json('success')) {
            throw new TenantResolutionException(
                (string) ($response->json('code') ?? 'UNKNOWN'),
                (string) ($response->json('message') ?? 'Tenant list request failed (HTTP '.$response->status().').'),
                $response->status()
            );
        }

        return array_values(array_filter(
            (array) $response->json('tenants', []),
            fn ($tenant) => $this->belongsToThisApp($tenant)
        ));
    }

    public function activateById(int $tenantId, bool $fresh = false): array
    {
        return $this->resolveById($tenantId, $fresh);
    }

    /**
     * Resolve and activate tenant by setup/tenant ID.
     */
    public function resolveById(int $tenantId, bool $fresh = false): array
    {
        $key = 'tenant_id:'.(int) $tenantId.'|'.config('tenant.product_code').'|'.config('tenant.project_code');
        $cache = $this->lookupCache();

        if (! $fresh && is_array($cached = $cache->get($key))) {
            return $this->activate($cached);
        }

        try {
            $tenants = $this->all($tenantId);
            $tenant = $tenants[0] ?? null;
        } catch (ConnectionException $e) {
            return $this->activateStaleOrFail($key, $e->getMessage());
        }

        if (! $tenant) {
            $cache->forget($key);
            $cache->forget($key.':stale');
            throw new TenantResolutionException('NOT_FOUND', "Tenant #{$tenantId} was not found for this product.", 404);
        }

        if (! $this->isUsable($tenant)) {
            $cache->forget($key);
            $cache->forget($key.':stale');
            throw new TenantResolutionException(
                strtoupper((string) ($tenant['status'] ?? 'INACTIVE')),
                'Your account is inactive or expired. Please contact the administrator.',
                403,
                $tenant
            );
        }

        $cache->put($key, $tenant, max(1, (int) config('tenant.cache_seconds')));
        $cache->put($key.':stale', $tenant, max(1, (int) config('tenant.grace_seconds')));

        return $this->activate($tenant);
    }

    public function forgetCachedId(int $tenantId): void
    {
        $key = 'tenant_id:'.(int) $tenantId.'|'.config('tenant.product_code').'|'.config('tenant.project_code');
        $this->lookupCache()->forget($key);
        $this->lookupCache()->forget($key.':stale');
    }

    /**
     * Synchronize profile update to admin_erp panel and invalidate cached tenant resolution.
     */
    public function syncProfileToAdmin(array $payload): ?Response
    {
        if (! $this->enabled()) {
            return null;
        }

        try {
            $response = $this->sendApiRequest('post', '/update-profile', array_filter([
                'client_id' => $payload['client_id'] ?? ($this->tenant['client_id'] ?? null),
                'tenant_id' => $payload['tenant_id'] ?? ($this->tenant['id'] ?? null),
                'domain' => $payload['domain'] ?? ($this->tenant['domain'] ?? null),
                'current_email' => $payload['current_email'] ?? null,
                'name' => $payload['name'] ?? null,
                'email' => $payload['email'] ?? null,
                'phone' => $payload['phone'] ?? null,
                'password' => $payload['password'] ?? null,
            ]));

            $this->forgetCurrentCache();

            return $response;
        } catch (\Throwable $e) {
            Log::warning('TenantManager syncProfileToAdmin error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Clear all cached lookup entries for the current tenant.
     */
    public function forgetCurrentCache(): void
    {
        try {
            $cache = $this->lookupCache();
            if ($this->tenant) {
                if (!empty($this->tenant['domain'])) {
                    $host = static::normalizeHost($this->tenant['domain']);
                    $key = 'host:'.sha1($host.'|'.config('tenant.product_code').'|'.config('tenant.project_code'));
                    $cache->forget($key);
                    $cache->forget($key.':stale');
                }
                if (!empty($this->tenant['email'])) {
                    $email = trim(strtolower($this->tenant['email']));
                    $key = 'user_email:'.sha1($email.'|'.config('tenant.product_code').'|'.config('tenant.project_code'));
                    $cache->forget($key);
                    $cache->forget($key.':stale');
                }
                if (!empty($this->tenant['id'])) {
                    $key = 'tenant_id:'.(int) $this->tenant['id'].'|'.config('tenant.product_code').'|'.config('tenant.project_code');
                    $cache->forget($key);
                    $cache->forget($key.':stale');
                }
            }
        } catch (\Throwable) {
            // Ignore cache forget errors
        }
    }

    public function isUsable(array $tenant): bool
    {
        return strcasecmp((string) ($tenant['status'] ?? ''), 'Active') === 0
            && strcasecmp((string) ($tenant['client_status'] ?? 'Active'), 'Active') === 0
            && ! in_array($tenant['membership_status'] ?? null, ['Expired', 'Pending'], true);
    }

    public function activate(array $tenant): array
    {
        $db = $tenant['db'] ?? [];
        if (empty($db['database'])) {
            throw new TenantResolutionException('NOT_PROVISIONED', 'The database for this account has not been configured yet.', 409);
        }

        $connection = config('database.default');
        $dbHost = $db['host'] ?? 'localhost';
        if (PHP_OS_FAMILY !== 'Windows' && in_array($dbHost, ['127.0.0.1', 'localhost', ''], true)) {
            $dbHost = 'localhost';
        }

        $dbUsername = $db['username'] ?? '';
        $dbPassword = (string) ($db['password'] ?? '');
        $databaseName = $db['database'];

        // On local development, allow using local MySQL credentials (e.g. XAMPP root with blank password)
        if (app()->environment('local') && env('TENANT_USE_LOCAL_DB_CREDENTIALS', true)) {
            $defaultUsername = config("database.connections.{$connection}.username");
            $defaultPassword = config("database.connections.{$connection}.password");
            if (!empty($defaultUsername) || $defaultPassword === '') {
                $dbUsername = $defaultUsername;
                $dbPassword = $defaultPassword ?? '';
            }

            try {
                $pdo = new \PDO("mysql:host={$dbHost};port=" . (int) ($db['port'] ?? 3306), $dbUsername, $dbPassword, [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_SILENT,
                    \PDO::ATTR_TIMEOUT => 2,
                ]);
                $stmt = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = " . $pdo->quote($databaseName));
                if (!$stmt || !$stmt->fetch()) {
                    $cleanDbName = preg_replace('/^u\d+_/', '', $databaseName);
                    $checkCleanStmt = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = " . $pdo->quote($cleanDbName));
                    if ($checkCleanStmt && $checkCleanStmt->fetch()) {
                        $databaseName = $cleanDbName;
                    } else {
                        $databaseName = config("database.connections.{$connection}.database") ?: $databaseName;
                    }
                }
            } catch (\Throwable) {
                // Keep default
            }
        }

        config([
            "database.connections.{$connection}.host" => $dbHost,
            "database.connections.{$connection}.port" => (int) ($db['port'] ?? 3306),
            "database.connections.{$connection}.database" => $databaseName,
            "database.connections.{$connection}.username" => $dbUsername,
            "database.connections.{$connection}.password" => $dbPassword,
        ]);
        DB::purge($connection);

        $this->isolateFilesystem((int) $tenant['id']);

        Log::shareContext(['tenant_id' => (int) $tenant['id']]);

        $this->tenant = $tenant;
        app()->instance('tenant', $tenant);

        return $tenant;
    }

    public function forgetCachedHost(string $host): void
    {
        $host = static::normalizeHost($host);
        $key = 'host:'.sha1($host.'|'.config('tenant.product_code').'|'.config('tenant.project_code'));
        $this->lookupCache()->forget($key);
        $this->lookupCache()->forget($key.':stale');
    }

    /**
     * Uploads, generated PDFs, file cache and sessions all live under
     * storage/tenants/{id}, so code calling storage_path() or the public
     * disk is isolated per client without modification.
     */
    private function isolateFilesystem(int $tenantId): void
    {
        $root = $this->sharedStoragePath('tenants'.DIRECTORY_SEPARATOR.$tenantId);

        foreach (['app/public', 'framework/cache/data', 'framework/sessions', 'framework/views', 'logs'] as $dir) {
            File::ensureDirectoryExists($root.DIRECTORY_SEPARATOR.$dir, 0755);
        }

        app()->useStoragePath($root);

        $this->baseSessionCookie ??= (string) config('session.cookie');

        config([
            'filesystems.disks.local.root' => $root.DIRECTORY_SEPARATOR.'app',
            'filesystems.disks.public.root' => $root.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'public',
            'cache.stores.file.path' => $root.DIRECTORY_SEPARATOR.'framework'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'data',
            'cache.prefix' => 'tenant_'.$tenantId.'_',
            'session.files' => $root.DIRECTORY_SEPARATOR.'framework'.DIRECTORY_SEPARATOR.'sessions',
            'session.cookie' => $this->baseSessionCookie.'_t'.$tenantId,
        ]);

        Storage::forgetDisk(['local', 'public']);
        Cache::forgetDriver('file');
        Cache::forgetDriver(config('cache.default'));
    }

    private function tenantFromResponse(Response $response, string $key): array
    {
        $body = (array) $response->json();

        if ($response->successful() && ! empty($body['success']) && ! empty($body['tenant'])) {
            if (! $this->belongsToThisApp($body['tenant'])) {
                throw new TenantResolutionException('PRODUCT_MISMATCH', 'This account does not belong to this product.', 403);
            }

            $tenant = $body['tenant'];
            if (! empty($tenant['id'])) {
                $idKey = 'tenant_id:'.(int) $tenant['id'].'|'.config('tenant.product_code').'|'.config('tenant.project_code');
                $this->lookupCache()->put($idKey, $tenant, max(1, (int) config('tenant.cache_seconds')));
                $this->lookupCache()->put($idKey.':stale', $tenant, max(1, (int) config('tenant.grace_seconds')));
            }

            return $tenant;
        }

        $this->lookupCache()->forget($key);
        $this->lookupCache()->forget($key.':stale');

        throw new TenantResolutionException(
            (string) ($body['code'] ?? 'UNKNOWN'),
            (string) ($body['message'] ?? 'Unable to load your account.'),
            $response->status() >= 400 ? $response->status() : 403,
            $body
        );
    }

    private function belongsToThisApp(array $tenant): bool
    {
        $product = strtoupper((string) config('tenant.product_code'));
        $project = strtoupper((string) config('tenant.project_code'));

        $tProduct = strtoupper((string) ($tenant['product_code'] ?? ''));
        $tProject = strtoupper((string) ($tenant['project_code'] ?? ''));
        $tDomain = strtoupper((string) ($tenant['domain'] ?? ''));
        $tDatabase = strtoupper((string) ($tenant['db']['database'] ?? ($tenant['database_name'] ?? '')));

        // If this app is Garage ERP, allow if tenant belongs to GARAGE (by project, product, domain, or db)
        $isGarageApp = str_contains($product, 'GARAGE') || str_contains($project, 'GARAGE');
        $isGarageTenant = str_contains($tProject, 'GARAGE') 
            || str_contains($tProduct, 'GARAGE')
            || str_contains($tDomain, 'GARAGE')
            || str_contains($tDatabase, 'GARAGE');
        if ($isGarageApp && $isGarageTenant) {
            return true;
        }

        // If this app is Hotel ERP, allow if tenant belongs to HOTEL (by project or product)
        $isHotelApp = str_contains($product, 'HOTEL') || str_contains($project, 'HOTEL');
        $isHotelTenant = str_contains($tProject, 'HOTEL') || str_contains($tProduct, 'HOTEL');
        if ($isHotelApp && $isHotelTenant) {
            return true;
        }

        $targetProject = $project ?: $product;
        if (!empty($targetProject) && !empty($tProject) && $this->codesMatch($tProject, $targetProject)) {
            return true;
        }

        $productMatches = ($tProduct === '' || $product === '' || $this->codesMatch($tProduct, $product) || ($tProduct === 'ERP' && str_contains($product, 'ERP')));
        $projectMatches = ($tProject === '' || $project === '' || $this->codesMatch($tProject, $project) || $this->codesMatch($tProject, $product));

        return $productMatches && $projectMatches;
    }

    private function codesMatch(?string $codeA, ?string $codeB): bool
    {
        if (empty($codeA) || empty($codeB)) {
            return true;
        }
        $a = strtoupper(trim($codeA));
        $b = strtoupper(trim($codeB));
        if ($a === $b) {
            return true;
        }
        $cleanA = str_replace(['_', '-'], '', $a);
        $cleanB = str_replace(['_', '-'], '', $b);
        if ($cleanA === $cleanB) {
            return true;
        }
        if (str_contains($a, 'GARAGE') && str_contains($b, 'GARAGE')) {
            return true;
        }
        if (str_contains($a, 'HOTEL') && str_contains($b, 'HOTEL')) {
            return true;
        }
        $rootA = preg_replace('/(_|-)?ERP$/', '', $a);
        $rootB = preg_replace('/(_|-)?ERP$/', '', $b);
        return (!empty($rootA) && !empty($rootB) && $rootA === $rootB);
    }

    private function activateStaleOrFail(string $key, string $reason): array
    {
        $stale = $this->lookupCache()->get($key.':stale');

        Log::warning('Tenant API unavailable', ['reason' => $reason, 'using_stale' => is_array($stale)]);

        if (is_array($stale)) {
            return $this->activate($stale);
        }

        throw new TenantResolutionException('UNREACHABLE', 'Unable to reach the account server. Please try again in a few minutes.', 503);
    }

    private function sendApiRequest(string $method, string $path, array $data = []): Response
    {
        $urls = array_filter([
            (string) config('tenant.api_url'),
            'http://localhost/ERP/admin_erp/public/api/tenant',
            'http://127.0.0.1:8000/api/tenant',
        ]);
        $urls = array_values(array_unique($urls));

        $lastException = null;
        foreach ($urls as $baseUrl) {
            try {
                $client = $this->clientForUrl($baseUrl);
                $methodLower = strtolower($method);
                if ($methodLower === 'get') {
                    return $client->get($path, $data);
                }
                return $client->post($path, $data);
            } catch (ConnectionException $e) {
                $lastException = $e;
            }
        }

        throw $lastException ?: new ConnectionException("Could not connect to tenant API");
    }

    private function clientForUrl(string $url): PendingRequest
    {
        if ($url === '' || strlen((string) config('tenant.secret')) < 32 || config('tenant.product_code') === '') {
            throw new TenantResolutionException(
                'NOT_CONFIGURED',
                'Tenancy is enabled but TENANT_API_URL, TENANT_API_SECRET (min 32 chars) or TENANT_PRODUCT_CODE is missing.',
                500
            );
        }

        return Http::baseUrl($url)
            ->acceptJson()
            ->asForm()
            ->timeout(max(1, (int) config('tenant.http_timeout')))
            ->withHeaders(['X-Tenant-Secret' => (string) config('tenant.secret')]);
    }

    private function lookupCache(): Repository
    {
        if ($this->lookupCache === null) {
            $path = $this->sharedStoragePath('framework'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'tenant-lookup');
            File::ensureDirectoryExists($path, 0755);
            $this->lookupCache = Cache::repository(new FileStore(app('files'), $path));
        }

        return $this->lookupCache;
    }
}
