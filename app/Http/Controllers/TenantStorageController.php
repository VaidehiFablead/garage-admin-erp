<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Serves tenant storage files (logos, favicons, invoices, avatars, uploads)
 * both in local environments and multi-tenant live environments where files
 * are isolated per client under storage/tenants/{id}/app/public.
 */
class TenantStorageController extends Controller
{
    public function __invoke(Request $request, string $path): BinaryFileResponse
    {
        $normalizedPath = ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path), DIRECTORY_SEPARATOR);

        // Strip accidental duplicate "storage/" or "public/" prefix
        $normalizedPath = preg_replace('#^(public[\\/]+)?(storage[\\/]+)?#i', '', $normalizedPath);

        // Pattern 1: Direct tenant storage path: tenants/{id}/app/public/{subpath}
        if (preg_match('#^tenants[\\/](\d+)[\\/]app[\\/]public[\\/](.*)$#i', $normalizedPath, $matches)) {
            $tenantId = (int) $matches[1];
            $relativePath = $matches[2];
            $base = base_path('storage'.DIRECTORY_SEPARATOR.'tenants'.DIRECTORY_SEPARATOR.$tenantId.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'public');
            $file = realpath($base.DIRECTORY_SEPARATOR.$relativePath);

            if ($file && str_starts_with($file, realpath($base).DIRECTORY_SEPARATOR) && is_file($file)) {
                return $this->respondWithFile($file);
            }
        }

        // Pattern 2: Current tenant's isolated storage (storage_path resolves to storage/tenants/{id})
        $root = realpath(storage_path('app/public'));
        if ($root) {
            $file = realpath($root.DIRECTORY_SEPARATOR.$normalizedPath);
            if ($file && str_starts_with($file, $root.DIRECTORY_SEPARATOR) && is_file($file)) {
                return $this->respondWithFile($file);
            }
        }

        // Pattern 3: Shared base storage fallback (storage/app/public)
        $sharedRoot = realpath(base_path('storage/app/public'));
        if ($sharedRoot) {
            $sharedFile = realpath($sharedRoot.DIRECTORY_SEPARATOR.$normalizedPath);
            if ($sharedFile && str_starts_with($sharedFile, $sharedRoot.DIRECTORY_SEPARATOR) && is_file($sharedFile)) {
                return $this->respondWithFile($sharedFile);
            }
        }

        // Pattern 4: Fallback across any tenant directories (storage/tenants/*/app/public)
        $candidates = glob(base_path('storage'.DIRECTORY_SEPARATOR.'tenants'.DIRECTORY_SEPARATOR.'*'.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.$normalizedPath));
        if (!empty($candidates)) {
            foreach ($candidates as $candidate) {
                if (is_file($candidate)) {
                    return $this->respondWithFile($candidate);
                }
            }
        }

        // Pattern 5: Direct public storage symlink/folder if present
        $pubRoot = realpath(public_path('storage'));
        if ($pubRoot) {
            $pubFile = realpath($pubRoot.DIRECTORY_SEPARATOR.$normalizedPath);
            if ($pubFile && str_starts_with($pubFile, $pubRoot.DIRECTORY_SEPARATOR) && is_file($pubFile)) {
                return $this->respondWithFile($pubFile);
            }
        }

        abort(404, 'File not found');
    }

    private function respondWithFile(string $file): BinaryFileResponse
    {
        $mime = @mime_content_type($file) ?: 'application/octet-stream';

        return response()->file($file, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=86400',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
