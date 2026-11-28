<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\AutoCarCategory;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AutoCarCategoryImportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([
            \App\Http\Middleware\IdentifyTenant::class,
            \App\Http\Middleware\AuthApiMiddleware::class,
        ]);
    }

    public function test_import_categories_from_csv()
    {
        $user = User::first() ?? User::factory()->create();
        \Illuminate\Support\Facades\Auth::login($user);
        $this->actingAs($user);

        $csvContent = "Category Name\n"
                    . "Test Category Alpha\n"
                    . "Test Category Beta\n";

        $tempFile = tempnam(sys_get_temp_dir(), 'csv');
        file_put_contents($tempFile, $csvContent);

        $uploadedFile = new UploadedFile(
            $tempFile,
            'test_categories.csv',
            'text/csv',
            null,
            true
        );

        $response = $this->postJson('/api/importAutoCarCategories', [
            'file' => $uploadedFile,
            'sub_admin_id' => $user->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
        ]);

        $this->assertDatabaseHas('auto_car_categories', [
            'name' => 'Test Category Alpha',
            'branch_id' => $user->id,
            'is_deleted' => 0,
        ]);

        $this->assertDatabaseHas('auto_car_categories', [
            'name' => 'Test Category Beta',
            'branch_id' => $user->id,
            'is_deleted' => 0,
        ]);

        // Clean up
        AutoCarCategory::whereIn('name', ['Test Category Alpha', 'Test Category Beta'])->delete();
        @unlink($tempFile);
    }

    public function test_import_categories_from_excel()
    {
        $user = User::first() ?? User::factory()->create();
        \Illuminate\Support\Facades\Auth::login($user);
        $this->actingAs($user);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Category Name');
        $sheet->setCellValue('A2', 'Excel Category Test 1');
        $sheet->setCellValue('A3', 'Excel Category Test 2');

        $tempFile = tempnam(sys_get_temp_dir(), 'xlsx') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        $uploadedFile = new UploadedFile(
            $tempFile,
            'test_categories.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        $response = $this->postJson('/api/importAutoCarCategories', [
            'file' => $uploadedFile,
            'sub_admin_id' => $user->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
        ]);

        $this->assertDatabaseHas('auto_car_categories', [
            'name' => 'Excel Category Test 1',
            'branch_id' => $user->id,
        ]);

        // Clean up
        AutoCarCategory::whereIn('name', ['Excel Category Test 1', 'Excel Category Test 2'])->delete();
        @unlink($tempFile);
    }

    public function test_export_categories_to_excel()
    {
        $user = User::first() ?? User::factory()->create();
        \Illuminate\Support\Facades\Auth::login($user);
        $this->actingAs($user);

        // Seed a category
        AutoCarCategory::create([
            'name' => 'Export Sample Category',
            'branch_id' => $user->id,
            'is_deleted' => 0,
        ]);

        $response = $this->getJson('/api/export-autocar-categories?selectedSubAdminId=' . $user->id);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
        ]);
        $this->assertNotEmpty($response->json('file_url'));

        // Clean up
        AutoCarCategory::where('name', 'Export Sample Category')->delete();
    }

    public function test_download_sample_files()
    {
        $user = User::first() ?? User::factory()->create();
        \Illuminate\Support\Facades\Auth::login($user);
        $this->actingAs($user);

        $csvResponse = $this->get('/autocar-category-sample-csv');
        $csvResponse->assertStatus(200);
        $this->assertTrue(str_contains($csvResponse->getContent(), 'Category Name'));

        $excelResponse = $this->get('/autocar-category-sample-excel');
        $excelResponse->assertStatus(200);
    }
}
