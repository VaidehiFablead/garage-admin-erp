<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\LabourService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LabourServiceImportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([
            \App\Http\Middleware\IdentifyTenant::class,
            \App\Http\Middleware\AuthApiMiddleware::class,
        ]);
    }

    public function test_import_labour_services_from_csv()
    {
        $user = User::first() ?? User::factory()->create();
        \Illuminate\Support\Facades\Auth::login($user);
        $this->actingAs($user);

        $csvContent = "Service Name,Price\n"
                    . "Import Test Service A,450.00\n"
                    . "Import Test Service B,1200.50\n";

        $tempFile = tempnam(sys_get_temp_dir(), 'csv');
        file_put_contents($tempFile, $csvContent);

        $uploadedFile = new UploadedFile(
            $tempFile,
            'test_labour.csv',
            'text/csv',
            null,
            true
        );

        $response = $this->postJson('/api/importLabourServices', [
            'file' => $uploadedFile,
            'sub_admin_id' => $user->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
        ]);

        $this->assertDatabaseHas('labour_services', [
            'name' => 'Import Test Service A',
            'branch_id' => $user->id,
        ]);

        $this->assertDatabaseHas('labour_services', [
            'name' => 'Import Test Service B',
            'branch_id' => $user->id,
        ]);

        // Clean up
        LabourService::whereIn('name', ['Import Test Service A', 'Import Test Service B'])->delete();
        @unlink($tempFile);
    }

    public function test_import_labour_services_updates_existing_service()
    {
        $user = User::first() ?? User::factory()->create();
        \Illuminate\Support\Facades\Auth::login($user);
        $this->actingAs($user);

        // Ensure clean state
        LabourService::where('name', 'Existing Service To Update')->delete();

        // Create initial service
        $initial = LabourService::create([
            'name' => 'Existing Service To Update',
            'price' => 200.00,
            'branch_id' => $user->id,
            'is_deleted' => 0,
        ]);

        $csvContent = "Service Name,Price\n"
                    . "Existing Service To Update,550.00\n";

        $tempFile = tempnam(sys_get_temp_dir(), 'csv');
        file_put_contents($tempFile, $csvContent);

        $uploadedFile = new UploadedFile(
            $tempFile,
            'test_update.csv',
            'text/csv',
            null,
            true
        );

        $response = $this->postJson('/api/importLabourServices', [
            'file' => $uploadedFile,
            'sub_admin_id' => $user->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'updated' => 1,
        ]);

        $this->assertDatabaseHas('labour_services', [
            'id' => $initial->id,
            'name' => 'Existing Service To Update',
            'price' => 550.00,
        ]);

        // Clean up
        $initial->delete();
        @unlink($tempFile);
    }

    public function test_import_labour_services_from_excel_xlsx()
    {
        $user = User::first() ?? User::factory()->create();
        \Illuminate\Support\Facades\Auth::login($user);
        $this->actingAs($user);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Service Name');
        $sheet->setCellValue('B1', 'Price');
        $sheet->setCellValue('A2', 'Excel Test Service X');
        $sheet->setCellValue('B2', 999.00);

        $tempFile = tempnam(sys_get_temp_dir(), 'xlsx') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        $uploadedFile = new UploadedFile(
            $tempFile,
            'test_labour.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        $response = $this->postJson('/api/importLabourServices', [
            'file' => $uploadedFile,
            'sub_admin_id' => $user->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
        ]);

        $this->assertDatabaseHas('labour_services', [
            'name' => 'Excel Test Service X',
            'branch_id' => $user->id,
        ]);

        // Clean up
        LabourService::where('name', 'Excel Test Service X')->delete();
        @unlink($tempFile);
    }

    public function test_sample_csv_download()
    {
        $controller = new \App\Http\Controllers\admin\LabourServiceController();
        $response = $controller->downloadSampleCsv();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));
        $this->assertStringContainsString('Service Name,Price', $response->getContent());
    }

    public function test_sample_excel_download()
    {
        $controller = new \App\Http\Controllers\admin\LabourServiceController();
        $response = $controller->downloadSampleExcel();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('spreadsheetml', $response->headers->get('content-type'));
    }

    public function test_import_labour_view_renders()
    {
        $controller = new \App\Http\Controllers\admin\LabourServiceController();
        $view = $controller->import_labour(request());

        $this->assertEquals('labour_service.import_labour', $view->name());
    }
}
