<?php

namespace Tests\Unit;

use App\Services\ApiKeyService;
use App\Services\Purchase\PurchaseBillVisionService;
use Tests\TestCase;

class PurchaseBillVisionServiceTest extends TestCase
{
    public function test_service_is_configured_and_resolves_keys(): void
    {
        $service = app(PurchaseBillVisionService::class);
        $this->assertTrue($service->isConfigured());

        $aiKeys = ApiKeyService::getCandidates('AI');
        $this->assertNotEmpty($aiKeys);
        $this->assertStringStartsWith('AIzaSy', $aiKeys[0]);
    }
}
