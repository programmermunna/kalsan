<?php

use App\Http\Controllers\AamarpayController;
use App\Http\Controllers\AiTemplateController;
use App\Http\Controllers\AllowanceController;
use App\Http\Controllers\AllowanceOptionController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AppraisalController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AttendanceEmployeeController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\AuthorizeNetController;
use App\Http\Controllers\AwardController;
use App\Http\Controllers\AwardTypeController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\BankTransferController;
use App\Http\Controllers\BankTransferPaymentController;
use App\Http\Controllers\BenefitPaymentController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\BiometricAttendanceController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\BugStatusController;
use App\Http\Controllers\CashfreeController;
use App\Http\Controllers\ChartOfAccountController;
use App\Http\Controllers\CinetPayController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CoingatePaymentController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\CompanyPolicyController;
use App\Http\Controllers\CompetenciesController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\ContractTypeController;
use App\Http\Controllers\CreditNoteController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerCreditNotesController;
use App\Http\Controllers\CustomerDebitNotesController;
use App\Http\Controllers\CustomFieldController;
use App\Http\Controllers\CustomQuestionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\DebitNoteController;
use App\Http\Controllers\DeductionOptionController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DoubleEntryReportController;
use App\Http\Controllers\DucumentUploadController;
use App\Http\Controllers\EasebuzzController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FedapayController;
use App\Http\Controllers\FlutterwavePaymentController;
use App\Http\Controllers\FormBuilderController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\GoalTrackingController;
use App\Http\Controllers\GoalTypeController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\IndicatorController;
use App\Http\Controllers\InterviewScheduleController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\CargoController;
use App\Http\Controllers\IyziPayController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\JobCategoryController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\JobStageController;
use App\Http\Controllers\JournalEntryController;
use App\Http\Controllers\KhaltiController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\LeadStageController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\LeaveTypeController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\LoanOptionController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\MercadoPaymentController;
use App\Http\Controllers\MolliePaymentController;
use App\Http\Controllers\NotificationTemplatesController;
use App\Http\Controllers\OtherPaymentController;
use App\Http\Controllers\OvertimeController;
use App\Http\Controllers\PayFastController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentWallPaymentController;
use App\Http\Controllers\PaypalController;
use App\Http\Controllers\PaySlipController;
use App\Http\Controllers\PayslipTypeController;
use App\Http\Controllers\PaystackPaymentController;
use App\Http\Controllers\PaytabController;
use App\Http\Controllers\PaytmPaymentController;
use App\Http\Controllers\PaytrController;
use App\Http\Controllers\YooKassaController;
use App\Http\Controllers\PerformanceTypeController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PipelineController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductServiceCategoryController;
use App\Http\Controllers\ProductServiceController;
use App\Http\Controllers\VisaController;
use App\Http\Controllers\ProductServiceUnitController;
use App\Http\Controllers\ProductStockController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectReportController;
use App\Http\Controllers\ProjectstagesController;
use App\Http\Controllers\ProjectTaskController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\RazorpayPaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ResignationController;
use App\Http\Controllers\RevenueController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SaturationDeductionController;
use App\Http\Controllers\SetSalaryController;
use App\Http\Controllers\SkrillPaymentController;
use App\Http\Controllers\SourceController;
use App\Http\Controllers\SspayController;
use App\Http\Controllers\StageController;
use App\Http\Controllers\StripePaymentController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\TaskStageController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\TerminationController;
use App\Http\Controllers\TerminationTypeController;
use App\Http\Controllers\TimesheetController;
use App\Http\Controllers\TimeTrackerController;
use App\Http\Controllers\ToyyibpayController;
use App\Http\Controllers\TrainerController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\TravelController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VenderController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\WarehouseTransferController;
use App\Http\Controllers\WarningController;
use App\Http\Controllers\ZoomMeetingController;
use App\Http\Controllers\XenditPaymentController;
use App\Http\Controllers\MidtransPaymentController;
use App\Http\Controllers\ProjectExpenseController;
use App\Http\Controllers\NepalstePaymnetController;
use App\Http\Controllers\OzowPaymentController;
use App\Http\Controllers\PaiementProController;
use App\Http\Controllers\PayHereController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\TapController;
use App\Http\Controllers\TrainingTypeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth'])->name('dashboard');

require __DIR__ . '/auth.php';

//
// Route::get('/', ['as' => 'home','uses' =>'HomeController@index'])->middleware(['XSS']);
//Route::get('/home', ['as' => 'home','uses' =>'HomeController@index'])->middleware(['auth','XSS']);


Route::get('/', [DashboardController::class, 'account_dashboard_index'])->name('home')->middleware(['XSS', 'revalidate']);

// Route::get('/home', [DashboardController::class, 'account_dashboard_index'])->name('home')->middleware(['XSS', 'revalidate']);

Route::get('/register', function () {
    return Redirect::to('login');
});


// Route::get('/password/resets/{lang?}', 'Auth\AuthenticatedSessionController@showLinkRequestForm')->name('change.langPass');
// Route::get('/password/resets/{lang?}', 'Auth\LoginController@showLinkRequestForm')->name('change.langPass');



Route::get('/account-dashboard', [DashboardController::class, 'account_dashboard_index'])->name('dashboard')->middleware(['auth', 'XSS', 'revalidate']);

Route::get('/project-dashboard', [DashboardController::class, 'project_dashboard_index'])->name('project.dashboard')->middleware(['auth', 'XSS', 'revalidate']);

Route::get('/hrm-dashboard', [DashboardController::class, 'hrm_dashboard_index'])->name('hrm.dashboard')->middleware(['auth', 'XSS', 'revalidate']);

Route::get('/crm-dashboard', [DashboardController::class, 'crm_dashboard_index'])->name('crm.dashboard')->middleware(['auth', 'XSS', 'revalidate']);

Route::get('/pos-dashboard', [DashboardController::class, 'pos_dashboard_index'])->name('pos.dashboard')->middleware(['auth', 'XSS', 'revalidate']);

Route::get('profile', [UserController::class, 'profile'])->name('profile')->middleware(['auth', 'XSS', 'revalidate']);

Route::any('edit-profile', [UserController::class, 'editprofile'])->name('update.account')->middleware(['auth', 'XSS', 'revalidate']);

Route::resource('users', UserController::class)->middleware(['auth', 'XSS', 'revalidate']);

Route::post('change-password', [UserController::class, 'updatePassword'])->name('update.password');

Route::any('user-reset-password/{id}', [UserController::class, 'userPassword'])->name('users.reset');

Route::post('user-reset-password/{id}', [UserController::class, 'userPasswordReset'])->name('user.password.update');

Route::get('/change/mode', [UserController::class, 'changeMode'])->name('change.mode');

Route::resource('roles', RoleController::class)->middleware(['auth', 'XSS', 'revalidate']);

Route::resource('permissions', PermissionController::class)->middleware(['auth', 'XSS', 'revalidate']);



Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
            'revalidate',
        ],
    ], function () {
        Route::get('change-language/{lang}', [LanguageController::class, 'changeLanquage'])->name('change.language');

        Route::get('manage-language/{lang}', [LanguageController::class, 'manageLanguage'])->name('manage.language');

        Route::post('store-language-data/{lang}', [LanguageController::class, 'storeLanguageData'])->name('store.language.data');

        Route::get('create-language', [LanguageController::class, 'createLanguage'])->name('create.language');

        Route::any('store-language', [LanguageController::class, 'storeLanguage'])->name('store.language');

        Route::delete('/lang/{lang}', [LanguageController::class, 'destroyLang'])->name('lang.destroy');

    }
);
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
            'revalidate',
        ],
    ], function () {
        Route::resource('systems', SystemController::class)->except(['create', 'show']);
        Route::post('email-settings', [SystemController::class, 'saveEmailSettings'])->name('email.settings');
        Route::post('company-settings', [SystemController::class, 'saveCompanySettings'])->name('company.settings');
        Route::post('system-settings', [SystemController::class, 'saveSystemSettings'])->name('system.settings');
        Route::post('zoom-settings', [SystemController::class, 'saveZoomSettings'])->name('zoom.settings');
        Route::post('tracker-settings', [SystemController::class, 'saveTrackerSettings'])->name('tracker.settings');
        Route::post('slack-settings', [SystemController::class, 'saveSlackSettings'])->name('slack.settings');
        Route::post('telegram-settings', [SystemController::class, 'saveTelegramSettings'])->name('telegram.settings');
        Route::post('twilio-settings', [SystemController::class, 'saveTwilioSettings'])->name('twilio.setting');
        Route::get('print-setting', [SystemController::class, 'printIndex'])->name('print.setting');
        Route::get('settings', [SystemController::class, 'companyIndex'])->name('settings');
        Route::post('business-setting', [SystemController::class, 'saveBusinessSettings'])->name('business.setting');
        Route::post('company-payment-setting', [SystemController::class, 'saveCompanyPaymentSettings'])->name('company.payment.settings');
        Route::post('currency-settings', [SystemController::class, 'saveCurrencySettings'])->name('currency.settings');
        Route::post('company-preview', [SystemController::class, 'currencyPreview'])->name('currency.preview');

        Route::any('test-mail', [SystemController::class, 'testMail'])->name('test.mail');
        // Route::post('test-mail', [SystemController::class, 'testMail'])->name('test.mail');
        Route::post('test-mail/send', [SystemController::class, 'testSendMail'])->name('test.send.mail');

        Route::post('stripe-settings', [SystemController::class, 'savePaymentSettings'])->name('payment.settings');
        Route::post('pusher-setting', [SystemController::class, 'savePusherSettings'])->name('pusher.setting');
        Route::post('recaptcha-settings', [SystemController::class, 'recaptchaSettingStore'])->name('recaptcha.settings.store')->middleware(['auth', 'XSS']);
        Route::post('seo-settings', [SystemController::class, 'seoSettings'])->name('seo.settings.store')->middleware(['auth', 'XSS']);

        Route::get('webhook-settings/create', [SystemController::class, 'webhookCreate'])->name('webhook.create')->middleware(['auth', 'XSS']);
        Route::post('webhook-settings/store', [SystemController::class, 'webhookStore'])->name('webhook.store');
        Route::get('webhook-settings/{wid}/edit', [SystemController::class, 'webhookEdit'])->name('webhook.edit')->middleware(['auth', 'XSS']);
        Route::post('webhook-settings/{wid}/edit', [SystemController::class, 'webhookUpdate'])->name('webhook.update')->middleware(['auth', 'XSS']);
        Route::delete('webhook-settings/{wid}', [SystemController::class, 'webhookDestroy'])->name('webhook.destroy')->middleware(['auth', 'XSS']);

        Route::post('cookie-setting', [SystemController::class, 'saveCookieSettings'])->name('cookie.setting');

        Route::post('cache-settings', [SystemController::class, 'cacheSettingStore'])->name('cache.settings.store')->middleware(['auth', 'XSS']);

    }
);

Route::get('productservice/{id}/detail', [ProductServiceController::class, 'warehouseDetail'])->name('productservice.detail')->middleware(['auth', 'XSS']);
Route::get('visa/{id}/detail', [VisaController::class, 'warehouseDetail'])->name('visa.detail')->middleware(['auth', 'XSS']);
Route::post('empty-cart', [ProductServiceController::class, 'emptyCart'])->middleware(['auth', 'XSS']);
Route::post('warehouse-empty-cart', [ProductServiceController::class, 'warehouseemptyCart'])->name('warehouse-empty-cart')->middleware(['auth', 'XSS']);
Route::resource('productservice', ProductServiceController::class)->middleware(['auth', 'XSS', 'revalidate']);

 Route::resource('visa', VisaController::class)->middleware(['auth', 'XSS', 'revalidate']);

//Product Stock
Route::resource('productstock', ProductStockController::class)->middleware(['auth', 'XSS', 'revalidate']);

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
            'revalidate',
        ],
    ], function () {
        Route::resource('customer', CustomerController::class);
    }
);
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
            'revalidate',
        ],
    ], function () {
        Route::resource('vender', VenderController::class);
    }
);

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
            'revalidate',
        ],
    ], function () {
        Route::resource('bank-account', BankAccountController::class);
    }
);
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
            'revalidate',
        ],
    ], function () {
        Route::resource('bank-transfer', BankTransferController::class);
    }
);

Route::resource('taxes', TaxController::class)->middleware(['auth', 'XSS', 'revalidate']);

Route::resource('product-category', ProductServiceCategoryController::class)->middleware(['auth', 'XSS', 'revalidate']);
Route::post('product-category/getaccount', [ProductServiceCategoryController::class, 'getAccount'])->name('productServiceCategory.getaccount')->middleware(['auth', 'XSS', 'revalidate']);

Route::resource('product-unit', ProductServiceUnitController::class)->middleware(['auth', 'XSS', 'revalidate']);

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
            'revalidate',
        ],
    ], function () {
        Route::get('invoice/{id}/duplicate', [InvoiceController::class, 'duplicate'])->name('invoice.duplicate');
        Route::get('invoice/{id}/shipping/print', [InvoiceController::class, 'shippingDisplay'])->name('invoice.shipping.print');
        Route::get('invoice/{id}/payment/reminder', [InvoiceController::class, 'paymentReminder'])->name('invoice.payment.reminder');
        Route::post('invoice/product/destroy', [InvoiceController::class, 'productDestroy'])->name('invoice.product.destroy');
        Route::post('invoice/product', [InvoiceController::class, 'product'])->name('invoice.product');
        Route::post('invoice/customer', [InvoiceController::class, 'customer'])->name('invoice.customer');
        Route::post('invoice/customer/commission', [InvoiceController::class, 'customerCommission'])->name('invoice.customer.commission');
        Route::post('invoice/vender', [InvoiceController::class, 'vender'])->name('invoice.vender');
        Route::get('invoice/{id}/sent', [InvoiceController::class, 'sent'])->name('invoice.sent');
        Route::get('invoice/{id}/resent', [InvoiceController::class, 'resent'])->name('invoice.resent');
        Route::get('invoice/{id}/payment', [InvoiceController::class, 'payment'])->name('invoice.payment');
        Route::post('invoice/{id}/payments', [InvoiceController::class, 'createPayment'])->name('invoice.payments');
        Route::post('invoice/{id}/payment/{pid}/destroy', [InvoiceController::class, 'paymentDestroy'])->name('invoice.payment.destroy');
        Route::get('invoice/items', [InvoiceController::class, 'items'])->name('invoice.items');
        Route::resource('invoice', InvoiceController::class);
        Route::get('invoice/create/{cid}', [InvoiceController::class, 'create'])->name('invoice.create');
    }
);

