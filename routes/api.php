<?php

use App\Http\Controllers\admin\JobCardController;
use App\Http\Controllers\api\AccountLedgerController;
use App\Http\Controllers\api\AdvancePaymentController;
use App\Http\Controllers\api\AppointmentController;
use App\Http\Controllers\api\AttendanceController;
use App\Http\Controllers\api\AutoCarCategoryController;
use App\Http\Controllers\api\AutoCarModelController;
use App\Http\Controllers\api\BrandController;
use App\Http\Controllers\api\CategoryController;
use App\Http\Controllers\api\CreditNoteItemApiController;
use App\Http\Controllers\api\CreditNotesTypeController;
use App\Http\Controllers\api\CurrencyController;
use App\Http\Controllers\api\CustomerController;
use App\Http\Controllers\api\CustomInvoiceController;
use App\Http\Controllers\api\DailyDataController;
use App\Http\Controllers\api\DebitNoteItemApiController;
use App\Http\Controllers\api\ExpenseController;
use App\Http\Controllers\api\ExpenseTypeController;
use App\Http\Controllers\api\GeneralSettingController;
use App\Http\Controllers\api\GstController;
use App\Http\Controllers\api\GSTR_1Controller;
use App\Http\Controllers\api\GstSalesReportController;
use App\Http\Controllers\api\InventoryController;
use App\Http\Controllers\api\JobCardConroller;
use App\Http\Controllers\api\LabourServiceController;
use App\Http\Controllers\api\LoginController;
use App\Http\Controllers\api\ProductController;
use App\Http\Controllers\api\ProfileController;
use App\Http\Controllers\api\PurchaseController;
use App\Http\Controllers\api\SalaryController;
use App\Http\Controllers\api\SaleReturnController;
use App\Http\Controllers\api\SalesController;
use App\Http\Controllers\api\StaffController;
use App\Http\Controllers\api\SubBranchController;
use App\Http\Controllers\api\SupplierController;
use App\Http\Controllers\api\TaxRateController;
use App\Http\Controllers\api\UnitController;
use App\Http\Controllers\api\LeaveTypeController;
use App\Http\Controllers\api\PayrollController;
use App\Http\Controllers\admin\LeaveController as AdminLeaveController;
use App\Http\Controllers\admin\AttendanceeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use App\Http\Controllers\admin\DesignationController as AdminDesignationController;
use App\Http\Controllers\admin\DepartmentController as AdminDepartmentController;
use App\Http\Controllers\api\FaceLoginController;
use App\Http\Controllers\api\PlanController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/oauth/token', [AccessTokenController::class, 'issueToken'])
    ->middleware(['throttle', 'passport.client']);

Route::get('/job-card/get-next-number', [JobCardController::class, 'getNextNumber'])->name('api.job_card.get_next_number');

Route::post('login', [LoginController::class, 'login'])->name('login');

// Face recognition login routes (public - no auth token required)
Route::post('/face-login', [FaceLoginController::class, 'login'])->name('api.face.login');
Route::get('/face-login-data', [FaceLoginController::class, 'getFaceData'])->name('api.face.login.data');

