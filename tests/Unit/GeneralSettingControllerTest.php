<?php

namespace Tests\Unit;

use App\Http\Controllers\api\GeneralSettingController;
use App\Models\Setting;
use Tests\TestCase;

class GeneralSettingControllerTest extends TestCase
{
    public function test_setting_model_and_controller_bank_fields(): void
    {
        // 1. Ensure columns check method runs without error
        GeneralSettingController::ensureBankColumnsExist();

        // 2. Test saving bank fields with and without GST
        $setting = Setting::firstOrNew(['branch_id' => 99999]);
        $setting->name = 'Test Garage';
        $setting->email = 'test@garage.com';
        $setting->phone = '9999999999';
        $setting->address = 'Test Address';
        $setting->currency_symbol = '₹';
        $setting->currency_position = 'left';

        // With GST bank details
        $setting->bank_name = 'State Bank of India';
        $setting->branch = 'Main Branch';
        $setting->ac_no = '1234567890';
        $setting->ifsc_code = 'SBIN0001234';
        $setting->qr_code = 'qr_codes/test_with_gst.png';

        // Without GST bank details
        $setting->bank_name_without_gst = 'HDFC Bank';
        $setting->branch_without_gst = 'Ring Road Branch';
        $setting->ac_no_without_gst = '9876543210';
        $setting->ifsc_code_without_gst = 'HDFC0005678';
        $setting->qr_code_without_gst = 'qr_codes/test_without_gst.png';

        $setting->save();

        // 3. Verify retrieval
        $fresh = Setting::where('branch_id', 99999)->first();
        $this->assertNotNull($fresh);
        $this->assertEquals('State Bank of India', $fresh->bank_name);
        $this->assertEquals('Main Branch', $fresh->branch);
        $this->assertEquals('1234567890', $fresh->ac_no);
        $this->assertEquals('SBIN0001234', $fresh->ifsc_code);
        $this->assertEquals('qr_codes/test_with_gst.png', $fresh->qr_code);

        $this->assertEquals('HDFC Bank', $fresh->bank_name_without_gst);
        $this->assertEquals('Ring Road Branch', $fresh->branch_without_gst);
        $this->assertEquals('9876543210', $fresh->ac_no_without_gst);
        $this->assertEquals('HDFC0005678', $fresh->ifsc_code_without_gst);
        $this->assertEquals('qr_codes/test_without_gst.png', $fresh->qr_code_without_gst);

        // Cleanup
        $fresh->delete();
    }
}