Route::get('/invoices/preview/{template}/{color}', [InvoiceController::class, 'previewInvoice'])->name('invoice.preview');
Route::post('/invoices/template/setting', [InvoiceController::class, 'saveTemplateSettings'])->name('template.setting');

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
            'revalidate',
        ],
    ], function () {
        Route::get('customer-credits-note', [CustomerCreditNotesController::class, 'index'])->name('custom-credit.note');
        Route::get('customer-credit', [CustomerCreditNotesController::class, 'create'])->name('create.custom.credit.note');
        Route::post('custom-credit-store', [CustomerCreditNotesController::class, 'store'])->name('custom-credits.store');
        Route::get('invoice/{id}/custom-credit/edit/{cn_id}', [CustomerCreditNotesController::class, 'edit'])->name('invoice.edit.custom-credit');
        Route::post('invoice/{id}/custom-credit-note/edit/{cn_id}', [CustomerCreditNotesController::class, 'update'])->name('invoice.custom-note.edit');
        Route::delete('invoice/{id}/custom-credit/delete/{cn_id}', [CustomerCreditNotesController::class, 'destroy'])->name('invoice.custom-note.delete');
        Route::post('credit-invoice/items', [CustomerCreditNotesController::class, 'getItems'])->name('credit-invoice.items');
        Route::post('credit-invoice/itemprice', [CustomerCreditNotesController::class, 'getItemPrice'])->name('credit-invoice.itemprice');

        Route::get('invoice/{id}/credit-note', [CreditNoteController::class, 'create'])->name('invoice.credit.note');
        Route::post('invoice/{id}/credit-storenote', [CreditNoteController::class, 'store'])->name('invoice.credit.storenote');
        Route::get('invoice/{id}/credit-note/edit/{cn_id}', [CreditNoteController::class, 'edit'])->name('invoice.edit.credit.note');
        Route::post('invoice/{id}/credit-note//{cn_id}', [CreditNoteController::class, 'update'])->name('invoice.edit.credit.updatenote');
        Route::delete('invoice/{id}/credit-note/delete/{cn_id}', [CreditNoteController::class, 'destroy'])->name('invoice.delete.credit.note');
        Route::post('invoice/credit-note/price', [CreditNoteController::class, 'getPrice'])->name('credit-note.price');
    }
);

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
            'revalidate',
        ],
    ], function () {
        Route::get('customer-debits-note', [CustomerDebitNotesController::class, 'index'])->name('custom-debit.note');
        Route::get('customer-debit', [CustomerDebitNotesController::class, 'create'])->name('create.custom.debit.note');
        Route::post('custom-debit-store', [CustomerDebitNotesController::class, 'store'])->name('custom-debits.store');
        Route::get('bill/{id}/custom-debit/edit/{cn_id}', [CustomerDebitNotesController::class, 'edit'])->name('bill.edit.custom-debit');
        Route::post('bill/{id}/custom-debit-note/edit/{cn_id}', [CustomerDebitNotesController::class, 'update'])->name('bill.custom-note.edit');
        Route::delete('bill/{id}/custom-debit/delete/{cn_id}', [CustomerDebitNotesController::class, 'destroy'])->name('bill.custom-note.delete');
        Route::post('debit-bill/items', [CustomerDebitNotesController::class, 'getItems'])->name('debit-bill.items');
        Route::post('debit-bill/itemprice', [CustomerDebitNotesController::class, 'getItemPrice'])->name('debit-bill.itemprice');

        Route::get('bill/{id}/debit-note', [DebitNoteController::class, 'create'])->name('bill.debit.note');
        Route::post('bill/{id}/debit-storenote', [DebitNoteController::class, 'store'])->name('bill.debit.storenote');
        Route::get('bill/{id}/debit-note/edit/{cn_id}', [DebitNoteController::class, 'edit'])->name('bill.edit.debit.note');
        Route::post('bill/{id}/debit-note//{cn_id}', [DebitNoteController::class, 'update'])->name('bill.edit.debit.updatenote');
        Route::delete('bill/{id}/debit-note/delete/{cn_id}', [DebitNoteController::class, 'destroy'])->name('bill.delete.debit.note');
        Route::post('bill/debit-note/price', [DebitNoteController::class, 'getPrice'])->name('debit-note.price');
    }
);

Route::get('/bill/preview/{template}/{color}', [BillController::class, 'previewBill'])->name('bill.preview')->middleware(['auth', 'XSS']);
Route::post('/bill/template/setting', [BillController::class, 'saveBillTemplateSettings'])->name('bill.template.setting');

Route::resource('taxes', TaxController::class)->middleware(['auth', 'XSS', 'revalidate']);


Route::resource('revenue', RevenueController::class)->middleware(['auth', 'XSS', 'revalidate']);

Route::get('invoice/pdf/{id}', [InvoiceController::class, 'invoice'])->name('invoice.pdf')->middleware(['XSS', 'revalidate']);

Route::get('bill/pdf/{id}', [BillController::class, 'bill'])->name('bill.pdf')->middleware(['XSS', 'revalidate']);

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
            'revalidate',
        ],
    ], function () {
        Route::get('bill/{id}/duplicate', [BillController::class, 'duplicate'])->name('bill.duplicate');
        Route::get('bill/{id}/shipping/print', [BillController::class, 'shippingDisplay'])->name('bill.shipping.print');
        Route::post('bill/product/destroy', [BillController::class, 'productDestroy'])->name('bill.product.destroy');
        Route::post('bill/product', [BillController::class, 'product'])->name('bill.product');
        Route::post('bill/vender', [BillController::class, 'vender'])->name('bill.vender');
        Route::get('bill/{id}/sent', [BillController::class, 'sent'])->name('bill.sent');
        Route::get('bill/{id}/resent', [BillController::class, 'resent'])->name('bill.resent');
        Route::get('bill/{id}/payment', [BillController::class, 'payment'])->name('bill.payment');
        Route::post('bill/{id}/payments', [BillController::class, 'createPayment'])->name('bill.payments');
        Route::post('bill/{id}/payment/{pid}/destroy', [BillController::class, 'paymentDestroy'])->name('bill.payment.destroy');
        Route::get('bill/items', [BillController::class, 'items'])->name('bill.items');
        Route::resource('bill', BillController::class);
    }
);

Route::resource('payment', PaymentController::class)->middleware(['auth', 'XSS', 'revalidate'])->except(['show']);

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
            'revalidate',
        ],
    ], function () {
        Route::get('report/transaction', [TransactionController::class, 'index'])->name('transaction.index');
    }
);

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
            'revalidate',
        ],
    ], function () {
        Route::get('report/income-summary', [ReportController::class, 'incomeSummary'])->name('report.income.summary');
        Route::get('report/expense-summary', [ReportController::class, 'expenseSummary'])->name('report.expense.summary');
        Route::get('report/income-vs-expense-summary', [ReportController::class, 'incomeVsExpenseSummary'])->name('report.income.vs.expense.summary');
        Route::get('report/tax-summary', [ReportController::class, 'taxSummary'])->name('report.tax.summary');
        Route::get('report/invoice-summary', [ReportController::class, 'invoiceSummary'])->name('report.invoice.summary');
        Route::get('report/bill-summary', [ReportController::class, 'billSummary'])->name('report.bill.summary');
        Route::get('report/product-stock-report', [ReportController::class, 'productStock'])->name('report.product.stock.report');
        Route::get('report/invoice-report', [ReportController::class, 'invoiceReport'])->name('report.invoice');
        Route::get('report/account-statement-report', [ReportController::class, 'accountStatement'])->name('report.account.statement');

        Route::get('reports-monthly-cashflow', [ReportController::class, 'monthlyCashflow'])->name('report.monthly.cashflow')->middleware(['auth', 'XSS']);
        Route::get('reports-quarterly-cashflow', [ReportController::class, 'quarterlyCashflow'])->name('report.quarterly.cashflow')->middleware(['auth', 'XSS']);
    }
);

// doubel entry reportAdd commentMore actions
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
            'revalidate',
        ],
    ],
    function () {
        Route::get('trial-balance-report/{view?}', [DoubleEntryReportController::class, 'trialBalanceSummary'])->name('trial.balance');
        Route::post('export/trial-balance', [DoubleEntryReportController::class, 'trialBalanceExport'])->name('trial.balance.export');
        Route::get('balance-sheet-report/{view?}/{collapseview?}', [DoubleEntryReportController::class, 'balanceSheet'])->name('report.balance.sheet');
        Route::post('export/balance-sheet', [DoubleEntryReportController::class, 'balanceSheetExport'])->name('balance.sheet.export');
        Route::get('profit-loss-report/{view?}/{collapseView?}', [DoubleEntryReportController::class, 'profitLoss'])->name('report.profit.loss');
        Route::post('export/profit-loss', [DoubleEntryReportController::class, 'profitLossExport'])->name('profit.loss.export');
        Route::get('ledger-report/{account?}', [DoubleEntryReportController::class, 'ledgerSummary'])->name('report.ledger');
        Route::get('report/sales', [DoubleEntryReportController::class, 'salesReport'])->name('report.sales');
        Route::post('export/sales', [DoubleEntryReportController::class, 'salesReportExport'])->name('sales.export');
        Route::get('report/receivables', [DoubleEntryReportController::class, 'ReceivablesReport'])->name('report.receivables');
        Route::get('report/payables', [DoubleEntryReportController::class, 'PayablesReport'])->name('report.payables');
    }
);

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
            'revalidate',
        ],
    ], function () {
        Route::get('proposal/{id}/status/change', [ProposalController::class, 'statusChange'])->name('proposal.status.change');
        Route::get('proposal/{id}/convert', [ProposalController::class, 'convert'])->name('proposal.convert');
        Route::get('proposal/{id}/duplicate', [ProposalController::class, 'duplicate'])->name('proposal.duplicate');
        Route::post('proposal/product/destroy', [ProposalController::class, 'productDestroy'])->name('proposal.product.destroy');
        Route::post('proposal/customer', [ProposalController::class, 'customer'])->name('proposal.customer');
        Route::post('proposal/product', [ProposalController::class, 'product'])->name('proposal.product');
        Route::get('proposal/items', [ProposalController::class, 'items'])->name('proposal.items');
        Route::get('proposal/{id}/sent', [ProposalController::class, 'sent'])->name('proposal.sent');
        Route::get('proposal/{id}/resent', [ProposalController::class, 'resent'])->name('proposal.resent');
        Route::resource('proposal', ProposalController::class);

    }
);

Route::get('/proposal/preview/{template}/{color}', [ProposalController::class, 'previewProposal'])->name('proposal.preview');

Route::post('/proposal/template/setting', [ProposalController::class, 'saveProposalTemplateSettings'])->name('proposal.template.setting');

Route::resource('goal', GoalController::class)->middleware(['auth', 'XSS', 'revalidate']);

//Budget Planner //

Route::resource('budget', BudgetController::class)->middleware(['auth', 'XSS', 'revalidate']);

Route::resource('account-assets', AssetController::class)->middleware(['auth', 'XSS', 'revalidate']);

Route::resource('custom-field', CustomFieldController::class)->middleware(['auth', 'XSS', 'revalidate']);

Route::post('chart-of-account/subtype', [ChartOfAccountController::class, 'getSubType'])->name('charofAccount.subType')->middleware(['auth', 'XSS', 'revalidate']);

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
            'revalidate',
        ],
    ], function () {
        Route::resource('chart-of-account', ChartOfAccountController::class);
    }
);

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
            'revalidate',
        ],
    ], function () {

        Route::post('journal-entry/account/destroy', [JournalEntryController::class, 'accountDestroy'])->name('journal.account.destroy');
        Route::delete('journal-entry/journal/destroy/{item_id}', [JournalEntryController::class, 'journalDestroy'])->name('journal.destroy');

        Route::resource('journal-entry', JournalEntryController::class);

    }
);

// Client Module
Route::resource('clients', ClientController::class)->middleware(['auth', 'XSS']);

Route::any('client-reset-password/{id}', [ClientController::class, 'clientPassword'])->name('clients.reset');
Route::post('client-reset-password/{id}', [ClientController::class, 'clientPasswordReset'])->name('client.password.update');
// Deal Module

// Deal Module
Route::get('/deals/export', [DealController::class, 'export'])->name('deals.export')->middleware(['auth', 'XSS']);
Route::get('import/deals/file', [DealController::class, 'importFile'])->name('deals.import');
Route::post('deals/import', [DealController::class, 'fileImport'])->name('deals.file.import');
Route::get('import/deals/modal', [DealController::class, 'fileImportModal'])->name('deals.import.modal');
Route::post('import/deals', [DealController::class, 'dealImportdata'])->name('deals.import.data');

