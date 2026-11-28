<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Support\Facades\Auth;

class VendorExportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([
            \App\Http\Middleware\IdentifyTenant::class,
            \App\Http\Middleware\AuthApiMiddleware::class,
        ]);
    }

    public function test_export_vendor_excel()
    {
        $user = User::first() ?? User::factory()->create();
        Auth::login($user);
        $this->actingAs($user);

        // Create test vendor
        $vendor = User::create([
            'name'        => 'Test Vendor Export Co',
            'email'       => 'testvendor_' . uniqid() . '@example.com',
            'phone'       => '9' . rand(100000000, 999999999),
            'role'        => 'vendor',
            'branch_id'   => $user->id,
            'isDeleted'   => 0,
            'gst_number'  => '24ABCDE1234F1Z5',
            'password'    => bcrypt('123456'),
        ]);

        UserDetail::create([
            'user_id'   => $vendor->id,
            'country'   => 'India',
            'city'      => 'Surat',
            'address'   => 'Ring Road, Surat',
        ]);

        $response = $this->getJson('/api/export-vendor-excel?selectedSubAdminId=' . $user->id);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
        ]);
        $this->assertNotEmpty($response->json('file_url'));
        $this->assertNotEmpty($response->json('file_name'));

        // Clean up
        UserDetail::where('user_id', $vendor->id)->delete();
        $vendor->delete();
    }

    public function test_export_vendor_pdf()
    {
        $user = User::first() ?? User::factory()->create();
        Auth::login($user);
        $this->actingAs($user);

        // Create test vendor
        $vendor = User::create([
            'name'        => 'Test Vendor PDF Co',
            'email'       => 'testvendor_pdf_' . uniqid() . '@example.com',
            'phone'       => '9' . rand(100000000, 999999999),
            'role'        => 'vendor',
            'branch_id'   => $user->id,
            'isDeleted'   => 0,
            'gst_number'  => '24ABCDE1234F1Z6',
            'password'    => bcrypt('123456'),
        ]);

        UserDetail::create([
            'user_id'   => $vendor->id,
            'country'   => 'India',
            'city'      => 'Surat',
            'address'   => 'Varachha, Surat',
        ]);

        $response = $this->getJson('/api/export-vendor-pdf?selectedSubAdminId=' . $user->id);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
        ]);
        $this->assertNotEmpty($response->json('file_url'));
        $this->assertNotEmpty($response->json('file_name'));

        // Clean up
        UserDetail::where('user_id', $vendor->id)->delete();
        $vendor->delete();
    }
}
