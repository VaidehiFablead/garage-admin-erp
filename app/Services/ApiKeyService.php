<?php

namespace App\Services;

use App\Services\Tenancy\TenantManager;
use Illuminate\Support\Facades\Log;

class ApiKeyService
{
    /**
     * Default fallbacks if no key is configured in tenant or environment.
     */
    protected static array $defaultFallbacks = [
        'GST'       => '',
        'AI'        => '',
        'REMOVE_BG' => '',
        'OPENAI'    => '',
    ];

    /**
     * Secondary fallback keys.
     */
    protected static array $defaultBackupFallbacks = [
        'AI'        => '',
        'GST'       => '',
        'REMOVE_BG' => '',
    ];

    /**
     * Normalize label for comparison (case-insensitive, trims underscores/hyphens/spaces).
     */
    public static function normalizeLabel(string $label): string
    {
        return strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $label));
    }

    /**
     * Get an API key dynamically by label ('GST', 'AI', 'REMOVE_BG', 'OPENAI', etc.).
     *
     * Resolution order:
     * 1. Enabled key in tenant's primary api_keys
     * 2. Enabled key in tenant's backup_api_keys
     * 3. Environment variable / config fallback
     * 4. Provided $fallback or built-in default
     */
    public static function get(string $label, ?string $fallback = null): ?string
    {
        $primary = static::getPrimary($label);
        if (!empty($primary)) {
            return $primary;
        }

        $backup = static::getBackup($label);
        if (!empty($backup)) {
            return $backup;
        }

        $envVal = static::getEnvFallback($label);
        if (!empty($envVal)) {
            return $envVal;
        }

        if ($fallback !== null) {
            return $fallback;
        }

        return static::getDefaultFallback($label);
    }

    /**
     * Get primary API key from active tenant api_keys list if enabled.
     */
    public static function getPrimary(string $label, ?string $fallback = null): ?string
    {
        $keys = static::tenantKeys('api_keys');
        $val = static::findKeyInList($keys, $label);
        if (!empty($val)) {
            return $val;
        }

        return $fallback;
    }

    /**
     * Get backup API key from active tenant backup_api_keys list if enabled.
     */
    public static function getBackup(string $label, ?string $fallback = null): ?string
    {
        $keys = static::tenantKeys('backup_api_keys');
        $val = static::findKeyInList($keys, $label);
        if (!empty($val)) {
            return $val;
        }

        return $fallback;
    }

    /**
     * Check if a specific API key is enabled in tenant configuration.
     */
    public static function isEnabled(string $label): bool
    {
        $norm = static::normalizeLabel($label);
        $keys = static::tenantKeys('api_keys');
        foreach ($keys as $item) {
            if (is_array($item) && static::normalizeLabel((string) ($item['label'] ?? '')) === $norm) {
                return (bool) ($item['enabled'] ?? true) && !empty($item['value']);
            }
        }

        $backupKeys = static::tenantKeys('backup_api_keys');
        foreach ($backupKeys as $item) {
            if (is_array($item) && static::normalizeLabel((string) ($item['label'] ?? '')) === $norm) {
                return (bool) ($item['enabled'] ?? true) && !empty($item['value']);
            }
        }

        return !empty(static::getEnvFallback($label));
    }

    /**
     * Get candidate keys for execution (primary first, then backup if different and enabled).
     *
     * @return array<string>
     */
    public static function getCandidates(string $label): array
    {
        $norm = static::normalizeLabel($label);

        // If the tenant explicitly configured this key as disabled, do NOT use it or fall back
        $tenantPrimary = static::tenantKeys('api_keys');
        foreach ($tenantPrimary as $item) {
            if (is_array($item) && static::normalizeLabel((string) ($item['label'] ?? '')) === $norm) {
                if (isset($item['enabled']) && ! (bool) $item['enabled']) {
                    return [];
                }
            }
        }

        $candidates = [];

        $primary = static::getPrimary($label);
        if (!empty($primary)) {
            $candidates[] = $primary;
        }

        $backup = static::getBackup($label);
        if (!empty($backup) && !in_array($backup, $candidates, true)) {
            $candidates[] = $backup;
        }

        if (empty($candidates)) {
            $envVal = static::getEnvFallback($label);
            if (!empty($envVal) && !in_array($envVal, $candidates, true)) {
                $candidates[] = $envVal;
            }
            $defaultVal = static::getDefaultFallback($label);
            if (!empty($defaultVal) && !in_array($defaultVal, $candidates, true)) {
                $candidates[] = $defaultVal;
            }
        }

        // Ensure at least one backup candidate exists if available
        if (count($candidates) < 2) {
            $defaultBackupVal = static::$defaultBackupFallbacks[$norm] ?? null;
            if (!empty($defaultBackupVal) && !in_array($defaultBackupVal, $candidates, true)) {
                $candidates[] = $defaultBackupVal;
            }
        }

        return array_values(array_unique(array_filter($candidates)));
    }

    /**
     * Find enabled key in an array of key definitions.
     */
    protected static function findKeyInList(array $list, string $label): ?string
    {
        $norm = static::normalizeLabel($label);
        foreach ($list as $item) {
            if (!is_array($item)) {
                continue;
            }
            $itemLabel = static::normalizeLabel((string) ($item['label'] ?? ''));
            if ($itemLabel === $norm) {
                $enabled = (bool) ($item['enabled'] ?? true);
                if ($enabled && !empty($item['value'])) {
                    return trim((string) $item['value']);
                }
            }
        }
        return null;
    }

    /**
     * Get keys from current tenant payload.
     */
    protected static function tenantKeys(string $bucket): array
    {
        try {
            $raw = null;
            if (function_exists('tenant')) {
                $raw = tenant($bucket);
            }

            if (empty($raw) && app()->bound('tenant')) {
                $t = app('tenant');
                if (is_array($t) && isset($t[$bucket])) {
                    $raw = $t[$bucket];
                }
            }

            if (empty($raw) && app()->bound(TenantManager::class)) {
                $t = app(TenantManager::class)->current();
                if (is_array($t) && isset($t[$bucket])) {
                    $raw = $t[$bucket];
                }
            }

            if (empty($raw)) {
                $sessionTenant = session('tenant');
                if (is_array($sessionTenant) && isset($sessionTenant[$bucket])) {
                    $raw = $sessionTenant[$bucket];
                }
            }

            if (is_string($raw)) {
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) {
                    return $decoded;
                }
            }

            if (is_array($raw)) {
                return $raw;
            }
        } catch (\Throwable $e) {
            Log::debug('ApiKeyService tenant lookup failed: ' . $e->getMessage());
        }

        return [];
    }

    /**
     * Fallback to environment or services config.
     */
    public static function getEnvFallback(string $label): ?string
    {
        $norm = static::normalizeLabel($label);

        return match ($norm) {
            'GST', 'CLEARTAX' => env('GST_API_KEY') ?: (env('CLEARTAX_AUTH_TOKEN') ?: env('CLEARTAX_API_KEY')),
            'AI', 'GEMINI' => env('GEMINI_API_KEY') ?: (config('services.gemini.key') ?: null),
            'REMOVEBG' => env('REMOVE_BG_API_KEY') ?: (config('services.removebg.key') ?: null),
            'OPENAI' => env('OPENAI_API_KEY') ?: (config('services.openai.key') ?: null),
            default => env(strtoupper($label) . '_API_KEY') ?: env(strtoupper($label)),
        };
    }

    /**
     * Fallback to system default.
     */
    public static function getDefaultFallback(string $label): ?string
    {
        $norm = static::normalizeLabel($label);
        return match ($norm) {
            'GST', 'CLEARTAX' => static::$defaultFallbacks['GST'] ?? null,
            'AI', 'GEMINI' => static::$defaultFallbacks['AI'] ?? null,
            'REMOVEBG' => static::$defaultFallbacks['REMOVE_BG'] ?? null,
            'OPENAI' => static::$defaultFallbacks['OPENAI'] ?? null,
            default => null,
        };
    }
}