Route::post('/deals/user', [DealController::class, 'jsonUser'])->name('deal.user.json');
Route::post('/deals/order', [DealController::class, 'order'])->name('deals.order')->middleware(['auth', 'XSS']);
Route::post('/deals/change-pipeline', [DealController::class, 'changePipeline'])->name('deals.change.pipeline')->middleware(['auth', 'XSS']);
Route::post('/deals/change-deal-status/{id}', [DealController::class, 'changeStatus'])->name('deals.change.status')->middleware(['auth', 'XSS']);
Route::get('/deals/{id}/labels', [DealController::class, 'labels'])->name('deals.labels')->middleware(['auth', 'XSS']);
Route::post('/deals/{id}/labels', [DealController::class, 'labelStore'])->name('deals.labels.store')->middleware(['auth', 'XSS']);
Route::get('/deals/{id}/users', [DealController::class, 'userEdit'])->name('deals.users.edit')->middleware(['auth', 'XSS']);
Route::put('/deals/{id}/users', [DealController::class, 'userUpdate'])->name('deals.users.update')->middleware(['auth', 'XSS']);
Route::delete('/deals/{id}/users/{uid}', [DealController::class, 'userDestroy'])->name('deals.users.destroy')->middleware(['auth', 'XSS']);
Route::get('/deals/{id}/clients', [DealController::class, 'clientEdit'])->name('deals.clients.edit')->middleware(['auth', 'XSS']);
Route::put('/deals/{id}/clients', [DealController::class, 'clientUpdate'])->name('deals.clients.update')->middleware(['auth', 'XSS']);
Route::delete('/deals/{id}/clients/{uid}', [DealController::class, 'clientDestroy'])->name('deals.clients.destroy')->middleware(['auth', 'XSS']);
Route::get('/deals/{id}/products', [DealController::class, 'productEdit'])->name('deals.products.edit')->middleware(['auth', 'XSS']);
Route::put('/deals/{id}/products', [DealController::class, 'productUpdate'])->name('deals.products.update')->middleware(['auth', 'XSS']);
Route::delete('/deals/{id}/products/{uid}', [DealController::class, 'productDestroy'])->name('deals.products.destroy')->middleware(['auth', 'XSS']);
Route::get('/deals/{id}/sources', [DealController::class, 'sourceEdit'])->name('deals.sources.edit')->middleware(['auth', 'XSS']);
Route::put('/deals/{id}/sources', [DealController::class, 'sourceUpdate'])->name('deals.sources.update')->middleware(['auth', 'XSS']);
Route::delete('/deals/{id}/sources/{uid}', [DealController::class, 'sourceDestroy'])->name('deals.sources.destroy')->middleware(['auth', 'XSS']);
Route::post('/deals/{id}/file', [DealController::class, 'fileUpload'])->name('deals.file.upload')->middleware(['auth', 'XSS']);
Route::get('/deals/{id}/file/{fid}', [DealController::class, 'fileDownload'])->name('deals.file.download')->middleware(['auth', 'XSS']);
Route::delete('/deals/{id}/file/delete/{fid}', [DealController::class, 'fileDelete'])->name('deals.file.delete')->middleware(['auth', 'XSS']);
Route::post('/deals/{id}/note', [DealController::class, 'noteStore'])->name('deals.note.store')->middleware(['auth']);
Route::get('/deals/{id}/task', [DealController::class, 'taskCreate'])->name('deals.tasks.create')->middleware(['auth', 'XSS']);
Route::post('/deals/{id}/task', [DealController::class, 'taskStore'])->name('deals.tasks.store')->middleware(['auth', 'XSS']);
Route::get('/deals/{id}/task/{tid}/show', [DealController::class, 'taskShow'])->name('deals.tasks.show')->middleware(['auth', 'XSS']);
Route::get('/deals/{id}/task/{tid}/edit', [DealController::class, 'taskEdit'])->name('deals.tasks.edit')->middleware(['auth', 'XSS']);
Route::put('/deals/{id}/task/{tid}', [DealController::class, 'taskUpdate'])->name('deals.tasks.update')->middleware(['auth', 'XSS']);
Route::put('/deals/{id}/task_status/{tid}', [DealController::class, 'taskUpdateStatus'])->name('deals.tasks.update_status')->middleware(['auth', 'XSS']);
Route::delete('/deals/{id}/task/{tid}', [DealController::class, 'taskDestroy'])->name('deals.tasks.destroy')->middleware(['auth', 'XSS']);
Route::get('/deals/{id}/discussions', [DealController::class, 'discussionCreate'])->name('deals.discussions.create')->middleware(['auth', 'XSS']);
Route::post('/deals/{id}/discussions', [DealController::class, 'discussionStore'])->name('deals.discussion.store')->middleware(['auth', 'XSS']);
Route::get('/deals/{id}/permission/{cid}', [DealController::class, 'permission'])->name('deals.client.permission')->middleware(['auth', 'XSS']);
Route::put('/deals/{id}/permission/{cid}', [DealController::class, 'permissionStore'])->name('deals.client.permissions.store')->middleware(['auth', 'XSS']);
Route::get('/deals/list', [DealController::class, 'deal_list'])->name('deals.list')->middleware(['auth', 'XSS']);

// Deal Calls

Route::get('/deals/{id}/call', [DealController::class, 'callCreate'])->name('deals.calls.create')->middleware(['auth', 'XSS']);
Route::post('/deals/{id}/call', [DealController::class, 'callStore'])->name('deals.calls.store')->middleware(['auth']);
Route::get('/deals/{id}/call/{cid}/edit', [DealController::class, 'callEdit'])->name('deals.calls.edit')->middleware(['auth']);
Route::put('/deals/{id}/call/{cid}', [DealController::class, 'callUpdate'])->name('deals.calls.update')->middleware(['auth']);
Route::delete('/deals/{id}/call/{cid}', [DealController::class, 'callDestroy'])->name('deals.calls.destroy')->middleware(['auth', 'XSS']);

// Deal Email

Route::get('/deals/{id}/email', [DealController::class, 'emailCreate'])->name('deals.emails.create')->middleware(['auth', 'XSS']);
Route::post('/deals/{id}/email', [DealController::class, 'emailStore'])->name('deals.emails.store')->middleware(['auth', 'XSS']);





Route::resource('deals', DealController::class)->middleware(['auth', 'XSS']);
// end Deal Module

Route::get('/search', [UserController::class, 'search'])->name('search.json');
Route::post('/stages/order', [StageController::class, 'order'])->name('stages.order');
Route::post('/stages/json', [StageController::class, 'json'])->name('stages.json');

Route::resource('stages', StageController::class);
Route::resource('pipelines', PipelineController::class);
Route::resource('labels', LabelController::class);
Route::resource('sources', SourceController::class);
Route::resource('payments', PaymentController::class);
Route::resource('custom_fields', CustomFieldController::class);

// Leads Module
Route::post('/lead_stages/order', [LeadStageController::class, 'order'])->name('lead_stages.order');

Route::resource('lead_stages', LeadStageController::class)->middleware(['auth']);

Route::post('/leads/json', [LeadController::class, 'json'])->name('leads.json');
Route::post('/leads/order', [LeadController::class, 'order'])->name('leads.order')->middleware(['auth', 'XSS']);
Route::get('/leads/list', [LeadController::class, 'lead_list'])->name('leads.list')->middleware(['auth', 'XSS']);
Route::post('/leads/{id}/file', [LeadController::class, 'fileUpload'])->name('leads.file.upload')->middleware(['auth', 'XSS']);
Route::get('/leads/{id}/file/{fid}', [LeadController::class, 'fileDownload'])->name('leads.file.download')->middleware(['auth', 'XSS']);
Route::delete('/leads/{id}/file/delete/{fid}', [LeadController::class, 'fileDelete'])->name('leads.file.delete')->middleware(['auth', 'XSS']);
Route::post('/leads/{id}/note', [LeadController::class, 'noteStore'])->name('leads.note.store')->middleware(['auth']);
Route::get('/leads/{id}/labels', [LeadController::class, 'labels'])->name('leads.labels')->middleware(['auth', 'XSS']);
Route::post('/leads/{id}/labels', [LeadController::class, 'labelStore'])->name('leads.labels.store')->middleware(['auth', 'XSS']);
Route::get('/leads/{id}/users', [LeadController::class, 'userEdit'])->name('leads.users.edit')->middleware(['auth', 'XSS']);
Route::put('/leads/{id}/users', [LeadController::class, 'userUpdate'])->name('leads.users.update')->middleware(['auth', 'XSS']);
Route::delete('/leads/{id}/users/{uid}', [LeadController::class, 'userDestroy'])->name('leads.users.destroy')->middleware(['auth', 'XSS']);
Route::get('/leads/{id}/products', [LeadController::class, 'productEdit'])->name('leads.products.edit')->middleware(['auth', 'XSS']);
Route::put('/leads/{id}/products', [LeadController::class, 'productUpdate'])->name('leads.products.update')->middleware(['auth', 'XSS']);
Route::delete('/leads/{id}/products/{uid}', [LeadController::class, 'productDestroy'])->name('leads.products.destroy')->middleware(['auth', 'XSS']);
Route::get('/leads/{id}/sources', [LeadController::class, 'sourceEdit'])->name('leads.sources.edit')->middleware(['auth', 'XSS']);
Route::put('/leads/{id}/sources', [LeadController::class, 'sourceUpdate'])->name('leads.sources.update')->middleware(['auth', 'XSS']);
Route::delete('/leads/{id}/sources/{uid}', [LeadController::class, 'sourceDestroy'])->name('leads.sources.destroy')->middleware(['auth', 'XSS']);
Route::get('/leads/{id}/discussions', [LeadController::class, 'discussionCreate'])->name('leads.discussions.create')->middleware(['auth', 'XSS']);
Route::post('/leads/{id}/discussions', [LeadController::class, 'discussionStore'])->name('leads.discussion.store')->middleware(['auth', 'XSS']);
Route::get('/leads/{id}/show_convert', [LeadController::class, 'showConvertToDeal'])->name('leads.convert.deal')->middleware(['auth', 'XSS']);
Route::post('/leads/{id}/convert', [LeadController::class, 'convertToDeal'])->name('leads.convert.to.deal')->middleware(['auth', 'XSS']);

Route::get('/leads/export', [LeadController::class, 'export'])->name('leads.export')->middleware(['auth', 'XSS']);
// Route::post('import/leads', [LeadController::class, 'import'])->name('leads.import');
Route::get('import/leads/file', [LeadController::class, 'importFile'])->name('leads.import');
Route::post('leads/import', [LeadController::class, 'fileImport'])->name('leads.file.import');
Route::get('import/leads/modal', [LeadController::class, 'fileImportModal'])->name('leads.import.modal');
Route::post('import/leads', [LeadController::class, 'leadImportdata'])->name('leads.import.data');

// Lead Calls
Route::get('/leads/{id}/call', [LeadController::class, 'callCreate'])->name('leads.calls.create')->middleware(['auth', 'XSS']);
Route::post('/leads/{id}/call', [LeadController::class, 'callStore'])->name('leads.calls.store')->middleware(['auth']);
Route::get('/leads/{id}/call/{cid}/edit', [LeadController::class, 'callEdit'])->name('leads.calls.edit')->middleware(['auth', 'XSS']);
Route::put('/leads/{id}/call/{cid}', [LeadController::class, 'callUpdate'])->name('leads.calls.update')->middleware(['auth']);
Route::delete('/leads/{id}/call/{cid}', [LeadController::class, 'callDestroy'])->name('leads.calls.destroy')->middleware(['auth', 'XSS']);


// Lead Email

Route::get('/leads/{id}/email', [LeadController::class, 'emailCreate'])->name('leads.emails.create')->middleware(['auth', 'XSS']);
Route::post('/leads/{id}/email', [LeadController::class, 'emailStore'])->name('leads.emails.store')->middleware(['auth']);
Route::resource('leads', LeadController::class)->middleware(['auth', 'XSS']);

// end Leads Module

Route::get('user/{id}/plan', [UserController::class, 'upgradePlan'])->name('plan.upgrade')->middleware(['auth', 'XSS']);
Route::get('user/{id}/plan/{pid}', [UserController::class, 'activePlan'])->name('plan.active')->middleware(['auth', 'XSS']);
Route::get('/{uid}/notification/seen', [UserController::class, 'notificationSeen'])->name('notification.seen');

// Email Templates
Route::get('email_template_lang/{id}/{lang?}', [EmailTemplateController::class, 'manageEmailLang'])->name('manage.email.language')->middleware(['auth', 'XSS']);
Route::any('email_template_store/{pid}', [EmailTemplateController::class, 'storeEmailLang'])->name('store.email.language')->middleware(['auth']);
Route::any('email_template_store', [EmailTemplateController::class, 'updateStatus'])->name('status.email.language')->middleware(['auth']);
Route::resource('email_template', EmailTemplateController::class)->middleware(['auth', 'XSS']);

// End Email Templates

// HRM

Route::resource('user', UserController::class)->middleware(['auth', 'XSS']);
Route::post('employee/json', [EmployeeController::class, 'json'])->name('employee.json')->middleware(['auth', 'XSS']);
Route::post('branch/employee/json', [EmployeeController::class, 'employeeJson'])->name('branch.employee.json')->middleware(['auth', 'XSS']);
Route::get('employee-profile', [EmployeeController::class, 'profile'])->name('employee.profile')->middleware(['auth', 'XSS']);
Route::get('show-employee-profile/{id}', [EmployeeController::class, 'profileShow'])->name('show.employee.profile')->middleware(['auth', 'XSS']);

Route::get('lastlogin', [EmployeeController::class, 'lastLogin'])->name('lastlogin')->middleware(['auth', 'XSS']);

Route::resource('employee', EmployeeController::class)->middleware(['auth', 'XSS']);

Route::post('employee/getdepartment', [EmployeeController::class, 'getDepartment'])->name('employee.getdepartment')->middleware(['auth', 'XSS']);
Route::post('department/employee', [EmployeeController::class, 'getEmployee'])->name('department.getemployee')->middleware(['auth', 'XSS']);

Route::resource('department', DepartmentController::class)->middleware(['auth', 'XSS']);
Route::resource('designation', DesignationController::class)->middleware(['auth', 'XSS']);
Route::resource('document', DocumentController::class)->middleware(['auth', 'XSS']);
Route::resource('branch', BranchController::class)->middleware(['auth', 'XSS']);

// Hrm EmployeeController

Route::get('employee/salary/{eid}', [SetSalaryController::class, 'employeeBasicSalary'])->name('employee.basic.salary')->middleware(['auth', 'XSS']);

//payslip

Route::resource('paysliptype', PayslipTypeController::class)->middleware(['auth', 'XSS']);
Route::resource('allowance', AllowanceController::class)->except(['index'])->middleware(['auth', 'XSS']);
Route::resource('commission', CommissionController::class)->middleware(['auth', 'XSS']);
Route::resource('allowanceoption', AllowanceOptionController::class)->middleware(['auth', 'XSS']);
Route::resource('loanoption', LoanOptionController::class)->middleware(['auth', 'XSS']);
Route::resource('deductionoption', DeductionOptionController::class)->middleware(['auth', 'XSS']);
Route::resource('loan', LoanController::class)->middleware(['auth', 'XSS']);
Route::resource('saturationdeduction', SaturationDeductionController::class)->middleware(['auth', 'XSS']);
Route::resource('otherpayment', OtherPaymentController::class)->middleware(['auth', 'XSS']);
Route::resource('overtime', OvertimeController::class)->middleware(['auth', 'XSS'])->except(['index']);

