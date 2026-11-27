<?php

namespace Tests\Unit;

use App\Services\ApiKeyService;
use App\Services\Purchase\PurchaseBillVisionService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PurchaseBillVisionServiceTest extends TestCase
{
    public function test_service_is_configured_and_resolves_keys(): void
    {
        config(['services.gemini.key' => 'mock-test-key']);
        $service = app(PurchaseBillVisionService::class);
        $this->assertTrue($service->isConfigured());

        $aiKeys = ApiKeyService::getCandidates('AI');
        $this->assertNotEmpty($aiKeys);
    }

    public function test_automatic_failover_when_primary_key_has_permission_denied(): void
    {
        Http::fake(function (\Illuminate\Http\Client\Request $request) {
            $key = $request->header('x-goog-api-key')[0] ?? '';

            // Simulate primary key having Google Cloud "Permission denied on the service."
            if ($key === 'mock-primary-ai-key') {
                return Http::response([
                    'error' => [
                        'code' => 403,
                        'message' => 'Permission denied on the service.',
                        'status' => 'PERMISSION_DENIED',
                    ],
                ], 403);
            }

            // Simulate working backup key responding with bill items
            return Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'vendor_name' => 'Test Vendor',
                                        'bill_no' => 'BILL-101',
                                        'bill_date' => '2026-11-27',
                                        'grand_total' => 500,
                                        'items' => [
                                            [
                                                'name' => 'Brake Pad',
                                                'qty' => 2,
                                                'rate' => 250,
                                                'amount' => 500,
                                            ],
                                        ],
                                    ]),
                                ],
                            ],
                        ],
                    ],
                ],
            ], 200);
        });

        // Set tenant with primary AI key that fails and backup AI key that succeeds
        app()->instance('tenant', [
            'id' => 1,
            'api_keys' => [
                [
                    'label' => 'AI',
                    'value' => 'mock-primary-ai-key',
                    'enabled' => true,
                ],
            ],
            'backup_api_keys' => [
                [
                    'label' => 'AI',
                    'value' => 'mock-backup-ai-key',
                    'enabled' => true,
                ],
            ],
        ]);

        $service = app(PurchaseBillVisionService::class);
        $file = UploadedFile::fake()->image('bill.jpg', 600, 800);

        $extracted = $service->extract($file);

        $this->assertEquals('Test Vendor', $extracted['vendor_name']);
        $this->assertEquals('BILL-101', $extracted['bill_no']);
        $this->assertEquals(500, $extracted['grand_total']);
        $this->assertCount(1, $extracted['items']);
        $this->assertEquals('Brake Pad', $extracted['items'][0]['name']);
    }
}