Route::middleware(['auth.api'])->group(function () {

    //advance pay

    Route::apiResource('advance-payments', AdvancePaymentController::class);
// routes/api.php
    Route::get('/advance-payment-filters', [AdvancePaymentController::class, 'getFilters']);
    Route::get('/export-advance-payments', [AdvancePaymentController::class, 'exportAdvancePayments'])
        ->name('export.advance-payments');
    Route::get('/advance-history', [SalaryController::class, 'getAdvanceHistory']);

    // convert quatation into jobcard
    Route::post('/convert-quotation-to-jobcard/{id}', [ProductController::class, 'convertQuotationToJobCard']);

    Route::get('logout', [LoginController::class, 'logoutapi'])->name('logout');
    Route::get('getAllProduct', [ProductController::class, 'getAllProduct'])->name('getAllProduct');
    Route::post('createProduct', [ProductController::class, 'createProduct'])->name('createProduct');
    Route::post('createProduct', [ProductController::class, 'createProduct'])->name('createProduct');
    Route::post('updateProduct', [ProductController::class, 'updateProduct'])->name('updateProduct');
    Route::post('deleteProduct/{id}', [ProductController::class, 'deleteProduct'])->name('deleteProduct');
    Route::post('importProducts', [ProductController::class, 'importProducts'])->name('importProducts');
    Route::post('remove-product-image', [ProductController::class, 'removeProductImage'])->name('removeProductImage');
    Route::get('/product-quantity-history/{productId}', [ProductController::class, 'getQuantityHistory']);
    Route::get('/export-product', [ProductController::class, 'export_product']);
    Route::get('/export-product-pdf', [ProductController::class, 'export_product_pdf']);

    Route::get('/order-report', [SalesController::class, 'orderReport']);
    Route::get('/getAllJobCards', [JobCardConroller::class, 'getAllJobCards'])->name('getAllJobCards');

    Route::get('/leavetype', [LeaveTypeController::class, 'index']);
    Route::get('/leavetype/{id}', [LeaveTypeController::class, 'show'])->whereNumber('id');
    Route::post('/leavetype', [LeaveTypeController::class, 'store']);
    Route::post('/leavetype/add', [LeaveTypeController::class, 'store']);
    Route::post('/leavetype/{id}', [LeaveTypeController::class, 'update'])->whereNumber('id');
    Route::delete('/leavetype/{id}', [LeaveTypeController::class, 'destroy'])->whereNumber('id');

    Route::get('getProfile', [ProfileController::class, 'getProfile'])->name('getProfile');
    Route::post('updateProfile', [ProfileController::class, 'updateProfile'])->name('updateProfile');
    Route::get('get_subadmin', [ProfileController::class, 'get_subadmin'])->name('get_subadmin');
    Route::get('getProductById/{num:}', [ProductController::class, 'getProductById'])->name('getProductById');

    Route::post('createCustomer', [CustomerController::class, 'createCustomer'])->name('createCustomer');
    Route::post('deleteCustomer/{num:}', [CustomerController::class, 'deleteCustomer'])->name('deleteCustomer');
    Route::get('/getCustomer/{id}', [CustomerController::class, 'getCustomer'])->name('getCustomer');
    Route::post('/updateCustomer/{id}', [CustomerController::class, 'updateCustomer'])->name('updateCustomer');
    Route::get('getAllCustomer', [CustomerController::class, 'getAllCustomer'])->name('getAllCustomer');
    // Route::get('/customer-view/{id}/data', [CustomerController::class, 'getCustomerData']);
    Route::get('customer/{id}', [CustomerController::class, 'getCustomerProfile']);

    Route::post('/fetch-gst-details', [SupplierController::class, 'fetch'])->name('gst.fetch');
    Route::post('createSupplier', [SupplierController::class, 'createSupplier'])->name('createSupplier');
    Route::post('deleteSupplier/{num:}', [SupplierController::class, 'deleteSupplier'])->name('deleteSupplier');
    Route::get('/getSupplier/{id}', [SupplierController::class, 'getSupplier'])->name('getSupplier');
    Route::post('/updateSupplier/{id}', [SupplierController::class, 'updateSupplier'])->name('updateSupplier');
    Route::get('getAllSupplier', [SupplierController::class, 'getAllSupplier'])->name('getAllSupplier');
    Route::get('vendor/{id}', [SupplierController::class, 'getVendorProfile']);

    Route::get('getAllCategory', [CategoryController::class, 'getAllCategory'])->name('getAllCategory');
    Route::post('addcategory_cat', [CategoryController::class, 'addcategory_cat'])->name('addcategory_cat');
    Route::post('deleteCategory/{id}', [CategoryController::class, 'deleteCategory'])->name('deleteCategory');
    Route::post('updatecategory', [CategoryController::class, 'updatecategory'])->name('updatecategory');

    // Labour Services API Routes
    Route::get('/getAllServices', [LabourServiceController::class, 'getAllServices']);
    Route::post('/addservice', [LabourServiceController::class, 'addService']);
    Route::post('/updateservice', [LabourServiceController::class, 'updateService']);
    Route::delete('/deleteservice/{id}', [LabourServiceController::class, 'deleteService']);
    Route::get('/getService/{id}', [LabourServiceController::class, 'getService']);

    // Autocar Categorys
    Route::get('/getAllCategories', [AutoCarCategoryController::class, 'getAllCategories']);
    Route::post('/addcategory', [AutoCarCategoryController::class, 'addCategory']);
    Route::post('/autocar/updatecategory', [AutoCarCategoryController::class, 'updateCategory']);
    Route::delete('/deletecategory/{id}', [AutoCarCategoryController::class, 'deleteCategory']);

    Route::get('/getAllModels', [AutoCarModelController::class, 'getAllModels']);
    Route::post('/addmodel', [AutoCarModelController::class, 'addModel']);
    Route::post('/updatemodel', [AutoCarModelController::class, 'updateModel']);
    Route::delete('/deletemodel/{id}', [AutoCarModelController::class, 'deleteModel']);

    // Salary
    Route::get('/salaries', [SalaryController::class, 'index']);
    Route::post('/salaries/pay', [SalaryController::class, 'store']);
    Route::put('/salaries/update/{id}', [SalaryController::class, 'update']);
    Route::get('/salaries/years', [SalaryController::class, 'getSalaryYears']);
    Route::get('/staff', [SalaryController::class, 'getActiveStaff']);
    Route::get('/salaries/export', [SalaryController::class, 'export']);
    Route::get('/salaries/pdf', [SalaryController::class, 'generatePDF']);
    // Route::get('/salaries/staff/pdf', [SalaryController::class, 'generateStaffPDF']);
    Route::get('/salaries/staff/pdf', [SalaryController::class, 'generateStaffPDF'])->name('salary.staff_pdf');

    Route::get('/inventory-list', [InventoryController::class, 'index'])->name('inventory.list');
    Route::get('/products_edit_inventroy/{id}', [InventoryController::class, 'products_edit_inventroy'])->name('inventory.edit');
    Route::post('/inventory_update/{id}', [InventoryController::class, 'inventory_update'])->name('inventory.update');
    Route::get('/getQuantityHistory_inventory/{id}', [InventoryController::class, 'getQuantityHistory_inventory'])->name('inventory.getQuantityHistory_inventory');
    Route::get('/products/filter', [InventoryController::class, 'filter']);

    Route::get('getAllBrand', [BrandController::class, 'getAllBrand'])->name('getAllBrand');
    Route::post('addBrand', [BrandController::class, 'addBrand'])->name('addBrand');

    Route::get('/tax-rates', [TaxRateController::class, 'index'])->name('tax-rates.index');
    Route::post('/tax-rates/store', [TaxRateController::class, 'store'])->name('tax-rates.store');
    Route::get('/tax-rates/{id}', [TaxRateController::class, 'edit'])->name('tax-rates.edit');
    Route::post('/tax-rates/update/{id}', [TaxRateController::class, 'update'])->name('tax-rates.update');
    Route::post('/tax-rates/delete/{id}', [TaxRateController::class, 'destroy'])->name('tax-rates.destroy');

    Route::get('/currency', [CurrencyController::class, 'index'])->name('currency.index');
    Route::post('/currency/store', [TaxRateController::class, 'store'])->name('currency.store');
    Route::get('/currency/{id}', [TaxRateController::class, 'edit'])->name('currency.edit');
    Route::post('/currency/update/{id}', [TaxRateController::class, 'update'])->name('currency.update');
    Route::post('/currency/delete/{id}', [TaxRateController::class, 'destroy'])->name('currency.destroy');

    Route::get('/general-settings', [GeneralSettingController::class, 'show'])->name('general-settings.show');
    Route::post('/general-settings/update', [GeneralSettingController::class, 'update'])->name('general-settings.update');
    Route::post('/company-rules/update', [GeneralSettingController::class, 'updateCompanyRules'])->name('company-rules.update');
    Route::post('/dashboard-settings/update', [GeneralSettingController::class, 'updateDashboardSettings'])->name('general-dashboard-settings.update');

    // Route::post('/purchase_order', [PurchaseController::class, 'purchase_order'])->name('purchase_order');
    // Route::post('/purchase_order1', [PurchaseController::class, 'purchase_order1'])->name('purchase_order1');
    // Route::get('/purchase_list', [PurchaseController::class, 'purchase_list'])->name('purchase_list');
    // Route::get('/purchase_list1', [PurchaseController::class, 'purchase_list1'])->name('purchase_list1');
    // Route::post('/purchase_delete', [PurchaseController::class, 'purchas~~e_delete'])->name('purchase_delete');
    // Route::post('/purchase_delete_auto', [PurchaseController::class, 'purchase_delete1'])->name('purchase_delete1');
    // Route::get('/purchase_get/{id}', [PurchaseController::class, 'showPurchase'])->name('purchase_get');
    // Route::post('/purchase_update/{id}', [PurchaseController::class, 'purchase_update'])->name('purchase_update');
    // Route::get('/purchase-report-data', [PurchaseController::class, 'fetch_purchase_report']);

    Route::post('/purchase_order', [PurchaseController::class, 'purchase_order'])->name('purchase_order');
    Route::get('/purchase_list', [PurchaseController::class, 'purchase_list'])->name('purchase_list');
    Route::post('/purchase_delete', [PurchaseController::class, 'purchase_delete'])->name('purchase_delete');
    Route::get('/purchase_get/{id}', [PurchaseController::class, 'showPurchase'])->name('purchase_get');
    Route::post('/purchase_update/{id}', [PurchaseController::class, 'purchase_update'])->name('purchase_update');
    Route::get('/purchase-report-data', [PurchaseController::class, 'fetch_purchase_report']);
    Route::post('/make-payment-purchase', [PurchaseController::class, 'make_payment'])->name('make-payment.purchase');
    Route::get('/purchase/payment-history/{id}', [PurchaseController::class, 'getHistory1'])->name('purchase.payment_history');
    Route::post('/purchase-export-excel', [PurchaseController::class, 'export_purchase_excel']);
    Route::get('/export-purchase', [PurchaseController::class, 'export_purchase']);
    Route::get('/export-purchase-pdf', [PurchaseController::class, 'export_purchase_pdf']);
    Route::post('/purchases/report/export-pdf-api', [PurchaseController::class, 'export_purchases_report_pdf_api'])->name('purchase.reportapi.exportPdf');

    Route::post('deleteBrand/{id}', [BrandController::class, 'deleteBrand'])->name('deleteBrand');
    Route::post('updateBrand', [BrandController::class, 'updateBrand'])->name('updateBrand');

    Route::get('getProductsByCategory/{id}', [SalesController::class, 'getProductsByCategory'])->name('getProductsByCategory');

    Route::get('/product-view/{id}', [ProductController::class, 'getProductDetails']);

    Route::post('order_sale', [SalesController::class, 'order_sale'])->name('order_sale');
    Route::get('get_orders', [SalesController::class, 'get_orders'])->name('get_orders');
    Route::get('sales/{selectedSubAdminId?}', [SalesController::class, 'get_orders'])->name('api.get_orders');

    Route::get('getsalseById/{id}', [SalesController::class, 'getsalseById'])->name('getsalseById');
    Route::post('delete/{id}', [SalesController::class, 'delete'])->name('delete');
    Route::post('update_sale', [SalesController::class, 'update_sale'])->name('update_sale');
    Route::get('/orders/filter', [SalesController::class, 'getFilteredOrders']);

    Route::post('/expenses/store', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::get('/expenses/list', [ExpenseController::class, 'getExpenses'])->name('expenses.list');
    Route::post('/expenses/{id}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');
    Route::get('/expenses/{id}/edit', [ExpenseController::class, 'edit'])->name('expenses.edit');
    Route::put('/expenses/{id}', [ExpenseController::class, 'update'])->name('expenses.update');
    Route::get('/expenses/report', [ExpenseController::class, 'getExpensesReport']);

    // Expense-Type
    Route::post('/expense-type/store', [ExpenseTypeController::class, 'store'])->name('expense-type.store');
    Route::get('/expense-types', [ExpenseTypeController::class, 'list']);
    Route::get('/expense-types/{id}', [ExpenseTypeController::class, 'show']);
    Route::put('/expense-types/{id}', [ExpenseTypeController::class, 'update']);
    Route::post('/expenses-type/{id}', [ExpenseTypeController::class, 'destroy'])->name('expenses-type.destroy');

    Route::get('/getSaleDetails/{id}', [SaleReturnController::class, 'getSaleDetails'])->name('getSaleDetails');
    Route::post('/return_sale', [SaleReturnController::class, 'return_sale'])->name('return_sale');

    Route::post('/customer_invoice/store', [CustomInvoiceController::class, 'store'])->name('/customer_invoice/store');
    Route::get('/custom_invoice_list', [CustomInvoiceController::class, 'list'])->name('/customer_invoice/list');
    Route::post('/custom_invoice_delete', [CustomInvoiceController::class, 'delete'])->name('/customer_invoice/delete');
    Route::get('/edit-invoice/{id}', [CustomInvoiceController::class, 'getInvoiceData']);

    Route::get('/custom_invoice_get/{id}', [CustomInvoiceController::class, 'get'])->name('/customer_invoice/get');
    Route::post('/custom_invoice_update/{id}', [CustomInvoiceController::class, 'update'])->name('/customer_invoice/update');

    Route::post('createStaff', [StaffController::class, 'createStaff'])->name('createCustomer');
    Route::post('deleteStaff/{num:}', [StaffController::class, 'deleteStaff'])->name('deleteCustomer');
    Route::get('/getStaff/{id}', [StaffController::class, 'getStaff'])->name('getCustomer');
    Route::post('/updateStaff/{id}', [StaffController::class, 'updateStaff'])->name('updateCustomer');
    Route::get('getAllStaff', [StaffController::class, 'getAllStaff'])->name('getAllCustomer');
    // Route::get('/customer-view/{id}/data', [CustomerController::class, 'getCustomerData']);
    Route::get('staff/{id}', [StaffController::class, 'getStaffProfile']);

    Route::post('createSubbranch', [SubBranchController::class, 'createSubbranch'])->name('createSubbranch');
    Route::post('deleteSubbranch/{num:}', [SubBranchController::class, 'deleteSubbranch'])->name('deleteSubbranch');
    Route::get('/getSubbranch/{id}', [SubBranchController::class, 'getSubbranch'])->name('getSubbranch');
    Route::post('/updateSubbranch', [SubBranchController::class, 'updateSubbranch'])->name('updateSubbranch');
    Route::get('getAllSubbranch', [SubBranchController::class, 'getAllSubbranch'])->name('getAllSubbranch');
    // Route::get('/customer-view/{id}/data', [CustomerController::class, 'getCustomerData']);
    Route::get('subbranch/{id}', [SubBranchController::class, 'getSubbranchProfile']);

    Route::get('/appointments', [AppointmentController::class, 'getAllAppointments']);
    Route::post('/appointments', [AppointmentController::class, 'store']);
    Route::get('/appointments/{id}', [AppointmentController::class, 'show']);
    Route::post('/appointments/{id}', [AppointmentController::class, 'update']);
    Route::delete('/appointments/{id}', [AppointmentController::class, 'destroy']);
    Route::get('/export-appointments', [AppointmentController::class, 'exportAppointments']);

    Route::post('/attendance/store', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::post('/attendance/bulk-store', [AttendanceController::class, 'bulkStore'])->name('attendance.bulk-store');
    Route::get('/attendance/filter', [AttendanceController::class, 'filter'])->name('attendance.filter');

    Route::post('/jobcard/{id}/make-payment', [DailyDataController::class, 'makePayment'])->name('jobcard.makePayment');
    Route::get('/jobcard/{id}/payment-history', [DailyDataController::class, 'getPaymentHistory'])->name('jobcard.paymentHistory');
    Route::post('/payment-history/{id}/update', [DailyDataController::class, 'updatePaymentHistory'])->name('payment_history.update');
    Route::delete('/payment-history/{id}/delete', [DailyDataController::class, 'deletePaymentHistory'])->name('payment_history.delete');
    Route::get('/jobcards-get', [DailyDataController::class, 'jobcards_get'])->name('jobcard.jobcards_get');
    Route::get('/account-ledger', [AccountLedgerController::class, 'getAccountLedger'])->name('account_ledger.list');
    Route::get('/get-payment-details', [AccountLedgerController::class, 'getPaymentDetails'])->name('get.payment.details');
    Route::get('/paid-payment-all', [AccountLedgerController::class, 'paid_payment_all'])->name('paid-payment-all');
    Route::get('/get-payment-pending', [AccountLedgerController::class, 'getPendingPaymentDetails'])->name('get.payment.pending');
    Route::get('/export-account-ledger', [AccountLedgerController::class, 'exportAccountLedgerExcel']);
    Route::post('/account-ledger/download-pdf', [AccountLedgerController::class, 'downloadAccountLedgerPDF']);

    Route::post('/createdailydata', [DailyDataController::class, 'add_daily_data'])->name('daily_data.store');
    Route::get('/daily-data/ajax', [DailyDataController::class, 'apiIndex']);
    // Route::get('/daily-data/paginated', [DailyDataController::class, 'paginatedIndex']);
    Route::get('/jobcard-details/{id}', [DailyDataController::class, 'jobcard_details'])->name('daily_data.jobcard_details');
    Route::delete('/daily-data/{id}/delete', [DailyDataController::class, 'destroy'])->name('daily_data.destroy');
    Route::post('/jobcards/update', [DailyDataController::class, 'update']);
    Route::get('/get-top-technicians', [DailyDataController::class, 'getTopTechnicians']);

    Route::get('/parts', [DailyDataController::class, 'getAllParts']);

    Route::get('/orders/filter/sales', [SalesController::class, 'getFilteredOrders']);

    // unit
    Route::post('/add-units', [UnitController::class, 'store'])->name('units.store');
    Route::get('/units', [UnitController::class, 'index'])->name('units.index');
    Route::post('/update-unit/{id}', [UnitController::class, 'update'])->name('units.update');
    Route::delete('/delete-unit/{id}', [UnitController::class, 'destroy'])->name('units.destroy');

    Route::get('/get-category', [ProductController::class, 'getCategory']);
    Route::get('/get-brand', [ProductController::class, 'getBrand']);
    Route::get('/get-tax-rates', [ProductController::class, 'getTaxRates']);
    Route::get('/edit_product/{id}', [ProductController::class, 'edit_product']);
    Route::get('/get-units', [ProductController::class, 'getUnits']);   

    Route::get('/gstr1/export-excel', [GSTR_1Controller::class, 'exportExcel']);
    // Route::get('/gstr2b/export-excel', [GstSalesReportController::class, 'exportExcel']);
    Route::get('/gstr3b/export-excel', [GstSalesReportController::class, 'exportGstr3b']);
    Route::get('/gstr3b/export', [GstSalesReportController::class, 'exportGstr3b']);
    Route::get('/gst/gstr-3b/export', [GstController::class, 'exportGstr3b'])->name('gst.gstr3b.export');

     // credit notes type
    Route::get('/credit-notes-types', [CreditNotesTypeController::class, 'index'])->name('credit-notes-types.index');
    Route::post('/credit-notes-types/store', [CreditNotesTypeController::class, 'store'])->name('credit-notes-types.store');
    Route::get('/credit-notes-types/{id}', [CreditNotesTypeController::class, 'show'])->name('credit-notes-types.show');
    Route::post('/credit-notes-types/update/{id}', [CreditNotesTypeController::class, 'update'])->name('credit-notes-types.update');
    Route::post('/credit-notes-types/delete/{id}', [CreditNotesTypeController::class, 'destroy'])->name('credit-notes-types.destroy');

    // credit notes items
    Route::get('/credit-note-items-api', [CreditNoteItemApiController::class, 'index'])->name('credit-note-items.api.index');
    Route::get('/credit-note-jobcards', [CreditNoteItemApiController::class, 'getJobCards'])->name('credit-note-items.api.jobcards');
    Route::get('/credit-note-jobcards/{id}', [CreditNoteItemApiController::class, 'getJobCardDetails'])->name('credit-note-items.api.jobcard-details');
    Route::get('/getPurchaseDetails/{id}', [CreditNoteItemApiController::class, 'getPurchaseDetails'])->name('getPurchaseDetails');
    Route::post('/credit-note-items-api/store', [CreditNoteItemApiController::class, 'store'])->name('credit-note-items.api.store');
    Route::get('/credit-note-items-api/{id}', [CreditNoteItemApiController::class, 'show'])->name('credit-note-items.api.show');
    Route::post('/credit-note-items-api/update/{id}', [CreditNoteItemApiController::class, 'update'])->name('credit-note-items.api.update');
    Route::post('/credit-note-items-api/delete/{id}', [CreditNoteItemApiController::class, 'destroy'])->name('credit-note-items.api.destroy');

    // debit note items
    Route::get('/debit-note-items', [DebitNoteItemApiController::class, 'index'])->name('debit-note-items.index');
    Route::post('/debit-note-items/store', [DebitNoteItemApiController::class, 'store'])->name('debit-note-items.store');
    Route::get('/debit-note-items/create-data', [DebitNoteItemApiController::class, 'getCreateData'])->name('debit-note-items.create-data');
    Route::get('/get-invoice-details/{invoice_number}', [DebitNoteItemApiController::class, 'getInvoiceDetails'])->name('get-invoice-details');
    Route::get('/get-order-details/{order_number}', [DebitNoteItemApiController::class, 'getOrderDetails'])->name('get-order-details');
    Route::get('/jobcards/details/{job_card_number}', [DebitNoteItemApiController::class, 'getJobCardDetails'])->name('jobcards.details');
    Route::get('/debit-note-items/{id}', [DebitNoteItemApiController::class, 'show']);
    Route::post('/debit-note-items/update/{id}', [DebitNoteItemApiController::class, 'update']);
    Route::post('/debit-note-items/delete/{id}', [DebitNoteItemApiController::class, 'destroy']);

    // Designation APIs
    Route::get('/designation', [AdminDesignationController::class, 'index']);
    Route::post('/designation', [AdminDesignationController::class, 'store']);
    Route::post('/designation/add', [AdminDesignationController::class, 'quickStore']);
    Route::get('/designation/{id}', [AdminDesignationController::class, 'show']);
    Route::match(['post', 'put', 'patch'], '/designation/{id}', [AdminDesignationController::class, 'update']);
    Route::delete('/designation/{id}', [AdminDesignationController::class, 'destroy']);
    Route::get('/designations', [AdminDesignationController::class, 'all']);

     // Department APIs
    Route::get('/department', [AdminDepartmentController::class, 'index']);
    Route::post('/department', [AdminDepartmentController::class, 'store']);
    Route::post('/department/add', [AdminDepartmentController::class, 'quickStore']);
    Route::get('/department/{id}', [AdminDepartmentController::class, 'show']);
    Route::match(['post', 'put', 'patch'], '/department/{id}', [AdminDepartmentController::class, 'update']);
    Route::delete('/department/{id}', [AdminDepartmentController::class, 'destroy']);
    Route::get('/departments', [AdminDepartmentController::class, 'all']);
    Route::get('/getdepartments', [AdminDepartmentController::class, 'all']);


    Route::get('/get-employees', [AttendanceeController::class, 'getEmployees']);
    Route::get('/attendance/absent-reason', [AttendanceeController::class, 'getAbsentReason']);
    Route::post('/attendance/update-reason', [AttendanceeController::class, 'updateStatus']);
    Route::post('/attendance/checkin', [AttendanceeController::class, 'checkIn']);
    Route::post('/attendance/checkout', [AttendanceeController::class, 'checkOut']);
    // Alias route: handles both check-in and checkout depending on 'action' in the body
    Route::post('/attendance/check-in', function (\Illuminate\Http\Request $request) {
        $action = $request->input('action', 'checkin');
        $controller = app(\App\Http\Controllers\admin\AttendanceeController::class);
        if ($action === 'checkout') {
            return $controller->checkOut();
        }
        return $controller->checkIn();
    });
    Route::get('/attendance/status', [AttendanceeController::class, 'getStatus']);
    Route::get('/attendance/getAttendanceData', [AttendanceeController::class, 'getAttendanceData']);
    Route::get('/attendance/getAttendance/{month}/{year}', [AttendanceeController::class, 'getAttendance'])
        ->whereNumber('month')
        ->whereNumber('year');
    Route::get('/dashboard-stats', [AttendanceeController::class, 'getDashboardStats']);
    Route::get('/leave/getLeaveDetails/{id}', [AttendanceeController::class, 'getLeaveDetails'])->whereNumber('id');
    Route::get('/attendance/get-face-photo', [AttendanceeController::class, 'getFacePhoto']);
    Route::post('/attendance/face-checkin', [AttendanceeController::class, 'faceCheckIn']);
    Route::delete('/attendance/delete-today', [AttendanceeController::class, 'deleteTodayAttendance']);
    Route::get('/attendance/day-records', [AttendanceeController::class, 'getDayAttendanceRecords']);
    Route::post('/attendance/update-day-records', [AttendanceeController::class, 'updateDayAttendanceRecords']);
    Route::post('/attendance/bulk-update', [AttendanceeController::class, 'bulkUpdateAttendanceRecords']);
    Route::post('/attendance/recalculate-overtime', [AttendanceeController::class, 'recalculateOvertimeForMonth']);

       // Plans CRUD
    Route::get('/getAllPlans',[PlanController::class, 'getAllPlans'])->name('plans.getAll');
    Route::get('/getPlanById/{id}',[PlanController::class, 'getPlanById'])->name('plans.getById');
    Route::post('/createPlan',[PlanController::class, 'createPlan'])->name('plans.create');
    Route::post('/updatePlan',[PlanController::class, 'updatePlan'])->name('plans.update');
    Route::post('/deletePlan/{id}',[PlanController::class, 'deletePlan'])->name('plans.delete');
    Route::post('/togglePlanStatus/{id}',[PlanController::class, 'togglePlanStatus'])->name('plans.toggleStatus');

    Route::get('/get-salary', [PayrollController::class, 'getSalary']);
    Route::prefix('payroll')->group(function () {
        Route::post('/create', [PayrollController::class, 'create']);
        Route::get('/getAll', [PayrollController::class, 'getAll']);
        Route::get('/{id}', [PayrollController::class, 'getByEmployee'])->whereNumber('id');
        Route::get('/details/{id}', [PayrollController::class, 'getProfile'])->whereNumber('id');
        Route::post('/update/{id}', [PayrollController::class, 'update'])->whereNumber('id');
        Route::delete('/{id}', [PayrollController::class, 'delete'])->whereNumber('id');
        Route::post('/get-worked-hours', [PayrollController::class, 'getWorkedHours']);
        Route::post('/get-working-days', [PayrollController::class, 'getWorkingDays']);
        Route::post('/calculate-payroll', [PayrollController::class, 'calculatePayroll']);
        Route::post('/save', [PayrollController::class, 'save']);
        Route::post('/saveAll', [PayrollController::class, 'saveAll']);
        Route::post('/get-monthly-leaves', [PayrollController::class, 'getMonthLeaves']);
        Route::post('/get-leave-details', [PayrollController::class, 'getLeaveDetails']);
        Route::post('/get-remaining-leaves/{userId}', [PayrollController::class, 'getRemainingPaidLeaves'])->whereNumber('userId');
        Route::match(['get', 'post'], '/get-deduction-breakdown', [PayrollController::class, 'getDeductionBreakdown']);
        Route::post('/downloadMultiple', [PayrollController::class, 'downloadMultiple']);
        Route::post('/downloadMultipleByIds', [PayrollController::class, 'downloadMultipleByIds']);
    });

    
});

// Leave APIs (CodeIgniter compatibility + Laravel routes)
Route::get('/leave', [AdminLeaveController::class, 'index']);
Route::post('/leave', [AdminLeaveController::class, 'store']);
Route::post('/leave/update/{leaveId}', [AdminLeaveController::class, 'updateStatus'])->whereNumber('leaveId');
Route::get('/getEmployees', [AdminLeaveController::class, 'getEmployees']);
Route::get('/hr/dashboard-data', [HrDashboardController::class, 'getDashboardData']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