Route::get('employee/salary/{eid}', [SetSalaryController::class, 'employeeBasicSalary'])->name('employee.basic.salary')->middleware(['auth', 'XSS']);
Route::post('employee/update/sallary/{id}', [SetSalaryController::class, 'employeeUpdateSalary'])->name('employee.salary.update')->middleware(['auth', 'XSS']);
Route::get('salary/employeeSalary', [SetSalaryController::class, 'employeeSalary'])->name('employeesalary')->middleware(['auth', 'XSS']);
Route::resource('setsalary', SetSalaryController::class)->middleware(['auth', 'XSS']);

Route::get('allowances/create/{eid}', [AllowanceController::class, 'allowanceCreate'])->name('allowances.create')->middleware(['auth', 'XSS']);
Route::get('commissions/create/{eid}', [CommissionController::class, 'commissionCreate'])->name('commissions.create')->middleware(['auth', 'XSS']);
Route::get('loans/create/{eid}', [LoanController::class, 'loanCreate'])->name('loans.create')->middleware(['auth', 'XSS']);
Route::get('saturationdeductions/create/{eid}', [SaturationDeductionController::class, 'saturationdeductionCreate'])->name('saturationdeductions.create')->middleware(['auth', 'XSS']);
Route::get('otherpayments/create/{eid}', [OtherPaymentController::class, 'otherpaymentCreate'])->name('otherpayments.create')->middleware(['auth', 'XSS']);
Route::get('overtimes/create/{eid}', [OvertimeController::class, 'overtimeCreate'])->name('overtimes.create')->middleware(['auth', 'XSS']);
Route::get('payslip/paysalary/{id}/{date}', [PaySlipController::class, 'paysalary'])->name('payslip.paysalary')->middleware(['auth', 'XSS']);
Route::get('payslip/bulk_pay_create/{date}', [PaySlipController::class, 'bulk_pay_create'])->name('payslip.bulk_pay_create')->middleware(['auth', 'XSS']);
Route::post('payslip/bulkpayment/{date}', [PaySlipController::class, 'bulkpayment'])->name('payslip.bulkpayment')->middleware(['auth', 'XSS']);
Route::post('payslip/search_json', [PaySlipController::class, 'search_json'])->name('payslip.search_json')->middleware(['auth', 'XSS']);
Route::get('payslip/employeepayslip', [PaySlipController::class, 'employeepayslip'])->name('payslip.employeepayslip')->middleware(['auth', 'XSS']);
Route::get('payslip/showemployee/{id}', [PaySlipController::class, 'showemployee'])->name('payslip.showemployee')->middleware(['auth', 'XSS']);
Route::get('payslip/editemployee/{id}', [PaySlipController::class, 'editemployee'])->name('payslip.editemployee')->middleware(['auth', 'XSS']);
Route::post('payslip/editemployee/{id}', [PaySlipController::class, 'updateEmployee'])->name('payslip.updateemployee')->middleware(['auth', 'XSS']);
Route::get('payslip/pdf/{id}/{m}', [PaySlipController::class, 'pdf'])->name('payslip.pdf')->middleware(['auth', 'XSS']);

Route::get('payslip/send/{id}/{m}', [PaySlipController::class, 'send'])->name('payslip.send')->middleware(['auth', 'XSS']);
Route::get('payslip/delete/{id}', [PaySlipController::class, 'destroy'])->name('payslip.delete')->middleware(['auth', 'XSS']);
Route::resource('payslip', PaySlipController::class)->middleware(['auth', 'XSS'])->except(['create', 'show']);

Route::resource('company-policy', CompanyPolicyController::class)->middleware(['auth', 'XSS']);
Route::resource('indicator', IndicatorController::class)->middleware(['auth', 'XSS']);
Route::resource('appraisal', AppraisalController::class)->middleware(['auth', 'XSS']);

Route::post('branch/employee/json', [EmployeeController::class, 'employeeJson'])->name('branch.employee.json')->middleware(['auth', 'XSS']);

Route::resource('goaltype', GoalTypeController::class)->middleware(['auth', 'XSS']);
Route::resource('goaltracking', GoalTrackingController::class)->middleware(['auth', 'XSS']);
Route::resource('account-assets', AssetController::class)->middleware(['auth', 'XSS']);

Route::post('event/getdepartment', [EventController::class, 'getdepartment'])->name('event.getdepartment')->middleware(['auth', 'XSS']);
Route::post('event/getemployee', [EventController::class, 'getemployee'])->name('event.getemployee')->middleware(['auth', 'XSS']);
Route::resource('event', EventController::class)->middleware(['auth', 'XSS']);

Route::post('meeting/getdepartment', [MeetingController::class, 'getdepartment'])->name('meeting.getdepartment')->middleware(['auth', 'XSS']);
Route::post('meeting/getemployee', [MeetingController::class, 'getemployee'])->name('meeting.getemployee')->middleware(['auth', 'XSS']);
Route::resource('meeting', MeetingController::class)->middleware(['auth', 'XSS']);

Route::resource('trainingtype', TrainingTypeController::class)->middleware(['auth', 'XSS']);
Route::resource('trainer', TrainerController::class)->middleware(['auth', 'XSS']);
Route::post('training/status', [TrainingController::class, 'updateStatus'])->name('training.status')->middleware(['auth', 'XSS']);
Route::resource('training', TrainingController::class)->middleware(['auth', 'XSS']);

// HRM - HR Module

Route::resource('awardtype', AwardTypeController::class)->middleware(['auth', 'XSS']);
Route::resource('award', AwardController::class)->middleware(['auth', 'XSS']);

Route::resource('resignation', ResignationController::class)->middleware(['auth', 'XSS']);

Route::resource('travel', TravelController::class)->middleware(['auth', 'XSS']);

Route::resource('promotion', PromotionController::class)->middleware(['auth', 'XSS']);

Route::resource('complaint', ComplaintController::class)->middleware(['auth', 'XSS']);

Route::resource('warning', WarningController::class)->middleware(['auth', 'XSS']);

Route::resource('termination', TerminationController::class)->middleware(['auth', 'XSS']);
Route::get('termination/{id}/description', [TerminationController::class, 'description'])->name('termination.description');
Route::resource('terminationtype', TerminationTypeController::class)->middleware(['auth', 'XSS']);

Route::post('announcement/getdepartment', [AnnouncementController::class, 'getdepartment'])->name('announcement.getdepartment');
Route::post('announcement/getemployee', [AnnouncementController::class, 'getemployee'])->name('announcement.getemployee');
Route::resource('announcement', AnnouncementController::class)->middleware(['auth', 'XSS']);

Route::resource('holiday', HolidayController::class)->middleware(['auth', 'XSS']);
Route::get('holiday-calender', [HolidayController::class, 'calender'])->name('holiday.calender');

//------------------------------------  Recurtment --------------------------------

Route::resource('job-category', JobCategoryController::class)->middleware(['auth', 'XSS']);

Route::resource('job-stage', JobStageController::class)->middleware(['auth', 'XSS']);
Route::post('job-stage/order', [JobStageController::class, 'order'])->name('job.stage.order');

Route::resource('job', JobController::class)->middleware(['auth', 'XSS']);

Route::get('career/{id}/{lang}', [JobController::class, 'career'])->name('career')->middleware(['XSS']);
Route::get('job/requirement/{code}/{lang}', [JobController::class, 'jobRequirement'])->name('job.requirement')->middleware(['XSS']);

Route::get('job/apply/{code}/{lang}', [JobController::class, 'jobApply'])->name('job.apply')->middleware(['XSS']);
Route::post('job/apply/data/{code}', [JobController::class, 'jobApplyData'])->name('job.apply.data')->middleware(['XSS']);
Route::get('candidates-job-applications', [JobApplicationController::class, 'candidate'])->name('job.application.candidate')->middleware(['auth','XSS']);

Route::resource('job-application', JobApplicationController::class)->middleware(['auth', 'XSS']);
Route::post('job-application/order', [JobApplicationController::class, 'order'])->name('job.application.order')->middleware(['XSS']);
Route::post('job-application/{id}/rating', [JobApplicationController::class, 'rating'])->name('job.application.rating')->middleware(['XSS']);
Route::delete('job-application/{id}/archive', [JobApplicationController::class, 'archive'])->name('job.application.archive')->middleware(['auth', 'XSS']);
Route::post('job-application/{id}/skill/store', [JobApplicationController::class, 'addSkill'])->name('job.application.skill.store')->middleware(['auth', 'XSS']);
Route::post('job-application/{id}/note/store', [JobApplicationController::class, 'addNote'])->name('job.application.note.store')->middleware(['auth', 'XSS']);
Route::delete('job-application/{id}/note/destroy', [JobApplicationController::class, 'destroyNote'])->name('job.application.note.destroy')->middleware(['auth', 'XSS']);
Route::post('job-application/getByJob', [JobApplicationController::class, 'getByJob'])->name('get.job.application')->middleware(['auth', 'XSS']);
Route::get('job-onboard', [JobApplicationController::class, 'jobOnBoard'])->name('job.on.board')->middleware(['auth', 'XSS']);
Route::get('job-onboard/create/{id}', [JobApplicationController::class, 'jobBoardCreate'])->name('job.on.board.create')->middleware(['auth', 'XSS']);
Route::post('job-onboard/store/{id}', [JobApplicationController::class, 'jobBoardStore'])->name('job.on.board.store')->middleware(['auth', 'XSS']);
Route::get('job-onboard/edit/{id}', [JobApplicationController::class, 'jobBoardEdit'])->name('job.on.board.edit')->middleware(['auth', 'XSS']);
Route::post('job-onboard/update/{id}', [JobApplicationController::class, 'jobBoardUpdate'])->name('job.on.board.update')->middleware(['auth', 'XSS']);
Route::delete('job-onboard/delete/{id}', [JobApplicationController::class, 'jobBoardDelete'])->name('job.on.board.delete')->middleware(['auth', 'XSS']);
Route::get('job-onboard/convert/{id}', [JobApplicationController::class, 'jobBoardConvert'])->name('job.on.board.convert')->middleware(['auth', 'XSS']);
Route::post('job-onboard/converts/{id}', [JobApplicationController::class, 'jobBoardConvertData'])->name('job.on.board.converts')->middleware(['auth', 'XSS']);
Route::post('job-application/stage/change', [JobApplicationController::class, 'stageChange'])->name('job.application.stage.change')->middleware(['auth', 'XSS']);

Route::resource('custom-question', CustomQuestionController::class)->middleware(['auth', 'XSS']);
Route::resource('interview-schedule', InterviewScheduleController::class)->middleware(['auth', 'XSS']);
Route::get('taskboard/{view?}', [ProjectTaskController::class, 'taskBoard'])->name('taskBoard.view')->middleware(['auth', 'XSS']);
Route::get('taskboard-view', [ProjectTaskController::class, 'taskboardView'])->name('project.taskboard.view')->middleware(['auth', 'XSS']);

Route::resource('document-upload', DucumentUploadController::class)->middleware(['auth', 'XSS']);

Route::resource('transfer', TransferController::class)->middleware(['auth', 'XSS']);

Route::get('attendanceemployee/bulkattendance', [AttendanceEmployeeController::class, 'bulkAttendance'])->name('attendanceemployee.bulkattendance')->middleware(['auth', 'XSS']);

Route::post('attendanceemployee/bulkattendances', [AttendanceEmployeeController::class, 'bulkAttendanceData'])->name('attendanceemployee.bulkattendances')->middleware(['auth', 'XSS']);

Route::post('attendanceemployee/attendance', [AttendanceEmployeeController::class, 'attendance'])->name('attendanceemployee.attendance')->middleware(['auth', 'XSS']);

Route::resource('attendanceemployee', AttendanceEmployeeController::class)->middleware(['auth', 'XSS']);

Route::resource('leavetype', LeaveTypeController::class)->middleware(['auth', 'XSS']);

Route::get('employee/{id}/leave/{status}/{type}/{month}/{year}', [ReportController::class, 'employeeLeave'])->name('report.employee.leave')->middleware(['auth', 'XSS']);
Route::get('leave/{id}/action', [LeaveController::class, 'action'])->name('leave.action')->middleware(['auth', 'XSS']);
Route::post('leave/changeaction', [LeaveController::class, 'changeaction'])->name('leave.changeaction')->middleware(['auth', 'XSS']);
Route::post('leave/jsoncount', [LeaveController::class, 'jsoncount'])->name('leave.jsoncount')->middleware(['auth', 'XSS']);

Route::resource('leave', LeaveController::class)->middleware(['auth', 'XSS']);

Route::get('report-leave', [ReportController::class, 'leave'])->name('report.leave')->middleware(['auth', 'XSS']);
Route::get('employee/{id}/leave/{status}/{type}/{month}/{year}', [ReportController::class, 'employeeLeave'])->name('report.employee.leave')->middleware(['auth', 'XSS']);

Route::get('reports-payroll', [ReportController::class, 'payroll'])->name('report.payroll')->middleware(['auth', 'XSS']);
Route::post('reports-payroll/getdepartment', [ReportController::class, 'getPayrollDepartment'])->name('report.payroll.getdepartment')->middleware(['auth', 'XSS']);
Route::post('reports-payroll/getemployee', [ReportController::class, 'getPayrollEmployee'])->name('report.payroll.getemployee')->middleware(['auth', 'XSS']);

Route::get('reports-monthly-attendance', [ReportController::class, 'monthlyAttendance'])->name('report.monthly.attendance')->middleware(['auth', 'XSS']);
Route::get('report/attendance/{month}/{branch}/{department}', [ReportController::class, 'exportCsv'])->name('report.attendance')->middleware(['auth', 'XSS']);

//crm report
Route::get('reports-lead', [ReportController::class, 'leadReport'])->name('report.lead')->middleware(['auth', 'XSS']);
Route::get('reports-deal', [ReportController::class, 'dealReport'])->name('report.deal')->middleware(['auth', 'XSS']);

//pos report
Route::get('reports-warehouse', [ReportController::class, 'warehouseReport'])->name('report.warehouse')->middleware(['auth', 'XSS']);

Route::get('reports-daily-purchase', [ReportController::class, 'purchaseDailyReport'])->name('report.daily.purchase')->middleware(['auth', 'XSS']);
Route::get('reports-monthly-purchase', [ReportController::class, 'purchaseMonthlyReport'])->name('report.monthly.purchase')->middleware(['auth', 'XSS']);

