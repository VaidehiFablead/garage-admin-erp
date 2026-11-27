<?php

namespace Tests\Unit;

use App\Services\ApiKeyService;
use Tests\TestCase;

class ApiKeyServiceTest extends TestCase
{
    private array $sampleTenant = [
        'id' => 1,
        'domain' => 'testgarage.local',
        'api_keys' => [
            [
                'label' => 'GST',
                'value' => 'primary-gst-token-12345',
                'enabled' => true,
                'has_value' => true,
            ],
            [
                'label' => 'AI',
                'value' => 'primary-ai-key-abc',
                'enabled' => true,
                'has_value' => true,
            ],
            [
                'label' => 'REMOVE_BG',
                'value' => 'primary-remove-bg-xyz',
                'enabled' => false, // disabled in primary!
                'has_value' => true,
            ],
            [
                'label' => 'OPENAI',
                'value' => 'primary-openai-key-sk123',
                'enabled' => false,
                'has_value' => true,
            ],
        ],
        'backup_api_keys' => [
            [
                'label' => 'AI',
                'value' => 'backup-ai-key-def',
                'enabled' => true,
                'has_value' => true,
            ],
            [
                'label' => 'GST',
                'value' => 'backup-gst-token-67890',
                'enabled' => true,
                'has_value' => true,
            ],
            [
                'label' => 'REMOVE_BG',
                'value' => 'backup-remove-bg-active',
                'enabled' => true, // active in backup!
                'has_value' => true,
            ],
        ],
    ];

    public function test_resolves_primary_and_backup_keys(): void
    {
        app()->instance('tenant', $this->sampleTenant);

        // GST is active in primary
        $this->assertEquals('primary-gst-token-12345', ApiKeyService::get('GST'));
        $this->assertEquals('primary-gst-token-12345', ApiKeyService::getPrimary('GST'));
        $this->assertEquals('backup-gst-token-67890', ApiKeyService::getBackup('GST'));

        // AI is active in primary
        $this->assertEquals('primary-ai-key-abc', ApiKeyService::get('AI'));
        $this->assertEquals('backup-ai-key-def', ApiKeyService::getBackup('AI'));

        // REMOVE_BG is disabled in primary, so get() falls back to enabled backup
        $this->assertEquals('backup-remove-bg-active', ApiKeyService::get('REMOVE_BG'));
        $this->assertNull(ApiKeyService::getPrimary('REMOVE_BG'));
        $this->assertEquals('backup-remove-bg-active', ApiKeyService::getBackup('REMOVE_BG'));

        // Helper functions
        $this->assertEquals('primary-gst-token-12345', api_key('GST'));
        $this->assertEquals('backup-gst-token-67890', backup_api_key('GST'));
    }

    public function test_candidate_list_order(): void
    {
        app()->instance('tenant', $this->sampleTenant);

        $gstCandidates = ApiKeyService::getCandidates('GST');
        $this->assertCount(2, $gstCandidates);
        $this->assertEquals('primary-gst-token-12345', $gstCandidates[0]);
        $this->assertEquals('backup-gst-token-67890', $gstCandidates[1]);

        $aiCandidates = ApiKeyService::getCandidates('AI');
        $this->assertCount(2, $aiCandidates);
        $this->assertEquals('primary-ai-key-abc', $aiCandidates[0]);
        $this->assertEquals('backup-ai-key-def', $aiCandidates[1]);

        // OPENAI is disabled in tenant primary, so getCandidates returns empty and does not fall back
        $openaiCandidates = ApiKeyService::getCandidates('OPENAI');
        $this->assertEmpty($openaiCandidates);
    }

    public function test_alias_resolution_for_gemini_api_key(): void
    {
        // Setup matching the user's setup UI:
        // Row 2: AI (failing key)
        // Row 5: GEMINI_API_KEY (working key with AQ. prefix)
        $tenantWithAliases = [
            'id' => 2,
            'domain' => 'garage-erp.fableadtech.com',
            'api_keys' => [
                ['label' => 'GST', 'value' => 'mock-gst', 'enabled' => true],
                ['label' => 'AI', 'value' => 'mock-failing-ai-key', 'enabled' => true],
                ['label' => 'REMOVE_BG', 'value' => 'mock-bg', 'enabled' => true],
                ['label' => 'OPENAI', 'value' => 'mock-openai', 'enabled' => false],
                ['label' => 'GEMINI_API_KEY', 'value' => 'AQ.mock-working-gemini-key', 'enabled' => true],
            ],
            'backup_api_keys' => [],
        ];

        app()->instance('tenant', $tenantWithAliases);

        // When queried for 'AI', ApiKeyService should find both 'AI' and 'GEMINI_API_KEY'
        // and prioritize AQ. key first
        $candidates = ApiKeyService::getCandidates('AI');
        $this->assertCount(2, $candidates);
        $this->assertEquals('AQ.mock-working-gemini-key', $candidates[0]);
        $this->assertEquals('mock-failing-ai-key', $candidates[1]);

        // ApiKeyService::get('AI') should also resolve to the working key
        $this->assertEquals('AQ.mock-working-gemini-key', ApiKeyService::get('AI'));
    }
}
