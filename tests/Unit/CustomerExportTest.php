<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Support\Facades\Auth;

class CustomerExportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([
            \App\Http\Middleware\IdentifyTenant::class,
            \App\Http\Middleware\AuthApiMiddleware::class,
        ]);
    }

    public function test_export_customer_excel()
    {
        $user = User::first() ?? User::factory()->create();
        Auth::login($user);
        $this->actingAs($user);

        // Create test customer
        $customer = User::create([
            'name'        => 'Test Customer Export Co',
            'email'       => 'testcustomer_' . uniqid() . '@example.com',
            'phone'       => '9' . rand(100000000, 999999999),
            'role'        => 'customer',
            'branch_id'   => $user->id,
            'isDeleted'   => 0,
            'gst_number'  => '24ABCDE1234F1Z7',
            'password'    => bcrypt('123456'),
        ]);

        UserDetail::create([
            'user_id' => $customer->id,
            'country' => 'India',
            'city'    => 'Surat',
            'address' => 'GIDC, Surat',
        ]);

        $response = $this->getJson('/api/export-customer-excel?selectedSubAdminId=' . $user->id);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
        ]);
        $this->assertNotEmpty($response->json('file_url'));
        $this->assertNotEmpty($response->json('file_name'));

        // Clean up
        UserDetail::where('user_id', $customer->id)->delete();
        $customer->delete();
    }

    public function test_export_customer_pdf()
    {
        $user = User::first() ?? User::factory()->create();
        Auth::login($user);
        $this->actingAs($user);

        // Create test customer
        $customer = User::create([
            'name'        => 'Test Customer PDF Co',
            'email'       => 'testcustomer_pdf_' . uniqid() . '@example.com',
            'phone'       => '9' . rand(100000000, 999999999),
            'role'        => 'customer',
            'branch_id'   => $user->id,
            'isDeleted'   => 0,
            'gst_number'  => '24ABCDE1234F1Z8',
            'password'    => bcrypt('123456'),
        ]);

        UserDetail::create([
            'user_id' => $customer->id,
            'country' => 'India',
            'city'    => 'Surat',
            'address' => 'Adajan, Surat',
        ]);

        $response = $this->getJson('/api/export-customer-pdf?selectedSubAdminId=' . $user->id);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
        ]);
        $this->assertNotEmpty($response->json('file_url'));
        $this->assertNotEmpty($response->json('file_name'));

        // Clean up
        UserDetail::where('user_id', $customer->id)->delete();
        $customer->delete();
    }
}