Route::get('reports-daily-pos', [ReportController::class, 'posDailyReport'])->name('report.daily.pos')->middleware(['auth', 'XSS']);
Route::get('reports-monthly-pos', [ReportController::class, 'posMonthlyReport'])->name('report.monthly.pos')->middleware(['auth', 'XSS']);

Route::get('reports-pos-vs-purchase', [ReportController::class, 'posVsPurchaseReport'])->name('report.pos.vs.purchase')->middleware(['auth', 'XSS']);

// User Module
Route::get('users/{view?}', [UserController::class, 'index'])->name('users')->middleware(['auth', 'XSS']);
Route::get('users-view', [UserController::class, 'filterUserView'])->name('filter.user.view')->middleware(['auth', 'XSS']);
Route::get('checkuserexists', [UserController::class, 'checkUserExists'])->name('user.exists')->middleware(['auth', 'XSS']);
Route::get('profile', [UserController::class, 'profile'])->name('profile')->middleware(['auth', 'XSS']);
Route::post('/profile', [UserController::class, 'updateProfile'])->name('update.profile')->middleware(['auth', 'XSS']);
Route::get('user/info/{id}', [UserController::class, 'userInfo'])->name('users.info')->middleware(['auth', 'XSS']);
Route::get('user/{id}/info/{type}', [UserController::class, 'getProjectTask'])->name('user.info.popup')->middleware(['auth', 'XSS']);

// End User Module

// Search
Route::get('/search', [UserController::class, 'search'])->name('search.json');

// end

// Milestone Module
Route::get('projects/{id}/milestone', [ProjectController::class, 'milestone'])->name('project.milestone')->middleware(['auth', 'XSS']);

//Route::delete(
//    '/projects/{id}/users/{uid}', [
//                                    'as' => 'projects.users.destroy',
//                                    'uses' => 'ProjectController@userDestroy',
//                                ]
//)->middleware(
//    [
//        'auth',
//        'XSS',
//    ]
//);

Route::post('projects/{id}/milestone', [ProjectController::class, 'milestoneStore'])->name('project.milestone.store')->middleware(['auth', 'XSS']);
Route::get('projects/milestone/{id}/edit', [ProjectController::class, 'milestoneEdit'])->name('project.milestone.edit')->middleware(['auth', 'XSS']);
Route::post('projects/milestone/{id}', [ProjectController::class, 'milestoneUpdate'])->name('project.milestone.update')->middleware(['auth', 'XSS']);
Route::delete('projects/milestone/{id}', [ProjectController::class, 'milestoneDestroy'])->name('project.milestone.destroy')->middleware(['auth', 'XSS']);
Route::get('projects/milestone/{id}/show', [ProjectController::class, 'milestoneShow'])->name('project.milestone.show')->middleware(['auth', 'XSS']);

// End Milestone

// Project Module

Route::get('invite-project-member/{id}', [ProjectController::class, 'inviteMemberView'])->name('invite.project.member.view')->middleware(['auth', 'XSS']);
Route::post('invite-project-user-member', [ProjectController::class, 'inviteProjectUserMember'])->name('invite.project.user.member')->middleware(['auth', 'XSS']);

Route::delete('projects/{id}/users/{uid}', [ProjectController::class, 'destroyProjectUser'])->name('projects.user.destroy')->middleware(['auth', 'XSS']);
Route::get('project/{view?}', [ProjectController::class, 'index'])->name('projects.list')->middleware(['auth', 'XSS']);
Route::get('projects-view', [ProjectController::class, 'filterProjectView'])->name('filter.project.view')->middleware(['auth', 'XSS']);
Route::post('projects/{id}/store-stages/{slug}', [ProjectController::class, 'storeProjectTaskStages'])->name('project.stages.store')->middleware(['auth', 'XSS']);

Route::patch('remove-user-from-project/{project_id}/{user_id}', [ProjectController::class, 'removeUserFromProject'])->name('remove.user.from.project')->middleware(['auth', 'XSS']);
Route::get('projects-users', [ProjectController::class, 'loadUser'])->name('project.user')->middleware(['auth', 'XSS']);
Route::get('projects/{id}/gantt/{duration?}', [ProjectController::class, 'gantt'])->name('projects.gantt')->middleware(['auth', 'XSS']);
Route::post('projects/{id}/gantt', [ProjectController::class, 'ganttPost'])->name('projects.gantt.post')->middleware(['auth', 'XSS']);

Route::resource('projects', ProjectController::class)->middleware(['auth', 'XSS']);

// User Permission
Route::get('projects/{id}/user/{uid}/permission', [ProjectController::class, 'userPermission'])->name('projects.user.permission')->middleware(['auth', 'XSS']);
Route::post('projects/{id}/user/{uid}/permission', [ProjectController::class, 'userPermissionStore'])->name('projects.user.permission.store')->middleware(['auth', 'XSS']);

// End Project Module

// Task Module
Route::get('stage/{id}/tasks', [ProjectTaskController::class, 'getStageTasks'])->name('stage.tasks')->middleware(['auth', 'XSS']);

// Project Task Module
Route::get('/projects/{id}/task', [ProjectTaskController::class, 'index'])->name('projects.tasks.index')->middleware(['auth', 'XSS']);
Route::get('/projects/{pid}/task/create', [ProjectTaskController::class, 'create'])->name('projects.tasks.create')->middleware(['auth', 'XSS']);
Route::post('/projects/{pid}/task/store', [ProjectTaskController::class, 'store'])->name('projects.tasks.store')->middleware(['auth', 'XSS']);
Route::get('/projects/{id}/task/{tid}/show', [ProjectTaskController::class, 'show'])->name('projects.tasks.show')->middleware(['auth', 'XSS']);
Route::get('/projects/{id}/task/{tid}/edit', [ProjectTaskController::class, 'edit'])->name('projects.tasks.edit')->middleware(['auth', 'XSS']);
Route::post('/projects/{id}/task/update/{tid}', [ProjectTaskController::class, 'update'])->name('projects.tasks.update')->middleware(['auth', 'XSS']);
Route::delete('/projects/{id}/task/{tid}', [ProjectTaskController::class, 'destroy'])->name('projects.tasks.destroy')->middleware(['auth', 'XSS']);
Route::patch('/projects/{id}/task/order', [ProjectTaskController::class, 'taskOrderUpdate'])->name('tasks.update.order')->middleware(['auth', 'XSS']);
Route::patch('update-task-priority-color', [ProjectTaskController::class, 'updateTaskPriorityColor'])->name('update.task.priority.color')->middleware(['auth', 'XSS']);

Route::post('/projects/{id}/comment/{tid}/file', [ProjectTaskController::class, 'commentStoreFile'])->name('comment.store.file')->middleware(['auth', 'XSS']);
Route::delete('/projects/{id}/comment/{tid}/file/{fid}', [ProjectTaskController::class, 'commentDestroyFile'])->name('comment.destroy.file');
Route::post('/projects/{id}/comment/{tid}', [ProjectTaskController::class, 'commentStore'])->name('task.comment.store');
Route::delete('/projects/{id}/comment/{tid}/{cid}', [ProjectTaskController::class, 'commentDestroy'])->name('comment.destroy');
Route::post('/projects/{id}/checklist/{tid}', [ProjectTaskController::class, 'checklistStore'])->name('checklist.store');
Route::post('/projects/{id}/checklist/update/{cid}', [ProjectTaskController::class, 'checklistUpdate'])->name('checklist.update');
Route::delete('/projects/{id}/checklist/{cid}', [ProjectTaskController::class, 'checklistDestroy'])->name('checklist.destroy');
Route::post('/projects/{id}/change/{tid}/fav', [ProjectTaskController::class, 'changeFav'])->name('change.fav');
Route::post('/projects/{id}/change/{tid}/complete', [ProjectTaskController::class, 'changeCom'])->name('change.complete');
Route::post('/projects/{id}/change/{tid}/progress', [ProjectTaskController::class, 'changeProg'])->name('change.progress');
Route::get('/projects/task/{id}/get', [ProjectTaskController::class, 'taskGet'])->name('projects.tasks.get')->middleware(['auth', 'XSS']);
Route::get('/calendar/{id}/show', [ProjectTaskController::class, 'calendarShow'])->name('task.calendar.show')->middleware(['auth', 'XSS']);
Route::post('/calendar/{id}/drag', [ProjectTaskController::class, 'calendarDrag'])->name('task.calendar.drag');
Route::get('calendar/{task}/{pid?}', [ProjectTaskController::class, 'calendarView'])->name('task.calendar')->middleware(['auth', 'XSS']);

Route::resource('project-task-stages', TaskStageController::class)->middleware(['auth', 'XSS']);
Route::post('/project-task-stages/order', [TaskStageController::class, 'order'])->name('project-task-stages.order');

Route::post('project-task-new-stage', [TaskStageController::class, 'storingValue'])->name('new-task-stage')->middleware(['auth', 'XSS']);
// End Task Module

// Project Expense Module

// Project Expense Module
Route::get('/projects/{id}/expense', [ProjectExpenseController::class, 'index'])->name('projects.expenses.index')->middleware(['auth', 'XSS']);
Route::get('/projects/{pid}/expense/create', [ProjectExpenseController::class, 'create'])->name('projects.expenses.create')->middleware(['auth', 'XSS']);
Route::post('/projects/{pid}/expense/store', [ProjectExpenseController::class, 'store'])->name('projects.expenses.store')->middleware(['auth', 'XSS']);
Route::get('/projects/{id}/expense/{eid}/edit', [ProjectExpenseController::class, 'edit'])->name('projects.expenses.edit')->middleware(['auth', 'XSS']);
Route::post('/projects/{id}/expense/{eid}', [ProjectExpenseController::class, 'update'])->name('projects.expenses.update')->middleware(['auth', 'XSS']);
Route::delete('/projects/{eid}/expense/', [ProjectExpenseController::class, 'destroy'])->name('projects.expenses.destroy')->middleware(['auth', 'XSS']);
Route::get('/expense-list', [ExpenseController::class, 'expenseList'])->name('expense.list')->middleware(['auth', 'XSS']);

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
            'revalidate',
        ],
    ], function () {
        Route::resource('contractType', ContractTypeController::class);
    }
);

// Project Timesheet
Route::get('append-timesheet-task-html', [TimesheetController::class, 'appendTimesheetTaskHTML'])->name('append.timesheet.task.html')->middleware(['auth', 'XSS']);
Route::get('timesheet-table-view', [TimesheetController::class, 'filterTimesheetTableView'])->name('filter.timesheet.table.view')->middleware(['auth', 'XSS']);
Route::get('timesheet-view', [TimesheetController::class, 'filterTimesheetView'])->name('filter.timesheet.view')->middleware(['auth', 'XSS']);
Route::get('timesheet-list', [TimesheetController::class, 'timesheetList'])->name('timesheet.list')->middleware(['auth', 'XSS']);
Route::get('timesheet-list-get', [TimesheetController::class, 'timesheetListGet'])->name('timesheet.list.get')->middleware(['auth', 'XSS']);
Route::get('/project/{id}/timesheet', [TimesheetController::class, 'timesheetView'])->name('timesheet.index')->middleware(['auth', 'XSS']);
Route::get('/project/{id}/timesheet/create', [TimesheetController::class, 'timesheetCreate'])->name('timesheet.create')->middleware(['auth', 'XSS']);
Route::post('/project/timesheet', [TimesheetController::class, 'timesheetStore'])->name('timesheet.store')->middleware(['auth', 'XSS']);
Route::get('/project/timesheet/{project_id}/edit/{timesheet_id}', [TimesheetController::class, 'timesheetEdit'])->name('timesheet.edit')->middleware(['auth', 'XSS']);
Route::any('/project/timesheet/update/{timesheet_id}', [TimesheetController::class, 'timesheetUpdate'])->name('timesheet.update')->middleware(['auth', 'XSS']);

Route::delete('/project/timesheet/{timesheet_id}', [TimesheetController::class, 'timesheetDestroy'])->name('timesheet.destroy')->middleware(['auth', 'XSS']);

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function () {
        Route::resource('projectstages', ProjectstagesController::class);
        Route::post('/projectstages/order', [ProjectstagesController::class, 'order'])->name('projectstages.order')->middleware(['auth', 'XSS']);
        Route::post('projects/bug/kanban/order', [ProjectController::class, 'bugKanbanOrder'])->name('bug.kanban.order');
        Route::get('projects/{id}/bug/kanban', [ProjectController::class, 'bugKanban'])->name('task.bug.kanban');
        Route::get('projects/{id}/bug', [ProjectController::class, 'bug'])->name('task.bug');
        Route::get('projects/{id}/bug/create', [ProjectController::class, 'bugCreate'])->name('task.bug.create');
        Route::post('projects/{id}/bug/store', [ProjectController::class, 'bugStore'])->name('task.bug.store');
        Route::get('projects/{id}/bug/{bid}/edit', [ProjectController::class, 'bugEdit'])->name('task.bug.edit');
        Route::post('projects/{id}/bug/{bid}/update', [ProjectController::class, 'bugUpdate'])->name('task.bug.update');
        Route::delete('projects/{id}/bug/{bid}/destroy', [ProjectController::class, 'bugDestroy'])->name('task.bug.destroy');
        Route::get('projects/{id}/bug/{bid}/show', [ProjectController::class, 'bugShow'])->name('task.bug.show');
        Route::post('projects/{id}/bug/{bid}/comment', [ProjectController::class, 'bugCommentStore'])->name('bug.comment.store');
        Route::post('projects/bug/{bid}/file', [ProjectController::class, 'bugCommentStoreFile'])->name('bug.comment.file.store');
        Route::delete('projects/bug/comment/{id}', [ProjectController::class, 'bugCommentDestroy'])->name('bug.comment.destroy');
        Route::delete('projects/bug/file/{id}', [ProjectController::class, 'bugCommentDestroyFile'])->name('bug.comment.file.destroy');

        Route::resource('bugstatus', BugStatusController::class);
        Route::post('/bugstatus/order', [BugStatusController::class, 'order'])->name('bugstatus.order');
        Route::get('bugs-report/{view?}', [ProjectTaskController::class, 'allBugList'])->name('bugs.view')->middleware(['auth', 'XSS']);
    }
);

