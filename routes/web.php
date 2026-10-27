<?php

use App\Http\Controllers\admin\AccountLedgerController;
use App\Http\Controllers\admin\AdvancePaymentController;
use App\Http\Controllers\admin\AppointmentController;
use App\Http\Controllers\admin\AttendanceController;
use App\Http\Controllers\admin\AttendanceeController;
use App\Http\Controllers\admin\AuthController;
use App\Http\Controllers\admin\AutoCarCategoryController;
use App\Http\Controllers\admin\AutoCarModelController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\CreditNoteItemController;
use App\Http\Controllers\admin\CreditNotesTypeController;
use App\Http\Controllers\admin\CustomerController;
use App\Http\Controllers\admin\CustomInvoiceController;
use App\Http\Controllers\admin\DailyDataController;
use App\Http\Controllers\admin\DebitNoteItemController;
use App\Http\Controllers\admin\ExpenseController;
use App\Http\Controllers\admin\ExpenseTypeController;
use App\Http\Controllers\admin\GstSalesReportController;
use App\Http\Controllers\admin\InventoryController;
use App\Http\Controllers\admin\InvoiceController;
use App\Http\Controllers\admin\JobCardController;
use App\Http\Controllers\admin\LabourServiceController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\PurchaseController;
use App\Http\Controllers\admin\PurchaseReturnController;
use App\Http\Controllers\admin\QuotationController;
use App\Http\Controllers\admin\SalaryController;
use App\Http\Controllers\admin\SalesController;
use App\Http\Controllers\admin\SalesReturnController;
use App\Http\Controllers\admin\SettingController;
use App\Http\Controllers\admin\StaffController;
use App\Http\Controllers\admin\SubBranchController;
use App\Http\Controllers\admin\UnitController;
use App\Http\Controllers\admin\VendorController;
use App\Http\Controllers\api\GstSalesReportController as ApiGstSalesReportController;
use App\Http\Controllers\api\LoginController;
use App\Http\Controllers\api\FaceLoginController;
use App\Http\Controllers\api\PayrollController as ApiPayrollController;
use App\Http\Controllers\admin\LeaveController;
use App\Http\Controllers\admin\LeaveTypeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\DepartmentController;
use App\Http\Controllers\admin\DesignationController;
use App\Http\Controllers\admin\PlanController;
use App\Http\Controllers\api\LeaveTypeController as ApiLeaveTypeController;
use App\Http\Controllers\LicenseController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// 1. The setup routes (These MUST NOT have the middleware, otherwise you get a redirect loop)
Route::prefix('setup')->group(function () {
    Route::get('/license', [LicenseController::class, 'showForm'])->name('license.form');
    Route::post('/license/verify', [LicenseController::class, 'verify'])->name('license.verify');
});

Route::post('/clear-subadmin-session', function () {
    session()->forget('selectedSubAdminId');
    return response()->json(['status' => 'cleared']);
});

Route::middleware(['guest:web'])->group(function () {
    Route::get('/', [AuthController::class, 'signin'])->name('auth.signin');
    Route::post('/', [LoginController::class, 'login'])->name('login');

    // Face recognition login (no auth required — this IS the login)
    Route::get('/api/face-login-data', [FaceLoginController::class, 'getFaceData'])->name('face.login.data');
    Route::post('/face-login', [FaceLoginController::class, 'login'])->name('face.login');
});

// Face photo serving route (no auth required — serves face images for recognition)
Route::get('/face-photo/{filename}', function ($filename) {
    $path = storage_path('app/public/staff/face/' . $filename);
    if (!file_exists($path)) {
        abort(404);
    }
    $mime = mime_content_type($path) ?: 'image/jpeg';
    return response()->file($path, ['Content-Type' => $mime]);
})->where('filename', '.+');



