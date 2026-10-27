<?php

return [

    /*
    | When enabled, every request is mapped to a client (tenant) by its domain or user login.
    | The client's database credentials come from the admin-products panel and
    | the DB_* values in .env are only used when this is disabled.
    */
    'enabled' => (bool) env('TENANCY_ENABLED', false),

    /*
    | Base URL of the admin-products tenant API, e.g.
    | http://127.0.0.1:8000/api/tenant
    */
    'api_url' => rtrim((string) env('TENANT_API_URL', ''), '/'),

    /*
    | Must equal TENANT_API_SECRET in admin-products.
    */
    'secret' => (string) env('TENANT_API_SECRET', ''),

    /*
    | Codes of this application in admin-products (Products / Projects).
    | A domain is only accepted if its client bought this product + project.
    */
    'product_code' => strtoupper((string) env('TENANT_PRODUCT_CODE', 'GARAGE_ERP')),
    'project_code' => strtoupper((string) env('TENANT_PROJECT_CODE', 'GARAGE_ERP')),

    /*
    | How long a successful lookup is reused before asking admin-products again.
    | Suspending or expiring a client takes effect within this window
    | (and immediately on the next login).
    */
    'cache_seconds' => (int) env('TENANT_CACHE_SECONDS', 10),

    /*
    | If admin-products is unreachable, keep serving the last known good
    | lookup for this long so a short outage does not take every client down.
    */
    'grace_seconds' => (int) env('TENANT_GRACE_SECONDS', 86400),

    'http_timeout' => (int) env('TENANT_HTTP_TIMEOUT', 8),

];