// User_Todo Module

Route::post('/todo/create', [UserController::class, 'todo_store'])->name('todo.store')->middleware(['auth', 'XSS']);
Route::post('/todo/{id}/update', [UserController::class, 'todo_update'])->name('todo.update')->middleware(['auth', 'XSS']);
Route::delete('/todo/{id}', [UserController::class, 'todo_destroy'])->name('todo.destroy')->middleware(['auth', 'XSS']);
Route::get('/change/mode', [UserController::class, 'changeMode'])->name('change.mode')->middleware(['auth', 'XSS']);
Route::get('dashboard-view', [DashboardController::class, 'filterView'])->name('dashboard.view')->middleware(['auth', 'XSS']);
Route::get('dashboard', [DashboardController::class, 'clientView'])->name('client.dashboard.view')->middleware(['auth', 'XSS']);

// saas
Route::resource('users', UserController::class)->middleware(['auth', 'XSS', 'revalidate']);

//================================= Form Builder ====================================//

// Form Builder
Route::resource('form_builder', FormBuilderController::class)->middleware(['auth', 'XSS']);

// Form link base view
Route::get('/form/{code}', [FormBuilderController::class, 'formView'])->name('form.view')->middleware(['XSS']);
Route::post('/form_view_store', [FormBuilderController::class, 'formViewStore'])->name('form.view.store')->middleware(['XSS']);

// Form Field

Route::get('/form_builder/{id}/field', [FormBuilderController::class, 'fieldCreate'])->name('form.field.create')->middleware(['auth', 'XSS']);
Route::post('/form_builder/{id}/field', [FormBuilderController::class, 'fieldStore'])->name('form.field.store')->middleware(['auth', 'XSS']);
Route::get('/form_builder/{id}/field/{fid}/show', [FormBuilderController::class, 'fieldShow'])->name('form.field.show')->middleware(['auth', 'XSS']);
Route::get('/form_builder/{id}/field/{fid}/edit', [FormBuilderController::class, 'fieldEdit'])->name('form.field.edit')->middleware(['auth', 'XSS']);
Route::post('/form_builder/{id}/field/{fid}', [FormBuilderController::class, 'fieldUpdate'])->name('form.field.update')->middleware(['auth', 'XSS']);
Route::delete('/form_builder/{id}/field/{fid}', [FormBuilderController::class, 'fieldDestroy'])->name('form.field.destroy')->middleware(['auth', 'XSS']);

// Form Response
Route::get('/form_response/{id}', [FormBuilderController::class, 'viewResponse'])->name('form.response')->middleware(['auth', 'XSS']);
Route::get('/response/{id}', [FormBuilderController::class, 'responseDetail'])->name('response.detail')->middleware(['auth', 'XSS']);

// Form Field Bind
Route::get('/form_field/{id}', [FormBuilderController::class, 'formFieldBind'])->name('form.field.bind')->middleware(['auth', 'XSS']);
Route::post('/form_field_store/{id}', [FormBuilderController::class, 'bindStore'])->name('form.bind.store')->middleware(['auth', 'XSS']);
// end Form Builder

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
            'revalidate',
        ],
    ], function () {
        Route::get('contract/{id}/description', [ContractController::class, 'description'])->name('contract.description');
        Route::get('contract/grid', [ContractController::class, 'grid'])->name('contract.grid');
        Route::resource('contract', ContractController::class);
    }
);

Route::post('/contract/{id}/file', [ContractController::class, 'fileUpload'])->name('contract.file.upload')->middleware(['auth', 'XSS']);
Route::get('contract/pdf/{id}', [ContractController::class, 'pdffromcontract'])->name('contract.download.pdf')->middleware(['auth']);
Route::get('contract/{id}/get_contract', [ContractController::class, 'printContract'])->name('get.contract')->middleware(['auth']);
Route::post('/contract_status_edit/{id}', [ContractController::class, 'contract_status_edit'])->name('contract.status')->middleware(['auth', 'XSS']);
Route::post('contract/{id}/contract_description', [ContractController::class, 'contract_descriptionStore'])->name('contract.contract_description.store')->middleware(['auth']);
Route::get('/contract/{id}/file/{fid}', [ContractController::class, 'fileDownload'])->name('contracts.file.download')->middleware(['auth', 'XSS']);
Route::delete('/contract/{id}/file/delete/{fid}', [ContractController::class, 'fileDelete'])->name('contracts.file.delete')->middleware(['auth', 'XSS']);
Route::get('/contract/copy/{id}', [ContractController::class, 'copycontract'])->name('contract.copy')->middleware(['auth', 'XSS']);
Route::post('/contract/copy/store', [ContractController::class, 'copycontractstore'])->name('contract.copy.store')->middleware(['auth', 'XSS']);
Route::get('/contract/{id}/mail', [ContractController::class, 'sendmailContract'])->name('send.mail.contract');
Route::get('/signature/{id}', [ContractController::class, 'signature'])->name('signature');
Route::post('/signaturestore', [ContractController::class, 'signatureStore'])->name('signaturestore')->middleware(['auth', 'XSS']);
Route::post('/contract/{id}/comment', [ContractController::class, 'commentStore'])->name('comment.store');
Route::post('/contract/{id}/notes', [ContractController::class, 'noteStore'])->name('note_store.store')->middleware(['auth']);
Route::delete('/contract/{id}/notes', [ContractController::class, 'noteDestroy'])->name('note_store.destroy')->middleware(['auth']);
Route::delete('/contract/{id}/comment', [ContractController::class, 'commentDestroy'])->name('comment_store.destroy');
Route::get('get-projects/{client_id}', [ContractController::class, 'clientByProject'])->name('project.by.user.id')->middleware(['auth', 'XSS']);

//client wise project show in modal
Route::any('/contract/clients/select/{bid}', [ContractController::class, 'clientwiseproject'])->name('contract.clients.select');

//copy contract
Route::get('/contract/copy/{id}', [ContractController::class, 'copycontract'])->name('contract.copy')->middleware(['auth', 'XSS']);
Route::post('contract/copy/store', [ContractController::class, 'copycontractstore'])->name('contract.copy.store')->middleware(['auth', 'XSS']);

//================================= Custom Landing Page ====================================//

//Route::get('/landingpage', [LandingPageSectionController::class, 'index'])->name('custom_landing_page.index')->middleware(['auth','XSS']);
//
//Route::get('/LandingPage/show/{id}', [LandingPageSectionController::class, 'show']);
//Route::post('/LandingPage/setConetent', [LandingPageSectionController::class, 'setConetent'])->middleware(['auth','XSS']);
//
//
//Route::get(
//    '/get_landing_page_section/{name}', function ($name){
//    $plans = \DB::table('plans')->get();
//
//    return view('custom_landing_page.' . $name, compact('plans'));
//}
//);
//
//Route::post('/LandingPage/removeSection/{id}', [LandingPageSectionController::class, 'removeSection'])->middleware(['auth','XSS']);
//Route::post('/LandingPage/setOrder', [LandingPageSectionController::class, 'setOrder'])->middleware(['auth','XSS']);
//Route::post('/LandingPage/copySection', [LandingPageSectionController::class, 'copySection'])->middleware(['auth','XSS']);

Route::get('/customer/cargo/{id}/', [CargoController::class, 'invoiceLink'])->name('cargo.link.copy');
Route::get('/customer/invoice/{id}/', [InvoiceController::class, 'invoiceLink'])->name('invoice.link.copy');
Route::get('/vender/bill/{id}/', [BillController::class, 'invoiceLink'])->name('bill.link.copy');
Route::get('/vendor/purchase/{id}/', [PurchaseController::class, 'purchaseLink'])->name('purchase.link.copy');
Route::get('/customer/proposal/{id}/', [ProposalController::class, 'invoiceLink'])->name('proposal.link.copy');
Route::get('proposal/pdf/{id}', [ProposalController::class, 'proposal'])->name('proposal.pdf')->middleware(['XSS', 'revalidate']);

//Route::post('plan-pay-with-paypal', [PaypalController::class, 'planPayWithPaypal'])->name('plan.pay.with.paypal')->middleware(['auth','XSS','revalidate']);
//Route::get('{id}/plan-get-payment-status', [PaypalController::class, 'planGetPaymentStatus'])->name('plan.get.payment.status')->middleware(['auth','XSS','revalidate']);

//================================= Invoice Payment Gateways  ====================================//

Route::post('/customer-pay-with-bank', [BankTransferPaymentController::class, 'customerPayWithBank'])->name('customer.pay.with.bank')->middleware(['XSS']);
Route::get('invoice/{id}/action', [BankTransferPaymentController::class, 'invoiceAction'])->name('invoice.action');
Route::post('invoice/{id}/changeaction', [BankTransferPaymentController::class, 'invoiceChangeStatus'])->name('invoice.changestatus');

Route::post('customer/{id}/payment', [StripePaymentController::class, 'addpayment'])->name('customer.payment');
Route::post('{id}/pay-with-paypal', [PaypalController::class, 'customerPayWithPaypal'])->name('customer.pay.with.paypal')->middleware(['XSS']);
Route::get('{id}/get-payment-status/{amount}', [PaypalController::class, 'customerGetPaymentStatus'])->name('customer.get.payment.status');

Route::post('/customer-pay-with-paystack', [PaystackPaymentController::class, 'customerPayWithPaystack'])->name('customer.pay.with.paystack')->middleware(['XSS']);
Route::get('/customer/paystack/{pay_id}/{invoice_id}', [PaystackPaymentController::class, 'getInvoicePaymentStatus'])->name('customer.paystack');

Route::post('/customer-pay-with-flaterwave', [FlutterwavePaymentController::class, 'customerPayWithFlutterwave'])->name('customer.pay.with.flaterwave')->middleware(['XSS']);
Route::get('/customer/flaterwave/{txref}/{invoice_id}', [FlutterwavePaymentController::class, 'getInvoicePaymentStatus'])->name('customer.flaterwave');

Route::post('/customer-pay-with-razorpay', [RazorpayPaymentController::class, 'customerPayWithRazorpay'])->name('customer.pay.with.razorpay')->middleware(['XSS']);
Route::get('/customer/razorpay/{txref}/{invoice_id}', [RazorpayPaymentController::class, 'getInvoicePaymentStatus'])->name('customer.razorpay');

Route::post('/customer-pay-with-paytm', [PaytmPaymentController::class, 'customerPayWithPaytm'])->name('customer.pay.with.paytm')->middleware(['XSS']);
Route::post('/customer/paytm/{invoice}/{amount}', [PaytmPaymentController::class, 'getInvoicePaymentStatus'])->name('customer.paytm');

Route::post('/customer-pay-with-mercado', [MercadoPaymentController::class, 'customerPayWithMercado'])->name('customer.pay.with.mercado')->middleware(['XSS']);
Route::get('/customer/mercado/{invoice}', [MercadoPaymentController::class, 'getInvoicePaymentStatus'])->name('customer.mercado');

Route::post('/customer-pay-with-mollie', [MolliePaymentController::class, 'customerPayWithMollie'])->name('customer.pay.with.mollie')->middleware(['XSS']);
Route::get('/customer/mollie/{invoice}/{amount}', [MolliePaymentController::class, 'getInvoicePaymentStatus'])->name('customer.mollie');

Route::post('/customer-pay-with-skrill', [SkrillPaymentController::class, 'customerPayWithSkrill'])->name('customer.pay.with.skrill')->middleware(['XSS']);
Route::get('/customer/skrill/{invoice}/{amount}', [SkrillPaymentController::class, 'getInvoicePaymentStatus'])->name('customer.skrill');

Route::post('/customer-pay-with-coingate', [CoingatePaymentController::class, 'customerPayWithCoingate'])->name('customer.pay.with.coingate')->middleware(['XSS']);
Route::get('/customer/coingate/{invoice}/{amount}', [CoingatePaymentController::class, 'getInvoicePaymentStatus'])->name('customer.coingate');

Route::post('/customer-pay-with-toyyibpay', [ToyyibpayController::class, 'invoicepaywithtoyyibpay'])->name('customer.pay.with.toyyibpay');
Route::get('/customer/toyyibpay/{invoice}/{amount}', [ToyyibpayController::class, 'getInvoicePaymentStatus'])->name('customer.toyyibpay');

Route::post('invoice-with-payfast', [PayFastController::class, 'invoicePayWithPayFast'])->name('invoice.with.payfast');
Route::get('invoice-payfast-status/{success}', [PayFastController::class, 'invoicepayfaststatus'])->name('invoice.payfast.status');

Route::post('/customer-pay-with-iyzipay', [IyziPayController::class, 'invoicepaywithiyzipay'])->name('customer.pay.with.iyzipay');
Route::post('iyzipay/callback/{invoice}/{amount}', [IyzipayController::class, 'getInvoiceiyzipayCallback'])->name('iyzipay.invoicepayment.callback');

Route::post('/customer-pay-with-sspay', [SspayController::class, 'invoicepaywithsspaypay'])->name('customer.pay.with.sspay');
Route::get('/customer/sspay/{invoice}/{amount}', [SspayController::class, 'getInvoicePaymentStatus'])->name('customer.sspay');

Route::post('/invoice-pay-with-paytab', [PaytabController::class, 'invoicePayWithpaytab'])->name('customer.pay.with.paytab');
Route::any('/invoice-paytab-success/{invoice}', [PaytabController::class, 'getInvoicePaymentStatus'])->name('invoice.paytab.success');