Route::middleware(['check.license'])->group(function () {
    

    Route::middleware(['auth:web'])->group(function () {
        Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
        Route::get('/dashboard/filter', [AuthController::class, 'filterData'])->name('dashboard.filter');

        // Search bar
        Route::get('/search-users', [AuthController::class, 'ajaxSearch'])->name('users.ajaxSearch');
        Route::post(
            '/job-cards/{id}/convert',
            [JobCardController::class, 'convertQuotation']
        )
            ->name('jobcard.convert');
        // unit

        Route::get('/units', [UnitController::class, 'unit_list'])->name('unit.list');

        // Brand
        Route::get('/brands', [BrandController::class, 'brand_list'])->name('brand.list');
        Route::get('/add-brand', [BrandController::class, 'add_brand'])->name('brand.add');
        Route::get('/edit-brand/{id}', [BrandController::class, 'edit_brand'])->name('brand.edit');

        Route::get('/inventory-list', [InventoryController::class, 'index'])->name('inventory.list');
        Route::get('/inventory-View/{id}', [InventoryController::class, 'View'])->name('inventory.View');

        // Labour Services Web Routes
        Route::get('/labours', [LabourServiceController::class, 'labour_list'])->name('labour.list');
        Route::get('/add-labour', [LabourServiceController::class, 'add_labour'])->name('labour.add');
        Route::get('/edit-labour/{id}', [LabourServiceController::class, 'edit_labour'])->name('labour.edit');

        Route::get('/autocar-categories', [AutoCarCategoryController::class, 'autoCategoryList'])->name('autocar.category.list');
        Route::get('/add-autocar-category', [AutoCarCategoryController::class, 'addAutoCategory'])->name('autocar.category.add');
        Route::get('/edit-autocar-category/{id}', [AutoCarCategoryController::class, 'editAutoCategory'])->name('autocar.category.edit');

        Route::get('/autocar-models', [AutoCarModelController::class, 'modelList'])->name('autocar.model.list');
        Route::get('/add-autocar-model', [AutoCarModelController::class, 'addModel'])->name('autocar.model.add');
        Route::get('/edit-autocar-model/{id}', [AutoCarModelController::class, 'editModel'])->name('autocar.model.edit');

        Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
        Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
        Route::get('/appointments/edit/{id}', [AppointmentController::class, 'edit'])->name('appointments.edit');
        Route::get('/appointments/view/{id}', [AppointmentController::class, 'view'])->name('appointments.view');

        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.list');
        Route::get('/attendance/add', [AttendanceController::class, 'add'])->name('attendance.create');

        Route::get('/account-ledger-list', [AccountLedgerController::class, 'index'])->name('account_ledger.list');
        Route::get('/account-ledger', [AccountLedgerController::class, 'add'])->name('account_ledger.add');
        Route::get('/pending-payment', [AccountLedgerController::class, 'pendingPayment'])->name('pening_payment.payment');

        //authcontroller
        Route::get('/forgetpassword', [AuthController::class, 'forgetpassword'])->name('auth.forgetpassword');
        Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('auth.dashboard');
        Route::get('/staff-dashboard', [AuthController::class, 'staffDashboard'])->name('auth.staff-dashboard');
        Route::get('/staff-dashboard/data', [AuthController::class, 'staffDashboardData'])->name('auth.staff-dashboard-data');
        Route::get('/hr/dashboard', [AuthController::class, 'hrDashboard'])->name('hr.dashboard');
        Route::get('/api/dashboard-filtered', [AuthController::class, 'getFilteredDashboardData']);

        Route::get('/profile', [AuthController::class, 'profile'])->name('auth.profile');
        Route::get('/taxrates', [AuthController::class, 'taxrates'])->name('auth.taxrates');
        Route::get('/currency', [AuthController::class, 'currency'])->name('auth.currency');

        // Categoty
        Route::get('/categories', [CategoryController::class, 'category_list'])->name('category.list');
        Route::get('/add-category', [CategoryController::class, 'add_category'])->name('category.add');
        Route::get('/edit-category/{id}', [CategoryController::class, 'edit_category'])->name('category.edit');
        // Product
        Route::get('/products', [ProductController::class, 'product_list'])->name('product.list');
        Route::get('/add-product', [ProductController::class, 'add_product'])->name('product.add');
        Route::get('/edit-product/{id}', [ProductController::class, 'edit_product'])->name('product.edit');
        // Route::get('/product-detail', [ProductController::class, 'product_detail'])->name('product.detail');
        Route::get('/product-detail/{id}', [ProductController::class, 'product_detail'])->name('product.detail');
        Route::get('/import-product', [ProductController::class, 'product_import'])->name('product.import');
        Route::get('/product-view/{id}', [ProductController::class, 'product_view'])->name('product.view');

        // purchase
        // Route::get('/purchase', [ProductController::class, 'product_list'])->name('Product.list');
        // Route::get('/add-product', [ProductController::class, 'add_product'])->name('Product.add');
        // Route::get('/edit-product', [ProductController::class, 'edit_product'])->name('Product.edit');
        // Route::get('/product-detail', [ProductController::class, 'product_detail'])->name('Product.detail');
        // Route::get('/import-product', [ProductController::class, 'product_import'])->name('Product.import');

        // quotation
        Route::get('/quotation', [QuotationController::class, 'quotation_list'])->name('quotation.list');
        Route::get('/add-quotation', [QuotationController::class, 'add_quotation'])->name('quotation.add');
        Route::get('/edit-quotation', [QuotationController::class, 'edit_quotation'])->name('quotation.edit');
        // Customer
        Route::get('/customer', [CustomerController::class, 'customer_list'])->name('customer.list');
        Route::get('/add-customer', [CustomerController::class, 'add_customer'])->name('customer.add');
        Route::get('/edit-customer/{num}', [CustomerController::class, 'edit_customer'])->name('customer.edit');
        Route::get('/customer-report', [CustomerController::class, 'customer_report'])->name('customer.report');
        Route::get('/customer-view/{id}', [CustomerController::class, 'customer_view'])->name('customer.view');

        // Sales
        Route::get('/sales', [SalesController::class, 'sales_list'])->name('sales.list');
        // Route::get('/add-sales', [SalesController::class, 'add_sales'])->name('sales.add');
        Route::get('/edit-sales/{num}', [SalesController::class, 'edit_sales'])->name('sales.edit');
        Route::get('/sales-report', [SalesController::class, 'sales_report'])->name('sales.report');
        Route::get('/sales-details/{num}', [SalesController::class, 'sales_details'])->name('sales.details');
        Route::get('/sales-invoice/{num}', [SalesController::class, 'salse_invoice'])->name('sales.invoice');
        Route::get('/add-sales', [SalesController::class, 'pos'])->name('sales.add');
        Route::get('/sales/invoice/pdf/{id}', [SalesController::class, 'salse_invoice_pdf'])->name('sales.invoice.pdf');
        Route::get('/sales/invoice/pdf/view/{id}', [SalesController::class, 'salse_invoice_pdf_view'])->name('sales.invoice.pdf-view');
        // Vendor
        Route::get('/vendors', [VendorController::class, 'vendor_list'])->name('vendor.list');
        Route::get('/add-vendor', [VendorController::class, 'add_vendor'])->name('vendor.add');
        Route::get('/edit-vendor/{num}', [VendorController::class, 'edit_vendor'])->name('vendor.edit');
        Route::get('/vendor-report', [VendorController::class, 'vendor_report'])->name('vendor.report');
        Route::get('/vendor-view/{id}', [VendorController::class, 'vendor_view'])->name('vendor.view');

        //settings
        Route::get('/setting', [SettingController::class, 'generalsettings'])->name('setting.generalsettings');

        //salesreturn

        Route::get('/create-sales-return', [SalesReturnController::class, 'createsales_return'])->name('salesreturn.add');
        Route::get('/create-sales-returns', [SalesReturnController::class, 'createsales_returns'])->name('salesreturn.adds');

        Route::get('/edit-sales-return', [SalesReturnController::class, 'editsales_return'])->name('salesreturn.edit');
        Route::get('/edit-sales-returns', [SalesReturnController::class, 'editsales_returns'])->name('salesreturn.edits');

        Route::get('/sales-return-list', [SalesReturnController::class, 'salesreturn_list'])->name('salesreturn.list');
        Route::get('/sales-return-lists', [SalesReturnController::class, 'salesreturn_lists'])->name('salesreturn.lists');

        // Purchase
        // Route::get('/purchase-list', [PurchaseController::class, 'purchase_list'])->name('purchase.lists');
        // Route::get('/add-purchase', [PurchaseController::class, 'add_purchase'])->name('purchase.add');
        // Route::get('/edit-purchase/{num}', [PurchaseController::class, 'edit_purchase'])->name('purchase.edit');
        // Route::get('/purchase-invoice', [PurchaseController::class, 'purchase_invoice'])->name('purchase.invoice');
        // Route::get('/print-purchase/{id}', [PurchaseController::class, 'printPurchase'])->name('purchase.print');
        // Route::get('/purchase-order-report', [PurchaseController::class, 'purchase_order_report'])->name('purchase.orderreport');
        // Route::get('/import-purchase', [PurchaseController::class, 'import_purchase'])->name('purchase.import');
        // Route::get('/purchase-report', [PurchaseController::class, 'purchase_report'])->name('purchase.report');
        // // Route::get('/purchase-details', [PurchaseController::class, 'purchase_details'])->name('purchase.details');
        // Route::get('/purchase-details', [PurchaseController::class, 'purchaseDetails'])->name('purchase.details');
        // Route::get('/purchase-view/{id}', [PurchaseController::class, 'getPurchaseDetails'])->name('purchase.viewData');
        // Route::get('/purchase/invoice/pdf/{id}', [PurchaseController::class, 'purchase_invoice_pdf'])->name('purchase.invoice.pdf');

        Route::get('/purchase-list', [PurchaseController::class, 'purchase_list'])->name('purchase.lists');
        Route::get('/add-purchase', [PurchaseController::class, 'add_purchase'])->name('purchase.add');
        Route::get('/edit-purchase/{num}', [PurchaseController::class, 'edit_purchase'])->name('purchase.edit');
        Route::get('/purchase-invoice', [PurchaseController::class, 'purchase_invoice'])->name('purchase.invoice');
        Route::get('/print-purchase/{id}', [PurchaseController::class, 'printPurchase'])->name('purchase.print');
        Route::get('/purchase-order-report', [PurchaseController::class, 'purchase_order_report'])->name('purchase.orderreport');
        Route::get('/import-purchase', [PurchaseController::class, 'import_purchase'])->name('purchase.import');
        Route::get('/purchase-report', [PurchaseController::class, 'purchase_report'])->name('purchase.report');
        // Route::get('/purchase-details', [PurchaseController::class, 'purchase_details'])->name('purchase.details');
        Route::get('/purchase-details', [PurchaseController::class, 'purchaseDetails'])->name('purchase.details');
        Route::get('/purchase-view/{id}', [PurchaseController::class, 'getPurchaseDetails'])->name('purchase.viewData');
        Route::get('/purchase/invoice/pdf/{id}', [PurchaseController::class, 'purchase_invoice_pdf'])->name('purchase.invoice.pdf');

        // Route::get('/purchases/pdf', [PurchaseController::class, 'purchases_report']);
        // Custom Invoice
        Route::get('/custom-invoice-list', [CustomInvoiceController::class, 'custom_invoice_list'])->name('custom_invoice.lists');
        Route::get('/add-custom-invoice', [CustomInvoiceController::class, 'add_custom_invoice'])->name('custom_invoice.add');
        Route::get('/edit-custom-invoice/{num}', [CustomInvoiceController::class, 'edit_custom_invoice'])->name('custom_invoice.edit');
        Route::get('/custom-invoice-print/{num}', [CustomInvoiceController::class, 'custom_invoice_print'])->name('custom_invoice.print');
        Route::get('/invoice-view/{id}', [CustomInvoiceController::class, 'custom_invoice_view'])->name('custom_invoice.view');
        Route::get('/custom-invoice/pdf/{id}', [CustomInvoiceController::class, 'custom_invoice_pdf'])->name('custom_invoice.pdf');
        // Purchase Reports
        Route::get('/purchases/report/{ids}', [PurchaseController::class, 'purchases_report']);

        // Sales Reports
        Route::get('/sales/report/{ids}', [SalesController::class, 'sale_report']);

        //Purchase Return
        Route::get('admin/get-vendor-name/{invoiceId}', [PurchaseReturnController::class, 'getVendorName']);
        Route::get('admin/get-invoice-products/{invoiceId}', [PurchaseReturnController::class, 'getInvoiceProducts']);
        Route::post('/admin/update-quantity', [PurchaseReturnController::class, 'updateQuantity']);

        //purchasereturn
        Route::get('/create-purchase-return', [PurchaseReturnController::class, 'create_purchase_return'])->name('purchasereturn.add');
        Route::get('/edit-purchase-return', [PurchaseReturnController::class, 'edit_purchase_return'])->name('purchase.edit');
        Route::get('/purchase-return-list', [PurchaseReturnController::class, 'purchase_return_list'])->name('purchasereturn.list');
        //expense
        Route::get('/create-expense', [ExpenseController::class, 'create_expense'])->name('expense.add');
        // Route::get('/edit-purchase/{num}', [PurchaseController::class, 'edit_purchase'])->name('purchase.edit');
        Route::get('/edit-expense/{num}', [ExpenseController::class, 'edit_expense'])->name('expense.edit');
        Route::get('/expense-category', [ExpenseController::class, 'expense_category'])->name('expense.category');
        Route::get('/expense-list', [ExpenseController::class, 'expense_list'])->name('expense.list');
        Route::get('/expense-report', [ExpenseController::class, 'expense_report'])->name('expense.report');
        Route::get('/expense/report/{ids}', [ExpenseController::class, 'expense_report_view']);

        // Expense Type
        Route::get('/create-expense-type', [ExpenseTypeController::class, 'create_expense_type'])->name('expensetype.add');
        Route::get('/expense-type-list', [ExpenseTypeController::class, 'expense_type_list'])->name('expensetype.list');
        Route::get('/expense-type/edit/{id}', [ExpenseTypeController::class, 'edit'])->name('expensetype.edit');
        Route::get('/edit-expense-type/{num}', [ExpenseTypeController::class, 'edit_expense_type'])->name('expensetype.edit');

        //invoice
        Route::get('/inventory-report', [InvoiceController::class, 'inventory_report'])->name('inventory.report');
        Route::get('/invoice-report', [InvoiceController::class, 'invoice_report'])->name('invoice.report');

        // Salary
        Route::get('/salary-list', [SalaryController::class, 'List'])->name('salary.list');
        Route::get('/salary/view', [SalaryController::class, 'viewList'])->name('salary.view');

        Route::get('/custom-invoice/{id}', [CustomInvoiceController::class, 'show'])->name('custom-invoice.view');
        // Route::post('/get_user_data', [SalesController::class, 'get_user_data'])->name('sales.get_user_data');

        Route::get('/staff', [StaffController::class, 'staff_list'])->name('staff.list');
        Route::get('/add-staff', [StaffController::class, 'add_staff'])->name('staff.add');
        Route::get('/edit-staff/{num}', [StaffController::class, 'edit_staff'])->name('staff.edit');
        Route::get('/staff-report', [StaffController::class, 'staff_report'])->name('staff.report');
        Route::get('/staff-view/{id}', [StaffController::class, 'staff_view'])->name('staff.view');

        // Sub Branch
        Route::get('/sub-branch', [SubBranchController::class, 'subbranch_list'])->name('subbranch.list');
        Route::get('/add-subbranch', [SubBranchController::class, 'add_subbranch'])->name('subbranch.add');
        Route::get('/edit-subbranch/{id}', [SubBranchController::class, 'edit_subbranch'])->name('subbranch.edit');
        Route::get('/delete-subbranch/{id}', [SubBranchController::class, 'delete_subbranch'])->name('subbranch.delete');
        Route::get('/view-subbranch/{id}', [SubBranchController::class, 'view_subbranch'])->name('subbranch.view');

        Route::get('/job-cards', [JobCardController::class, 'index'])->name('job_card.index');
        Route::get('/job-cards/create', [JobCardController::class, 'create'])->name('job_card.create');
        Route::post('/job-cards/store', [JobCardController::class, 'store'])->name('job_card.store');
        Route::get('/job-cards/{id}', [JobCardController::class, 'show'])->name('job_card.show');
        Route::get('/job-cards/{id}/edit', [JobCardController::class, 'edit'])->name('job_card.edit');
        Route::put('/job-cards/{id}/update', [JobCardController::class, 'update'])->name('job_card.update');
        Route::delete('/job-cards/{id}/delete', [JobCardController::class, 'destroy'])->name('job_card.destroy');
        Route::get('/get-history', [JobCardController::class, 'get_history'])->name('job_card.get-history');
        Route::get('/job-card/{id}/view-pdf', [JobCardController::class, 'viewPdf'])->name('job_card.view_pdf');
        Route::get('/job-card/{id}/download-pdf', [JobCardController::class, 'downloadPdf'])->name('job_card.download_pdf');
        Route::get('/job-card/{id}/download-pdf-view', [JobCardController::class, 'downloadPdf1'])->name('job_card.download_pdf_view');
        Route::get('/job-cards/{id}/convert', [JobCardController::class, 'convertToJobCard'])->name('job_card.convert');
        Route::post('/job-card/make-payment', [JobCardController::class, 'makePaymentSubmit'])->name('job_card.make_payment_submit');
        Route::post('/purchase/make-payment', [PurchaseController::class, 'makePaymentSubmit'])->name('purchase.make_payment_submit');
        Route::get('/filter-jobcards', [JobCardController::class, 'filter'])->name('job_card.filter');
        Route::get('/job-card/search-vehicle', [JobCardController::class, 'searchAndGetVehicle'])->name('job_card.search_vehicle');
        Route::get('/job-card/payment-history/{id}', [JobCardController::class, 'getHistory'])->name('job_card.payment_history');
        Route::get('/purchase/payment-history/{id}', [PurchaseController::class, 'getHistory'])->name('purchase.payment_history');
        Route::get('/sales/payment-history/{id}', [SalesController::class, 'getHistory1'])->name('sales.payment_history');

        Route::get('/pdf-orders', [SalesController::class, 'exportOrdersPDF'])->name('pdf.export');
        Route::get('/pdf-purchase', [PurchaseController::class, 'exportPurchasePDF'])->name('purchase.pdf.export');

        Route::get('/get-parts', [JobCardController::class, 'getParts'])->name('get.parts');

        Route::get('/daily-data', [DailyDataController::class, 'index'])->name('daily_data.index');
        Route::get('/daily-data/create', [DailyDataController::class, 'create'])->name('daily_data.add-daily-data');
        // Route::get('/daily-data/{id}', [DailyDataController::class, 'show'])->name('daily_data.show');
        Route::get('/jobcards/export', [DailyDataController::class, 'downloadCSV'])->name('jobcards.export');
        Route::get('/daily-data/filter', [DailyDataController::class, 'filter'])->name('daily_data.filter');
        Route::get('/daily-data/download', [DailyDataController::class, 'downloaddailydataReport'])->name('daily-data.downloadReport');

        Route::get('/advance_pay', [AdvancePaymentController::class, 'index'])->name('advance_pay.index');
        Route::get('/advance_pay/create', [AdvancePaymentController::class, 'create'])->name('advance_pay.create');
        Route::post('/advance_pay/store', [AdvancePaymentController::class, 'store'])->name('advance_pay.store');
        Route::get('/advance_pay/{id}', [AdvancePaymentController::class, 'show'])->name('advance_pay.show');
        Route::get('/advance_pay/{id}/edit', [AdvancePaymentController::class, 'edit'])->name('advance_pay.edit');
        Route::put('/advance_pay/{id}/update', [AdvancePaymentController::class, 'update'])->name('advance_pay.update');
        Route::delete('/advance_pay/{id}/delete', [AdvancePaymentController::class, 'destroy'])->name('advance_pay.destroy');

        Route::get('/export-orders', [SalesController::class, 'exportOrders'])->name('orders.export');
        Route::get('/export-purchase', [PurchaseController::class, 'exportPurchase'])->name('purchase.export');
        Route::get('/paid-payments', [AccountLedgerController::class, 'paid_payments'])->name('paid-payments');

        Route::get('/gst-sales-report/gstr3b-export', [ApiGstSalesReportController::class, 'exportGstr3b'])->name('gst.sales.report.gstr3b.export');
        Route::get('/gst/reports/sales-list', [GstSalesReportController::class, 'index'])->name('gst.sales_list');

        // credit note items
        Route::get('/credit-note-items', [CreditNoteItemController::class, 'index'])->name('credit-notes-items.index');
        Route::get('/credit-note-items/create', [CreditNoteItemController::class, 'create'])->name('credit-notes-items.create');
        Route::get('/view-credit-note-items/{id}', [CreditNoteItemController::class, 'show'])->name('credit-notes-items.show');
        Route::get('/edit-credit-note-items/{id}', [CreditNoteItemController::class, 'edit'])->name('credit-notes-items.edit');
        Route::post('/update-credit-note-items/{id}', [CreditNoteItemController::class, 'update'])->name('credit-notes-items.update');

        // credit notes type
        Route::get('/credit-notes', [CreditNotesTypeController::class, 'creditnotes_list'])->name('credit-notes.index');
        Route::get('credit-notes/create', [CreditNotesTypeController::class, 'create'])->name('credit-notes.create');
        Route::get('/edit-credit-notes/{id}', [CreditNotesTypeController::class, 'edit'])->name('credit-notes.edit');

        // Debit notes item type
        Route::get('/debit-note-items', [DebitNoteItemController::class, 'index'])->name('debit-notes-items.index');
        Route::get('/debit-note-items/create', [DebitNoteItemController::class, 'create'])->name('debit-notes-items.create');
        Route::get('/debit-note-items/edit/{id}', [DebitNoteItemController::class, 'edit'])->name('debit-notes-items.edit');
        Route::get('/debit-note-items/view/{id}', [DebitNoteItemController::class, 'show'])->name('debit-notes-items.show');
        Route::post('/debit-note-items/store', [DebitNoteItemController::class, 'store'])->name('debit-notes-items.store');


        // Converted attendance module from legacy CodeIgniter screens
        Route::prefix('staff/attendance')->name('attendence.')->group(function () {
            Route::get('/summary', [AttendanceeController::class, 'display'])->name('summary');
            Route::get('/list', [AttendanceeController::class, 'view'])->name('list');
            Route::get('/calendar', [AttendanceeController::class, 'viewCalendar'])->name('calendar');
        });

        // Backward-compatible URLs used inside the legacy attendance UI
        Route::get('/attendence', [AttendanceeController::class, 'display'])->name('attendence.manage');
        Route::get('/view', [AttendanceeController::class, 'view'])->name('attendence.view');
        Route::get('/view-calendar', [AttendanceeController::class, 'viewCalendar'])->name('attendence.view_calendar');

        // Staff self-service attendance actions
        Route::get('/staff/check-status', [AttendanceeController::class, 'staffCheckStatus'])->name('staff.checkstatus');
        Route::post('/staff/check-in', [AttendanceeController::class, 'staffCheckIn'])->name('staff.checkin');
        Route::post('/staff/check-out', [AttendanceeController::class, 'staffCheckOut'])->name('staff.checkout');


        // Payroll pages
        Route::prefix('payroll')->name('payroll.')->group(function () {
            Route::get('/', [ApiPayrollController::class, 'page'])->name('create');
            Route::get('/{id}', [ApiPayrollController::class, 'page'])->whereNumber('id')->name('edit');
            Route::get('/salary', [ApiPayrollController::class, 'groupsalaryPage'])->name('salary');
            Route::get('/salary-details', [ApiPayrollController::class, 'salaryDetails'])->name('salary-details');
            Route::get('/profile/{id}', [ApiPayrollController::class, 'profilePage'])->whereNumber('id')->name('profile');
            Route::get('/details/{id}', [ApiPayrollController::class, 'getProfile'])->whereNumber('id')->name('details');
            Route::get('/download-slip/{id}', [ApiPayrollController::class, 'downloadSlip'])->whereNumber('id')->name('download-slip');
        });
        Route::get('/payrollview', [ApiPayrollController::class, 'display'])->name('payroll.list');


        // Department pages
        Route::get('/department', [DepartmentController::class, 'createPage'])->name('department.create');
        Route::get('/department/{id}', [DepartmentController::class, 'createPage'])->name('department.edit');
        Route::get('/departmentview', [DepartmentController::class, 'indexPage'])->name('department.view');

        // Designation pages
        Route::get('/designation', [DesignationController::class, 'createPage'])->name('designation.create');
        Route::post('/designation/edit', [DesignationController::class, 'openEditPage'])->name('designation.edit.open');
        Route::get('/designationview', [DesignationController::class, 'indexPage'])->name('designation.view');

        // Leave type pages
        Route::get('/leave_type', [ApiLeaveTypeController::class, 'createPage'])->name('leave-type.create');
        Route::post('/leave_type/edit', [ApiLeaveTypeController::class, 'openEditPage'])->name('leave-type.edit.open');
        Route::get('/leavetypeview', [ApiLeaveTypeController::class, 'indexPage'])->name('leave-type.view');
        Route::get('/leaveview', [LeaveController::class, 'indexPage'])->name('leave.view');
        Route::get('/leave-request', [LeaveController::class, 'leaveRequest'])->name('leave.request');
        Route::post('/leave-request/{leaveId}/status', [LeaveController::class, 'updateStatusWeb'])->name('leave.request.status');
        Route::get('/leaveviews', [LeaveController::class, 'legacyPage'])->name('leave.views');
        Route::get('/addleave', [LeaveController::class, 'createPage'])->name('leave.add');

         //plans
    Route::get('/plan', [PlanController::class,'index'])->name('plans.planlist');
    Route::get('/add-plan', [PlanController::class,'create'])->name('plans.addplan');
    Route::post('/add-plan', [PlanController::class,'store'])->name('plans.store');
    Route::get('/edit-plan/{plan}', [PlanController::class,'edit'])->name('plans.edit');
    Route::put('/edit-plan/{plan}', [PlanController::class,'update'])->name('plans.update');
    Route::delete('/delete-plan/{plan}', [PlanController::class,'destroy'])->name('plans.destroy');
    Route::get('/my-plan', [PlanController::class,'myplan'])->name('plans.myplan');
    Route::get('/plan-details', [PlanController::class, 'myPlanDetails'])->name('plans.details');
    });
});

