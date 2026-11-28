<?php

namespace Tests\Unit;

use Tests\TestCase;

class JobCardPrintViewTest extends TestCase
{
    public function test_job_card_print_view_renders_correctly()
    {
        $setting = (object)[
            'currency_symbol' => 'Rs.',
            'shop_name' => 'Test Garage',
            'shop_email' => 'test@garage.com',
            'shop_mobile' => '9876543210',
            'shop_address' => 'Test Address',
            'gst_no' => '24AAAAA0000A1Z5',
            'logo' => null,
        ];
        $jobCard = (object)[
            'id' => 1,
            'job_card_no' => 'JC-001',
            'customer_name' => 'Test Customer',
            'phone' => '1234567890',
            'mobile' => '1234567890',
            'address' => 'Surat, Gujarat',
            'vehicle_name' => 'Car',
            'model' => 'Model X',
            'registration_no' => 'GJ01AB1234',
            'vehicle_no' => 'GJ01AB1234',
            'chassis_no' => 'CH123',
            'engine_no' => 'EN123',
            'odometer' => 1000,
            'km_hours' => '1000',
            'due_km_hours' => '5000',
            'date' => '2026-11-28',
            'created_at' => '2026-11-28 10:00:00',
            'next_due_date' => '2027-02-28',
            'gst' => '1',
            'gst_no' => '24AAAAA0000A1Z5',
            'customer' => null,
            'category' => 'General',
            'labour_discount' => null,
            'parts_discount' => null,
            'discount' => 0,
            'total_subtotal_amount' => 170,
            'remaining_amount' => 170,
            'payment_method' => 'cash',
            'payment_type' => 'Full Payment',
            'type' => 'job_card',
            'total_labour' => 100,
            'total_parts' => 70,
            'paid' => 0,
            'status' => 'Pending',
            'remark' => 'Test Remark',
            'comment' => 'Test Comment',
            'cash_amount' => 0,
            'online_amount' => 0,
        ];

        $rendered = view('job-card.job_card_print_pdf', [
            'jobCard' => $jobCard,
            'setting' => $setting,
            'client' => null,
            'vehicle' => null,
            'labourDetails' => [
                ['name' => 'Oil Change', 'price' => 100, 'remark' => 'Checked engine']
            ],
            'partsDetails' => [
                ['name' => 'Engine Oil', 'qty' => 1, 'price' => 70, 'remark' => '5W30 used']
            ],
            'paymentModeLabel' => 'CASH',
            'paymentTypeLabel' => 'Full Payment',
            'paidAmount' => 0,
            'remainingAmount' => 170,
            'labourDiscount' => ['type' => 'flat', 'amount' => 0],
            'partsDiscount' => ['type' => 'flat', 'amount' => 0],
            'extraDiscount' => 0,
            'grandTotal' => 170,
        ])->render();

        // 1. Remarks header should be present
        $this->assertStringContainsString('Remarks', $rendered);
        $this->assertStringContainsString('Checked engine', $rendered);
        $this->assertStringContainsString('5W30 used', $rendered);

        // 2. RATE and AMOUNT column headers should NOT be present in tables
        $this->assertStringNotContainsString('>Rate<', $rendered);
        $this->assertStringNotContainsString('>Amount<', $rendered);

        // 3. Payment Mode & Remaining / Balance should NOT be present
        $this->assertStringNotContainsString('Payment Mode:', $rendered);
        $this->assertStringNotContainsString('Remaining / Balance:', $rendered);
        $this->assertStringNotContainsString('Paid Amount:', $rendered);
    }
}