Route::any('invoice-with-benefit', [BenefitPaymentController::class, 'invoicepaywithbenefit'])->name('invoice.benefit.initiate');
Route::any('/invoice/benefit/{invoice_id}/{amount}', [BenefitPaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.benefit.call_back');

Route::post('invoice-with-cashfree', [CashfreeController::class, 'invoicepaywithcashfree'])->name('customer.pay.with.cashfree');
Route::any('invoice-with-cashfree/cashfree', [CashfreeController::class, 'getInvoicePaymentStatus'])->name('invoice.cashfreePayment.success');

Route::post('aamarpay/invoice', [AamarpayController::class, 'invoicepaywithaamarpay'])->name('customer.pay.with.aamarpay');
Route::any('aamarpay/invoice/success/{data}', [AamarpayController::class, 'getInvoicePaymentStatus'])->name('invoice.pay.aamarpay.success');

Route::post('/invoice-with-paytr', [PaytrController::class, 'invoicepaywithpaytr'])->name('customer.pay.with.paytr');
Route::get('/invoice/paytr/status', [PaytrController::class, 'getInvoicePaymentStatus'])->name('invoice.paytr');

Route::post('invoice-with-yookassa/', [YooKassaController::class, 'invoicePayWithYookassa'])->name('customer.with.yookassa');
Route::any('invoice-yookassa-status/', [YooKassaController::class, 'getInvociePaymentStatus'])->name('invoice.yookassa.status');

Route::any('invoice-with-midtrans/', [MidtransPaymentController::class, 'invoicePayWithMidtrans'])->name('customer.with.midtrans');
Route::any('invoice-midtrans-status/', [MidtransPaymentController::class, 'getInvociePaymentStatus'])->name('invoice.midtrans.status');

Route::any('/invoice-with-xendit', [XenditPaymentController::class, 'invoicePayWithXendit'])->name('customer.with.xendit');
Route::any('/invoice-xendit-status', [XenditPaymentController::class, 'getInvociePaymentStatus'])->name('invoice.xendit.status');

Route::post('/invoice-nepalste/payment', [NepalstePaymnetController::class, 'invoicePayWithnepalste'])->name('customer.with.nepalste');
Route::get('invoice-nepalste/status/',[NepalstePaymnetController::class,'invoiceGetNepalsteStatus'])->name('invoice.nepalste.status');
Route::get('invoice-nepalste/cancel/',[NepalstePaymnetController::class,'invoiceGetNepalsteCancel'])->name('invoice.nepalste.cancel');

Route::post('invoice-with-paiementpro/', [PaiementProController::class, 'invoicePayWithPaiementPro'])->name('customer.with.paiementpro');
Route::get('invoice-paiementpro-status/{invoice_id}', [PaiementProController::class, 'getInvociePaymentStatus'])->name('invoice.paiementpro.status');

Route::post('/invoice-with-cinetpay', [CinetPayController::class, 'invoicePayWithCinetPay'])->name('customer.with.cinetpay');
Route::post('/invoice/company/payment/return', [CinetPayController::class, 'invoiceCinetPayReturn'])->name('invoice.cinetpay.return');
Route::post('/invoice/company/payment/notify/', [CinetPayController::class, 'invoiceCinetPayNotify'])->name('invoice.cinetpay.notify');

Route::post('invoice-with-fedapay/', [FedapayController::class, 'invoicePayWithFedapay'])->name('customer.with.fedapay');
Route::get('invoice-fedapay-status/', [FedapayController::class, 'getInvociePaymentStatus'])->name('invoice.fedapay.status');

Route::post('invoice-with-payhere/', [PayHereController::class, 'invoicePayWithPayHere'])->name('customer.with.payhere');
Route::get('invoice-payhere-status/', [PayHereController::class, 'getInvociePaymentStatus'])->name('invoice.payhere.status');

Route::post('/invoice-pay-with/tap',[TapController::class,'invoicePayWithtap'])->name('invoice.pay.with.tap');
Route::get('/invoice/tap/{invoice_id}/{amount}',[TapController::class,'getInvoicePaymentStatus'])->name('invoice.tap');

Route::any('/invoice-pay-with-authorize-net', [AuthorizeNetController::class, 'invoicePayWithAuthorizeNet'])->name('invoice.pay.with.authorizenet');
Route::any('/invoice-get-authorizenet-status',[AuthorizeNetController::class,'getInvoicePaymentStatus'])->name('invoice.get.authorizenet.status');

Route::post('/invoice-pay-khalti',[KhaltiController::class,'getInvoicePaymentStatus'])->name('invoice.pay.with.khalti');

Route::any('/invoice-pay-easebuzz', [EasebuzzController::class, 'invoicePayWitheasebuzz'])->name('invoice.pay.with.easebuzz');
Route::match(['get','post'],'/invoice-easebuzz-payment-return', [EasebuzzController::class,'invoiceNotifyUrl'])->name('invoice.easebuzz.return');
Route::match(['get','post'],'invoice-easebuzz-payment-notify', [EasebuzzController::class,'invoiceReturnUrl'])->name('invoice.get.easebuzz.notify');

Route::post('/invoice-pay-ozow', [OzowPaymentController ::class, 'invoicePayWithOzow'])->name('invoice.pay.with.ozow');
Route::get('/invoice-pay-ozow/{invoice_id}/',[OzowPaymentController::class,'getInvoicePaymentStatus'])->name('invoice.ozow.status');




// cache
Route::get('/config-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('optimize:clear');
    return redirect()->back()->with('success', 'Cache Clear Successfully');
})->name('config.cache');


/***********************************************************************************************************************************************/

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
            'revalidate',
        ],
    ], function () {
        Route::get('support/{id}/reply', [SupportController::class, 'reply'])->name('support.reply');
        Route::post('support/{id}/reply', [SupportController::class, 'replyAnswer'])->name('support.reply.answer');
        Route::get('support/grid', [SupportController::class, 'grid'])->name('support.grid');
        Route::resource('support', SupportController::class);

    }
);
Route::resource('competencies', CompetenciesController::class)->middleware(['auth', 'XSS']);

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
            'revalidate',
        ],
    ], function () {
        Route::resource('performanceType', PerformanceTypeController::class);
    }
);

// ---------------------------------------------------------------------------------------------------------------------------

    Route::post('csv/import', [ImportController::class, 'fileImport'])->name('csv.import');
    Route::get('import/csv/modal/', [ImportController::class, 'fileImportModal'])->name('csv.import.modal');

//--------------------------------------------------------Import/Export Data Route-----------------------------------------------------------------

Route::get('export/productservice', [ProductServiceController::class, 'export'])->name('productservice.export');
Route::get('import/productservice/file', [ProductServiceController::class, 'importFile'])->name('productservice.file.import');
Route::post('productservice/import', [ProductServiceController::class, 'fileImport'])->name('productservice.import');
Route::get('import/productservice/modal', [ProductServiceController::class, 'fileImportModal'])->name('productservice.import.modal');
Route::post('import/productservice', [ProductServiceController::class, 'productserviceImportdata'])->name('productservice.import.data');

Route::get('export/visa', [VisaController::class, 'export'])->name('visa.export');
Route::get('import/visa/file', [VisaController::class, 'importFile'])->name('visa.file.import');
Route::post('visa/import', [VisaController::class, 'fileImport'])->name('visa.import');
Route::get('import/visa/modal', [VisaController::class, 'fileImportModal'])->name('visa.import.modal');
Route::post('import/visa', [VisaController::class, 'visaImportdata'])->name('visa.import.data');

Route::get('export/customer', [CustomerController::class, 'export'])->name('customer.export');
Route::get('import/customer/file', [CustomerController::class, 'importFile'])->name('customer.file.import');
// Route::post('import/customer', [CustomerController::class, 'import'])->name('customer.import');
Route::post('import/customer', [CustomerController::class, 'customerImportdata'])->name('customer.import.data');

Route::get('export/vender', [VenderController::class, 'export'])->name('vender.export');
Route::get('import/vender/file', [VenderController::class, 'importFile'])->name('vender.file.import');
// Route::post('import/vender', [VenderController::class, 'import'])->name('vender.import');
Route::post('import/vender', [VenderController::class, 'venderImportdata'])->name('vender.import.data');


Route::get('export/invoice', [InvoiceController::class, 'export'])->name('invoice.export');
Route::get('export/proposal', [ProposalController::class, 'export'])->name('proposal.export');
Route::get('export/bill', [BillController::class, 'export'])->name('bill.export');

Route::get('import/attendance/file', [AttendanceEmployeeController::class, 'importFile'])->name('attendance.file.import');
// Route::post('import/attendance', [AttendanceEmployeeController::class, 'import'])->name('attendance.import');
Route::post('import/attendance', [AttendanceEmployeeController::class, 'attendanceImportdata'])->name('attendance.import.data');

Route::get('export/employee', [EmployeeController::class, 'export'])->name('employee.export');
Route::get('import/employee/file', [EmployeeController::class, 'importFile'])->name('employee.file.import');
Route::post('employee/import', [EmployeeController::class, 'fileImport'])->name('employee.import');
Route::get('import/employee/modal', [EmployeeController::class, 'fileImportModal'])->name('employee.import.modal');
Route::post('import/employee', [EmployeeController::class, 'employeeImportdata'])->name('employee.import.data');

Route::get('export/transaction', [TransactionController::class, 'export'])->name('transaction.export');
Route::get('export/accountstatement', [ReportController::class, 'export'])->name('accountstatement.export');
Route::get('export/productstock', [ReportController::class, 'stock_export'])->name('productstock.export');
Route::get('export/payroll', [ReportController::class, 'PayrollReportExport'])->name('payroll.export');

Route::post('export/payslip', [PaySlipController::class, 'export'])->name('payslip.export');

//=================================== Time-Tracker======================================================================
Route::post('stop-tracker', [DashboardController::class, 'stopTracker'])->name('stop.tracker')->middleware(['auth', 'XSS']);
Route::get('time-tracker', [TimeTrackerController::class, 'index'])->name('time.tracker')->middleware(['auth', 'XSS']);
Route::delete('tracker/{tid}/destroy', [TimeTrackerController::class, 'Destroy'])->name('tracker.destroy');
Route::post('tracker/image-view', [TimeTrackerController::class, 'getTrackerImages'])->name('tracker.image.view');
Route::delete('tracker/image-remove', [TimeTrackerController::class, 'removeTrackerImages'])->name('tracker.image.remove');
Route::get('projects/time-tracker/{id}', [ProjectController::class, 'tracker'])->name('projecttime.tracker')->middleware(['auth', 'XSS']);

//=================================== Zoom Meeting ======================================================================
Route::resource('zoom-meeting', ZoomMeetingController::class)->middleware(['auth', 'XSS']);
Route::any('/zoom-meeting/projects/select/{bid}', [ZoomMeetingController::class, 'projectwiseuser'])->name('zoom-meeting.projects.select');
Route::get('zoom-meeting-calender', [ZoomMeetingController::class, 'calender'])->name('zoom-meeting.calender')->middleware(['auth', 'XSS']);

// ------------------------------------- PaymentWall ------------------------------

Route::post('/paymentwalls', [PaymentWallPaymentController::class, 'paymentwall'])->name('plan.paymentwallpayment')->middleware(['XSS']);
Route::post('/plan-pay-with-paymentwall/{plan}', [PaymentWallPaymentController::class, 'planPayWithPaymentWall'])->name('plan.pay.with.paymentwall')->middleware(['auth', 'XSS']);
Route::get('/plan/{flag}', [PaymentWallPaymentController::class, 'planeerror'])->name('error.plan.show');

Route::post('/paymentwall', [PaymentWallPaymentController::class, 'invoicepaymentwall'])->name('invoice.paymentwallpayment')->middleware(['XSS']);
Route::post('/invoice-pay-with-paymentwall/{invoice}', [PaymentWallPaymentController::class, 'invoicePayWithPaymentwall'])->name('invoice.pay.with.paymentwall')->middleware(['XSS']);

Route::get('/invoices/{flag}/{invoice}', [PaymentWallPaymentController::class, 'invoiceerror'])->name('error.invoice.show');

Route::get('quotation/items', [QuotationController::class, 'items'])->name('quotation.items');
Route::resource('quotation', QuotationController::class)->middleware(['auth', 'XSS', 'revalidate']);
Route::any('quotation/create/{cid}', [QuotationController::class, 'quotationCreate'])->name('quotations.create')->middleware(['auth', 'XSS', 'revalidate']);
Route::post('quotation/product', [QuotationController::class, 'product'])->name('quotation.product');
Route::post('quotation/product/destroy', [QuotationController::class, 'productDestroy'])->name('quotation.product.destroy');
Route::get('quotation/convert/{id}', [QuotationController::class, 'convert'])->name('quotation.convert');
Route::post('quantity/product', [QuotationController::class, 'productQuantity'])->name('product.quantity');
Route::post('/quotation/template/setting', [QuotationController::class, 'saveQuotationTemplateSettings'])->name('quotation.template.setting');
Route::get('quotation/preview/{template}/{color}', [QuotationController::class, 'previewQuotation'])->name('quotation.preview')->middleware(['auth', 'XSS']);
Route::get('printview/quotation', [QuotationController::class, 'printView'])->name('quotation.printview');
Route::get('quotation/pdf/{id}', [QuotationController::class, 'quotation'])->name('quotation.pdf')->middleware(['auth', 'XSS', 'revalidate']);

// ------------------------------------- POS System ------------------------------
Route::resource('warehouse', WarehouseController::class)->middleware(['auth', 'XSS', 'revalidate']);

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
            'revalidate',
        ],
    ], function () {
        Route::get('purchase/items', [PurchaseController::class, 'items'])->name('purchase.items');
        Route::resource('purchase', PurchaseController::class);

//    Route::get('/bill/{id}/', 'PurchaseController@purchaseLink')->name('purchase.link.copy');
        Route::get('purchase/{id}/payment', [PurchaseController::class, 'payment'])->name('purchase.payment');
        Route::post('purchase/{id}/payments', [PurchaseController::class, 'createPayment'])->name('purchase.payments');
        Route::post('purchase/{id}/payment/{pid}/destroy', [PurchaseController::class, 'paymentDestroy'])->name('purchase.payment.destroy');
        Route::post('purchase/product/destroy', [PurchaseController::class, 'productDestroy'])->name('purchase.product.destroy');
        Route::post('purchase/vender', [PurchaseController::class, 'vender'])->name('purchase.vender');
        Route::post('purchase/product', [PurchaseController::class, 'product'])->name('purchase.product');
        Route::get('purchase/{id}/sent', [PurchaseController::class, 'sent'])->name('purchase.sent');
        Route::get('purchase/{id}/resent', [PurchaseController::class, 'resent'])->name('purchase.resent');

    }

);

Route::get('pos-print-setting', [SystemController::class, 'posPrintIndex'])->name('pos.print.setting')->middleware(['auth', 'XSS']);
Route::get('purchase/preview/{template}/{color}', [PurchaseController::class, 'previewPurchase'])->name('purchase.preview')->middleware(['auth', 'XSS']);
Route::get('pos/preview/{template}/{color}', [PosController::class, 'previewPos'])->name('pos.preview')->middleware(['auth', 'XSS']);

Route::post('/purchase/template/setting', [PurchaseController::class, 'savePurchaseTemplateSettings'])->name('purchase.template.setting');
Route::post('/pos/template/setting', [PosController::class, 'savePosTemplateSettings'])->name('pos.template.setting');

Route::get('purchase/pdf/{id}', [PurchaseController::class, 'purchase'])->name('purchase.pdf')->middleware(['auth', 'XSS', 'revalidate']);
Route::get('pos/pdf/{id}', [PosController::class, 'pos'])->name('pos.pdf')->middleware(['auth', 'XSS', 'revalidate']);
Route::get('pos/data/store', [PosController::class, 'store'])->name('pos.data.store')->middleware(['auth', 'XSS', 'revalidate']);
//for pos print
Route::get('printview/pos', [PosController::class, 'printView'])->name('pos.printview')->middleware(['auth', 'XSS', 'revalidate']);

//pos barcode
Route::get('barcode/pos', [PosController::class, 'barcode'])->name('pos.barcode')->middleware(['auth', 'XSS']);
Route::get('setting/pos', [PosController::class, 'setting'])->name('pos.setting')->middleware(['auth', 'XSS']);
Route::post('barcode/settings', [PosController::class, 'BarcodesettingStore'])->name('barcode.setting');
Route::get('print/pos', [PosController::class, 'printBarcode'])->name('pos.print')->middleware(['auth', 'XSS']);
Route::post('pos/getproduct', [PosController::class, 'getproduct'])->name('pos.getproduct')->middleware(['auth', 'XSS']);
Route::any('pos-receipt', [PosController::class, 'receipt'])->name('pos.receipt')->middleware(['auth', 'XSS']);
Route::post('/cartdiscount', [PosController::class, 'cartdiscount'])->name('cartdiscount')->middleware(['auth', 'XSS']);

Route::resource('pos', PosController::class)->middleware(['auth', 'XSS', 'revalidate']);
Route::any('report/pos', [PosController::class, 'report'])->name('pos.report')->middleware(['auth', 'XSS']);
Route::get('{cid?}/pos', [PosController::class, 'index'])->name('poses.index');

Route::get('product-categories', [ProductServiceCategoryController::class, 'getProductCategories'])->name('product.categories')->middleware(['auth', 'XSS']);
Route::get('add-to-cart/{id}/{session}', [ProductServiceController::class, 'addToCart'])->middleware(['auth', 'XSS']);
Route::patch('update-cart', [ProductServiceController::class, 'updateCart'])->middleware(['auth', 'XSS']);
Route::delete('remove-from-cart', [ProductServiceController::class, 'removeFromCart'])->middleware(['auth', 'XSS']);

Route::get('name-search-products', [ProductServiceCategoryController::class, 'searchProductsByName'])->name('name.search.products')->middleware(['auth', 'XSS']);
Route::get('search-products', [ProductServiceController::class, 'searchProducts'])->name('search.products')->middleware(['auth', 'XSS']);
Route::get('search-products-sku', [ProductServiceController::class, 'searchProductsSku'])->name('search.products.sku')->middleware(['auth', 'XSS']);

//warehouse-transfer
Route::resource('warehouse-transfer', WarehouseTransferController::class)->middleware(['auth', 'XSS', 'revalidate']);
Route::post('warehouse-transfer/getproduct', [WarehouseTransferController::class, 'getproduct'])->name('warehouse-transfer.getproduct')->middleware(['auth', 'XSS']);
Route::post('warehouse-transfer/getquantity', [WarehouseTransferController::class, 'getquantity'])->name('warehouse-transfer.getquantity')->middleware(['auth', 'XSS']);

//BiometricAttendance
Route::post('biometric-setting', [SystemController::class, 'BiometricSetting'])->name('biometric-settings.store')->middleware(['auth', 'XSS']);
Route::resource('/biometric-attendance', BiometricAttendanceController::class)->middleware(
    [
         'auth',
        'XSS',
    ]
);
 Route::post('/biometric-attendance/sync/{start_date?}/{end_date?}', [BiometricAttendanceController::class, 'AllSync'])->middleware(['auth'])->name('biometric-attendance.allsync');

//Storage Setting
Route::post('storage-settings', [SystemController::class, 'storageSettingStore'])->name('storage.setting.store')->middleware(['auth', 'XSS']);

//appricalStar

Route::post('/appraisals', [AppraisalController::class, 'empByStar'])->name('empByStar')->middleware(['auth', 'XSS']);
Route::post('/appraisals1', [AppraisalController::class, 'empByStar1'])->name('empByStar1')->middleware(['auth', 'XSS']);
Route::post('/getemployee', [AppraisalController::class, 'getemployee'])->name('getemployee');

//offer Letter

Route::post('setting/offerlatter/{lang?}', [SystemController::class, 'offerletterupdate'])->name('offerlatter.update');
Route::get('setting/offerlatter', [SystemController::class, 'companyIndex'])->name('get.offerlatter.language')->middleware(['XSS']);
Route::get('job-onboard/pdf/{id}', [JobApplicationController::class, 'offerletterPdf'])->name('offerlatter.download.pdf');
Route::get('job-onboard/doc/{id}', [JobApplicationController::class, 'offerletterDoc'])->name('offerlatter.download.doc');

//joining Letter
Route::post('setting/joiningletter/{lang?}', [SystemController::class, 'joiningletterupdate'])->name('joiningletter.update');
Route::get('setting/joiningletter/', [SystemController::class, 'companyIndex'])->name('get.joiningletter.language')->middleware(['XSS']);
Route::get('employee/pdf/{id}', [EmployeeController::class, 'joiningletterPdf'])->name('joiningletter.download.pdf');
Route::get('employee/doc/{id}', [EmployeeController::class, 'joiningletterDoc'])->name('joininglatter.download.doc');

//Experience Certificate

Route::post('setting/exp/{lang?}', [SystemController::class, 'experienceCertificateupdate'])->name('experiencecertificate.update');
Route::get('setting/exp', [SystemController::class, 'companyIndex'])->name('get.experiencecertificate.language')->middleware(['XSS']);
Route::get('employee/exppdf/{id}', [EmployeeController::class, 'ExpCertificatePdf'])->name('exp.download.pdf');
Route::get('employee/expdoc/{id}', [EmployeeController::class, 'ExpCertificateDoc'])->name('exp.download.doc');

//NOC

Route::post('setting/noc/{lang?}', [SystemController::class, 'NOCupdate'])->name('noc.update');
Route::get('setting/noc', [SystemController::class, 'companyIndex'])->name('get.noc.language')->middleware(['XSS']);
Route::get('employee/nocpdf/{id}', [EmployeeController::class, 'NocPdf'])->name('noc.download.pdf');
Route::get('employee/nocdoc/{id}', [EmployeeController::class, 'NocDoc'])->name('noc.download.doc');

// Project Reports

Route::resource('/project_report', ProjectReportController::class)->middleware(['auth', 'XSS']);
Route::post('/project_report_data', [ProjectReportController::class, 'ajax_data'])->name('projects.ajax')->middleware(['auth', 'XSS']);
Route::post('/project_report/tasks/{id}', [ProjectReportController::class, 'ajax_tasks_report'])->name('tasks.report.ajaxdata')->middleware(['auth', 'XSS']);
Route::get('export/task_report/{id}', [ProjectReportController::class, 'export'])->name('project_report.export');

//project copy module
Route::get('/project/copy/{id}', [ProjectController::class, 'copyproject'])->name('project.copy')->middleware(['auth', 'XSS']);
Route::post('/project/copy/store/{id}', [ProjectController::class, 'copyprojectstore'])->name('project.copy.store')->middleware(['auth', 'XSS']);

//Google Calendar
Route::any('event/get_event_data', [EventController::class, 'get_event_data'])->name('event.get_event_data')->middleware(['auth', 'XSS']);

Route::post('setting/google-calender', [SystemController::class, 'saveGoogleCalenderSettings'])->name('google.calender.settings');
Route::any('holiday/get_holiday_data', [HolidayController::class, 'get_holiday_data'])->name('holiday.get_holiday_data')->middleware(['auth', 'XSS']);
Route::any('interview-schedule/get_interview_data', [InterviewScheduleController::class, 'get_interview_data'])->name('holiday.get_interview_data')->middleware(['auth', 'XSS']);
Route::post('calendar/get_task_data', [ProjectTaskController::class, 'get_task_data'])->name('task.calendar.get_task_data')->middleware(['auth', 'XSS']);
Route::any('zoom-meeting/get_zoom_meeting_data', [ZoomMeetingController::class, 'get_zoom_meeting_data'])->name('zoom-meeting.get_zoom_meeting_data')->middleware(['auth', 'XSS']);

Route::any('meeting/get_meeting_data', [MeetingController::class, 'get_meeting_data'])->name('meeting.get_meeting_data')->middleware(['auth', 'XSS']);
Route::get('meeting-calender', [MeetingController::class, 'calender'])->name('meeting.calender')->middleware(['auth', 'XSS']);

//branch wise department get in attendance report
Route::post('reports-monthly-attendance/getdepartment', [ReportController::class, 'getdepartment'])->name('report.attendance.getdepartment')->middleware(['auth', 'XSS']);
Route::post('reports-monthly-attendance/getemployee', [ReportController::class, 'getemployee'])->name('report.attendance.getemployee')->middleware(['auth', 'XSS']);

//shared project & copy link
Route::get('/projects/copylink/{id}', [ProjectController::class, 'projectCopyLink'])->name('projects.copylink');

Route::any('/projects/link/{id}/{lang?}', [ProjectController::class, 'projectlink'])->name('projects.link')->middleware(['XSS']);

Route::get('timesheet-table-view', [TimesheetController::class, 'filterTimesheetTableView'])->name('filter.timesheet.table.view')->middleware(['auth', 'XSS']);

Route::any('/projects/copy/link/{id}', [ProjectController::class, 'copylinksetting'])->name('projects.copy.link');
Route::any('/projects{id}/settingcreate', [ProjectController::class, 'copylink_setting_create'])->name('projects.copylink.setting.create');
Route::get('/shareproject/{lang?}', [ProjectController::class, 'shareproject'])->name('shareproject');

//User Log
Route::get('/userlogs', [UserController::class, 'userLog'])->name('user.userlog')->middleware(['auth', 'XSS']);
Route::get('userlogs/{id}', [UserController::class, 'userLogView'])->name('user.userlogview')->middleware(['auth', 'XSS']);
Route::delete('userlogs/{id}', [UserController::class, 'userLogDestroy'])->name('user.userlogdestroy')->middleware(['auth', 'XSS']);
Route::get('user-login/{id}', [UserController::class, 'LoginManage'])->name('users.login');

Route::any('/cookie-consent', [SystemController::class, 'CookieConsent'])->name('cookie-consent');

//notification Template
Route::get('notification_templates/{id?}/{lang?}', [NotificationTemplatesController::class, 'index'])->name('notification_templates.index')->middleware(['auth', 'XSS']);
Route::get('notification-templates-lang/{id}/{lang?}', [NotificationTemplatesController::class, 'manageNotificationLang'])->name('manage.notification.language')->middleware(['auth', 'XSS']);
Route::resource('notification-templates', NotificationTemplatesController::class)->middleware(['auth', 'XSS']);

//Proposal/Invoice/Bill/Purchase/POS - footer notes
Route::post('system-settings/note', [SystemController::class, 'footerNoteStore'])->name('system.settings.footernote')->middleware(['auth', 'XSS']);

//AI module
Route::post('chatgpt-settings', [SystemController::class, 'chatgptSetting'])->name('chatgpt.settings');
Route::get('generate/{template_name}', [AiTemplateController::class, 'create'])->name('generate');
Route::post('generate/keywords/{id}', [AiTemplateController::class, 'getKeywords'])->name('generate.keywords');
Route::post('generate/response', [AiTemplateController::class, 'AiGenerate'])->name('generate.response');

//AI module for grammar check
Route::get('grammar/{template}', [AiTemplateController::class, 'grammar'])->name('grammar')->middleware(['auth', 'XSS']);
Route::post('grammar/response', [AiTemplateController::class, 'grammarProcess'])->name('grammar.response')->middleware(['auth', 'XSS']);

//IP-Restrication settings
Route::get('create/ip', [SystemController::class, 'createIp'])->name('create.ip')->middleware(['auth', 'XSS']);
Route::post('create/ip', [SystemController::class, 'storeIp'])->name('store.ip')->middleware(['auth', 'XSS']);
Route::get('edit/ip/{id}', [SystemController::class, 'editIp'])->name('edit.ip')->middleware(['auth', 'XSS']);
Route::post('edit/ip/{id}', [SystemController::class, 'updateIp'])->name('update.ip')->middleware(['auth', 'XSS']);
Route::delete('destroy/ip/{id}', [SystemController::class, 'destroyIp'])->name('destroy.ip')->middleware(['auth', 'XSS']);

//lang enable / disable
Route::post('disable-language', [LanguageController::class, 'disableLang'])->name('disablelanguage')->middleware(['auth', 'XSS']);

//Expense Module
Route::get('expense/pdf/{id}', [ExpenseController::class, 'expense'])->name('expense.pdf')->middleware(['XSS', 'revalidate']);
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
            'revalidate',
        ],
    ], function () {
        Route::any('expense/customer', [ExpenseController::class, 'customer'])->name('expense.customer');
        Route::post('expense/vender', [ExpenseController::class, 'vender'])->name('expense.vender');
        Route::post('expense/employee', [ExpenseController::class, 'employee'])->name('expense.employee');

        Route::post('expense/product/destroy', [ExpenseController::class, 'productDestroy'])->name('expense.product.destroy');

        Route::post('expense/product', [ExpenseController::class, 'product'])->name('expense.product');
        Route::get('expense/{id}/payment', [ExpenseController::class, 'payment'])->name('expense.payment');
        Route::get('expense/items', [ExpenseController::class, 'items'])->name('expense.items');

        Route::resource('expense', ExpenseController::class);

    }
);

Route::get('payslip/payslipPdf/{id}/{month}', [PaySlipController::class, 'payslipPdf'])->name('payslip.payslipPdf')->middleware(['XSS']);
