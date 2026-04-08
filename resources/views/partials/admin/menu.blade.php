@php
    $logo = \App\Models\Utility::get_file('uploads/logo');

    $company_logo = Utility::getValByName('company_logo_dark');
    $company_logos = Utility::getValByName('company_logo_light');
    $setting = \App\Models\Utility::settings();
    $emailTemplate = \App\Models\EmailTemplate::first();

    // Check if it's a Customer login
    $isCustomerLogin = session('is_customer_login', false);
    $currentUser = $isCustomerLogin ? Auth::guard('customer')->user() : Auth::user();
    $lang = $currentUser ? ($currentUser->lang ?? 'en') : 'en';
    $show_dashboard = \App\Models\User::show_dashboard();
@endphp

<style>
.blue-white-icon {
  color: #2196F3;
  position: relative;
}
.blue-white-icon::before {
  content: "";
  position: absolute;
  width: 60%;
  height: 40%;
  background-color: white;
  top: 20%;
  left: 20%;
  border-radius: 2px;
}
</style>

@if (isset($setting['cust_theme_bg']) && $setting['cust_theme_bg'] == 'on')
    <nav class="dash-sidebar light-sidebar transprent-bg">
    @else
        <nav class="dash-sidebar light-sidebar">
@endif
<div class="navbar-wrapper">
    <div class="m-header main-logo">
        <a href="#" class="b-brand">
            @if ($setting['cust_darklayout'] && $setting['cust_darklayout'] == 'on')
                <img src="{{ $logo . '/' . (isset($company_logos) && !empty($company_logos) ? $company_logos : 'logo-dark.png') . '?' . time() }}"
                    alt="{{ config('app.name', 'Noble') }}" class="logo logo-lg">
            @else
                <img src="{{ $logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo-light.png') . '?' . time() }}"
                    alt="{{ config('app.name', 'Noble') }}" class="logo logo-lg">
            @endif
        </a>
    </div>
    <div class="navbar-content">
        @if ($isCustomerLogin)
            @php
                $customer = Auth::guard('customer')->user();
            @endphp
            @if($customer && $customer->type == 'Travel Agency')
                {{-- Customer (Travel Agency) Menu: Only Account System > Sales > Ticket --}}
                <ul class="dash-navbar">
                    <li class="dash-item dash-hasmenu {{ Request::segment(1) == 'dashboard' || Request::segment(1) == null || Request::segment(1) == 'account-dashboard' ? ' active' : '' }}">
                        <a href="{{ route('dashboard') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-home"></i></span><span
                                class="dash-mtext">{{ __('Dashboard') }}</span>
                        </a>
                    </li>
                    <li class="dash-item dash-hasmenu {{ Request::segment(1) == 'report' || Request::segment(1) == null || Request::segment(1) == 'account-dashboard' ? ' active' : '' }}">
                        <a href="{{ route('report') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-home"></i></span><span
                                class="dash-mtext">{{ __('Report') }}</span>
                        </a>
                    </li>

                    @if(true)
                        <li
                            class="dash-item dash-hasmenu {{ Request::segment(1) == 'invoice' ? ' active dash-trigger' : '' }}">
                            <a href="#!" class="dash-link"><span class="dash-micon"><i
                                        class="ti ti-box"></i></span><span
                                    class="dash-mtext">{{ __('Accounting System ') }}</span><span
                                    class="dash-arrow">
                                    <i data-feather="chevron-right"></i></span>
                            </a>
                            <ul class="dash-submenu">
                                <li
                                    class="dash-item dash-hasmenu {{ Request::segment(1) == 'invoice' ? 'active dash-trigger' : '' }}">
                                    <a class="dash-link" href="#">{{ __('Sales') }}<span
                                            class="dash-arrow"><i data-feather="chevron-right"></i></span></a>
                                    <ul class="dash-submenu">
                                        <li
                                            class="dash-item {{ Request::route()->getName() == 'invoice.index' || Request::route()->getName() == 'invoice.create' || Request::route()->getName() == 'invoice.edit' || Request::route()->getName() == 'invoice.show' ? ' active' : '' }}">
                                            <a class="dash-link"
                                                href="{{ route('invoice.index') }}">{{ __('Ticket') }}</a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                    @endif
                </ul>
            @endif
        @elseif (\Auth::user()->type != 'client')
            <ul class="dash-navbar">


                <!--------------------- Start Dashboard ----------------------------------->

                @if (Gate::check('show hrm dashboard') ||
                        Gate::check('show project dashboard') ||
                        Gate::check('show account dashboard') ||
                        Gate::check('show crm dashboard') ||
                        Gate::check('show pos dashboard'))
                    <li
                        class="dash-item dash-hasmenu
                             {{ Request::segment(1) == null ||
                                Request::segment(1) == 'account-dashboard'
                                    ? 'active dash-trigger'
                                    : '' }}">
                        <a href="{{ route('dashboard') }}" class="dash-link"><span class="dash-micon"><i
                                    class="ti ti-home"></i></span><span class="dash-mtext">{{ __('Dashboard') }}</span></a>

                    </li>
                @endif

                    <!--------------------- End Dashboard ----------------------------------->

                <!--------------------- Start Report ----------------------------------->

                @if (Gate::check('show hrm dashboard') ||
                        Gate::check('show project dashboard') ||
                        Gate::check('show account dashboard') ||
                        Gate::check('show crm dashboard') ||
                        Gate::check('show pos dashboard'))
                    <li
                        class="dash-item dash-hasmenu
                             {{ Request::segment(1) == null ||
                                Request::segment(1) == 'hrm-dashboard' ||
                                Request::segment(1) == 'crm-dashboard' ||
                                Request::segment(1) == 'project-dashboard' ||
                                Request::segment(1) == 'account-statement-report' ||
                                Request::segment(1) == 'invoice-summary' ||
                                Request::segment(1) == 'sales' ||
                                Request::segment(1) == 'receivables' ||
                                Request::segment(1) == 'payables' ||
                                Request::segment(1) == 'bill-summary' ||
                                Request::segment(1) == 'product-stock-report' ||
                                Request::segment(1) == 'transaction' ||
                                Request::segment(1) == 'income-summary' ||
                                Request::segment(1) == 'expense-summary' ||
                                Request::segment(1) == 'income-vs-expense-summary' ||
                                Request::segment(1) == 'tax-summary' ||
                                Request::segment(1) == 'income report' ||
                                Request::segment(1) == 'report' ||
                                Request::segment(1) == 'reports-monthly-cashflow' ||
                                Request::segment(1) == 'reports-quarterly-cashflow' ||
                                Request::segment(1) == 'reports-payroll' ||
                                Request::segment(1) == 'report-leave' ||
                                Request::segment(1) == 'reports-monthly-attendance' ||
                                Request::segment(1) == 'reports-lead' ||
                                Request::segment(1) == 'reports-deal' ||
                                Request::segment(1) == 'pos-dashboard' ||
                                Request::segment(1) == 'reports-warehouse' ||
                                Request::segment(1) == 'reports-daily-purchase' ||
                                Request::segment(1) == 'reports-monthly-purchase' ||
                                Request::segment(1) == 'reports-daily-pos' ||
                                Request::segment(1) == 'reports-monthly-pos' ||
                                Request::segment(1) == 'reports-pos-vs-purchase'
                                    ? 'active dash-trigger'
                                    : '' }}">
                        <a href="#!" class="dash-link"><span class="dash-micon"><i
                                    class="fas fa-file-alt"></i></span><span class="dash-mtext">{{ __('Reports') }}</span>
                            <span class="dash-arrow"><i data-feather="chevron-right"></i></span></a>

                        <ul class="dash-submenu">
                            @if ($show_dashboard == 1 && Gate::check('show account dashboard'))
                                <li
                                    class="dash-item dash-hasmenu {{ Request::segment(1) == null || Request::segment(1) == 'report' || Request::segment(1) == 'reports-monthly-cashflow' || Request::segment(1) == 'reports-quarterly-cashflow' ? ' active dash-trigger' : '' }}">
                                    <a class="dash-link" href="#">{{ __('Accounting ') }}<span
                                            class="dash-arrow"><i data-feather="chevron-right"></i></span></a>
                                    <ul class="dash-submenu">
                                       {{--  @can('show account dashboard')
                                            <li
                                                class="dash-item {{ Request::segment(1) == null || Request::segment(1) == 'account-dashboard' ? ' active' : '' }}">
                                                <a class="dash-link"
                                                    href="{{ route('dashboard') }}">{{ __(' Overview') }}</a>
                                            </li>
                                        @endcan --}}
                                        @if (Gate::check('income report') ||
                                                Gate::check('expense report') ||
                                                Gate::check('income vs expense report') ||
                                                Gate::check('tax report') ||
                                                Gate::check('loss & profit report') ||
                                                Gate::check('invoice report') ||
                                                Gate::check('bill report') ||
                                                Gate::check('stock report') ||
                                                Gate::check('manage transaction') ||
                                                Gate::check('statement report'))
                                            {{-- <li

                                                class="dash-item {{ Request::route()->getName() == 'report.account.statement' ? ' active' : '' }}">
                                                            <a class="dash-link"
                                                                href="{{ route('report.account.statement') }}">{{ __('Account Statement') }}</a>
                                                        </li> --}}
                                                    @endcan
                                                    {{-- @can('invoice report')
                                                        <li
                                                            class="dash-item {{ Request::route()->getName() == 'report.invoice.summary' ? ' active' : '' }}">
                                                            <a class="dash-link"
                                                                href="{{ route('report.invoice.summary') }}">{{ __('Invoice Summary') }}</a>
                                                        </li>
                                                    @endcan --}}
                                                    <li
                                                        class="dash-item {{ Request::route()->getName() == 'report.sales' ? ' active' : '' }}">
                                                        <a class="dash-link"
                                                            href="{{ route('report.sales') }}">{{ __('Sales Report') }}</a>
                                                    </li>
                                                    <li
                                                        class="dash-item {{ Request::route()->getName() == 'report.receivables' ? ' active' : '' }}">
                                                        <a class="dash-link"
                                                            href="{{ route('report.receivables') }}">{{ __('Accounts Receivable') }}</a>
                                                    </li>
                                                    <li
                                                        class="dash-item {{ Request::route()->getName() == 'report.payables' ? ' active' : '' }}">
                                                        <a class="dash-link"
                                                            href="{{ route('report.payables') }}">{{ __('Accounts Payable') }}</a>
                                                    </li>
                                                   {{-- @can('bill report')
                                                        <li
                                                            class="dash-item {{ Request::route()->getName() == 'report.bill.summary' ? ' active' : '' }}">
                                                            <a class="dash-link"
                                                                href="{{ route('report.bill.summary') }}">{{ __('Bill Summary') }}</a>
                                                        </li>
                                                    @endcan

                                                     @can('stock report')
                                                        <li
                                                            class="dash-item {{ Request::route()->getName() == 'report.product.stock.report' ? ' active' : '' }}">
                                                            <a href="{{ route('report.product.stock.report') }}"
                                                                class="dash-link">{{ __('Product Stock') }}</a>
                                                        </li>
                                                    @endcan
                                                    @can('loss & profit report')
                                                        <li
                                                            class="dash-item {{ request()->is('reports-monthly-cashflow') || request()->is('reports-quarterly-cashflow') ? 'active' : '' }}">
                                                            <a class="dash-link"
                                                                href="{{ route('report.monthly.cashflow') }}">{{ __('Cash Flow') }}</a>
                                                        </li>
                                                    @endcan --}}
                                                    @can('manage transaction')
                                                        <li
                                                            class="dash-item {{ Request::route()->getName() == 'transaction.index' || Request::route()->getName() == 'transfer.create' || Request::route()->getName() == 'transaction.edit' ? ' active' : '' }}">
                                                            <a class="dash-link"
                                                                href="{{ route('transaction.index') }}">{{ __('Bank Report') }}</a>
                                                        </li>
                                                    @endcan
                                                    {{-- @can('income report')
                                                        <li
                                                            class="dash-item {{ Request::route()->getName() == 'report.income.summary' ? ' active' : '' }}">
                                                            <a class="dash-link"
                                                                href="{{ route('report.income.summary') }}">{{ __('Income Summary') }}</a>
                                                        </li>
                                                    @endcan
                                                    @can('expense report')
                                                        <li
                                                            class="dash-item {{ Request::route()->getName() == 'report.expense.summary' ? ' active' : '' }}">
                                                            <a class="dash-link"
                                                                href="{{ route('report.expense.summary') }}">{{ __('Expense Summary') }}</a>
                                                        </li>
                                                    @endcan
                                                    @can('income vs expense report')
                                                        <li
                                                            class="dash-item {{ Request::route()->getName() == 'report.income.vs.expense.summary' ? ' active' : '' }}">
                                                            <a class="dash-link"
                                                                href="{{ route('report.income.vs.expense.summary') }}">{{ __('Income VS Expense') }}</a>
                                                        </li>
                                                    @endcan
                                                    @can('tax report')
                                                        <li
                                                            class="dash-item {{ Request::route()->getName() == 'report.tax.summary' ? ' active' : '' }}">
                                                            <a class="dash-link"
                                                                href="{{ route('report.tax.summary') }}">{{ __('Tax Summary') }}</a>
                                                        </li>
                                                    @endcan --}}
                                                </ul>
                                            </li>

                                </li>
                            @endif

                            {{-- @if ($show_dashboard == 1)
                                @can('show hrm dashboard')
                                    <li class="dash-item dash-hasmenu {{ Request::segment(1) == 'hrm-dashboard' || Request::segment(1) == 'reports-payroll' || Request::segment(1) == 'report-leave' || Request::segment(1) == 'reports-monthly-attendance' ? ' active dash-trigger' : '' }}">
                                        <a class="dash-link" href="#">{{ __('HRM ') }}<span class="dash-arrow"><i
                                                    data-feather="chevron-right"></i></span></a>
                                        <ul class="dash-submenu">
                                            <li
                                                class="dash-item {{ \Request::route()->getName() == 'hrm.dashboard' ? ' active' : '' }}">
                                                <a class="dash-link"
                                                    href="{{ route('hrm.dashboard') }}">{{ __(' Overview') }}</a>
                                            </li>
                                            @can('manage report')
                                                <li class="dash-item dash-hasmenu {{ Request::segment(1) == 'reports-monthly-attendance' || Request::segment(1) == 'report-leave' || Request::segment(1) == 'reports-payroll' ? 'active dash-trigger' : '' }}"
                                                    href="#hr-report" data-toggle="collapse" role="button"
                                                    aria-expanded="{{ Request::segment(1) == 'reports-monthly-attendance' || Request::segment(1) == 'report-leave' || Request::segment(1) == 'reports-payroll' ? 'true' : 'false' }}">
                                                    <a class="dash-link" href="#">{{ __('Reports') }}<span
                                                            class="dash-arrow"><i data-feather="chevron-right"></i></span></a>
                                                    <ul class="dash-submenu">
                                                        <li
                                                            class="dash-item {{ request()->is('reports-payroll') ? 'active' : '' }}">
                                                            <a class="dash-link"
                                                                href="{{ route('report.payroll') }}">{{ __('Payroll') }}</a>
                                                        </li>
                                                        <li
                                                            class="dash-item {{ request()->is('report-leave') ? 'active' : '' }}">
                                                            <a class="dash-link"
                                                                href="{{ route('report.leave') }}">{{ __('Leave') }}</a>
                                                        </li>
                                                        <li
                                                            class="dash-item {{ request()->is('reports-monthly-attendance') ? 'active' : '' }}">
                                                            <a class="dash-link"
                                                                href="{{ route('report.monthly.attendance') }}">{{ __('Monthly Attendance') }}</a>
                                                        </li>

                                                    </ul>
                                                </li>
                                            @endcan
                                        </ul>
                                    </li>
                                @endcan
                            @endif --}}

                            {{-- @if ($show_dashboard == 1)
                                @can('show crm dashboard')
                                    <li
                                        class="dash-item dash-hasmenu {{ Request::segment(1) == 'crm-dashboard' || Request::segment(1) == 'reports-lead' || Request::segment(1) == 'reports-deal' ? ' active dash-trigger' : '' }}">
                                        <a class="dash-link" href="#">{{ __('CRM') }}<span
                                                class="dash-arrow"><i data-feather="chevron-right"></i></span></a>
                                        <ul class="dash-submenu">
                                            <li
                                                class="dash-item {{ \Request::route()->getName() == 'crm.dashboard' ? ' active' : '' }}">
                                                <a class="dash-link"
                                                    href="{{ route('crm.dashboard') }}">{{ __(' Overview') }}</a>
                                            </li>
                                            <li class="dash-item dash-hasmenu {{ Request::segment(1) == 'reports-lead' || Request::segment(1) == 'reports-deal' ? 'active dash-trigger' : '' }}"
                                                href="#crm-report" data-toggle="collapse" role="button"
                                                aria-expanded="{{ Request::segment(1) == 'reports-lead' || Request::segment(1) == 'reports-deal' ? 'true' : 'false' }}">
                                                <a class="dash-link" href="#">{{ __('Reports') }}<span
                                                        class="dash-arrow"><i data-feather="chevron-right"></i></span></a>
                                                <ul class="dash-submenu">
                                                    <li
                                                        class="dash-item {{ request()->is('reports-lead') ? 'active' : '' }}">
                                                        <a class="dash-link"
                                                            href="{{ route('report.lead') }}">{{ __('Lead') }}</a>
                                                    </li>
                                                    <li
                                                        class="dash-item {{ request()->is('reports-deal') ? 'active' : '' }}">
                                                        <a class="dash-link"
                                                            href="{{ route('report.deal') }}">{{ __('Deal') }}</a>
                                                    </li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </li>
                                @endcan
                            @endif

                            @if ($show_dashboard == 1)
                                @can('show project dashboard')
                                    <li
                                        class="dash-item {{ Request::route()->getName() == 'project.dashboard' ? ' active' : '' }}">
                                        <a class="dash-link"
                                            href="{{ route('project.dashboard') }}">{{ __('Project ') }}</a>
                                    </li>
                                @endcan
                            @endif

                            @if ($show_dashboard == 1)
                                @can('show pos dashboard')
                                    <li
                                        class="dash-item dash-hasmenu {{
                                        Request::segment(1) == 'pos-dashboard' ||
                                        Request::segment(1) == 'reports-warehouse' ||
                                        Request::segment(1) == 'reports-daily-purchase' ||
                                        Request::segment(1) == 'reports-monthly-purchase' ||
                                        Request::segment(1) == 'reports-daily-pos' ||
                                        Request::segment(1) == 'reports-monthly-pos' ||
                                        Request::segment(1) == 'reports-pos-vs-purchase' ? ' active dash-trigger' : '' }}">
                                        <a class="dash-link" href="#">{{ __('POS') }}<span
                                                class="dash-arrow"><i data-feather="chevron-right"></i></span></a>
                                        <ul class="dash-submenu">
                                            <li
                                                class="dash-item {{ \Request::route()->getName() == 'pos.dashboard' ? ' active' : '' }}">
                                                <a class="dash-link"
                                                    href="{{ route('pos.dashboard') }}">{{ __(' Overview') }}</a>
                                            </li>
                                            @can('manage pos')
                                                <li class="dash-item dash-hasmenu {{
                                                    Request::segment(1) == 'reports-warehouse' ||
                                                    Request::segment(1) == 'reports-daily-purchase' ||
                                                    Request::segment(1) == 'reports-monthly-purchase' ||
                                                    Request::segment(1) == 'reports-daily-pos' ||
                                                    Request::segment(1) == 'reports-monthly-pos' ||
                                                    Request::segment(1) == 'reports-pos-vs-purchase' ? 'active dash-trigger' : '' }}"
                                                    href="#crm-report" data-toggle="collapse" role="button"
                                                    aria-expanded="{{
                                                    Request::segment(1) == 'reports-warehouse' ||
                                                    Request::segment(1) == 'reports-daily-purchase' ||
                                                    Request::segment(1) == 'reports-monthly-purchase' ||
                                                    Request::segment(1) == 'reports-daily-pos' ||
                                                    Request::segment(1) == 'reports-monthly-pos' ||
                                                    Request::segment(1) == 'reports-pos-vs-purchase' ? 'true' : 'false' }}">
                                                    <a class="dash-link" href="#">{{ __('Reports') }}<span
                                                            class="dash-arrow"><i data-feather="chevron-right"></i></span></a>
                                                    <ul class="dash-submenu">
                                                        <li
                                                            class="dash-item {{ request()->is('reports-warehouse') ? 'active' : '' }}">
                                                            <a class="dash-link"
                                                                href="{{ route('report.warehouse') }}">{{ __('Warehouse Report') }}</a>
                                                        </li>
                                                        <li
                                                            class="dash-item {{ request()->is('reports-daily-purchase') || request()->is('reports-monthly-purchase') ? 'active' : '' }}">
                                                            <a class="dash-link"
                                                                href="{{ route('report.daily.purchase') }}">{{ __('Purchase Daily/Monthly Report') }}</a>
                                                        </li>
                                                        <li
                                                            class="dash-item {{ request()->is('reports-daily-pos') || request()->is('reports-monthly-pos') ? 'active' : '' }}">
                                                            <a class="dash-link"
                                                                href="{{ route('report.daily.pos') }}">{{ __('POS Daily/Monthly Report') }}</a>
                                                        </li>
                                                        <li
                                                            class="dash-item {{ request()->is('reports-pos-vs-purchase') ? 'active' : '' }}">
                                                            <a class="dash-link"
                                                                href="{{ route('report.pos.vs.purchase') }}">{{ __('Pos VS Purchase Report') }}</a>
                                                        </li>
                                                    </ul>
                                                </li>
                                            @endcan
                                        </ul>
                                    </li>
                                @endcan
                            @endif --}}

                        </ul>
                    </li>
                @endif

                    <!--------------------- End Dashboard ----------------------------------->


                    <!--------------------- Start HRM ----------------------------------->

                   {{--  @if ($show_dashboard == 1)
                        @if (Gate::check('manage employee') ||
                            Gate::check('manage set salary') || Gate::check('manage pay slip') ||
                            Gate::check('manage leave') || Gate::check('manage attendance') ||
                            Gate::check('create attendance') || Gate::check('manage indicator') ||
                            Gate::check('manage appraisal') || Gate::check('manage goal tracking') ||
                            Gate::check('manage training') || Gate::check('manage trainer') ||
                            Gate::check('manage job') || Gate::check('create job') ||
                            Gate::check('manage job application') || Gate::check('manage custom question') ||
                            Gate::check('manage job onBoard') || Gate::check('show interview schedule') ||
                            Gate::check('show career') || Gate::check('manage award') ||
                            Gate::check('manage transfer') || Gate::check('manage resignation') ||
                            Gate::check('manage travel') || Gate::check('manage promotion') ||
                            Gate::check('manage complaint') || Gate::check('manage warning') ||
                            Gate::check('manage termination') || Gate::check('manage announcement') ||
                            Gate::check('manage holiday') || Gate::check('manage event') ||
                            Gate::check('manage meeting') || Gate::check('manage assets') ||
                            Gate::check('manage document') || Gate::check('manage company policy') ||
                            Gate::check('manage branch') || Gate::check('manage department') ||
                            Gate::check('manage designation') || Gate::check('manage leave type') ||
                            Gate::check('manage document type') || Gate::check('manage payslip type') ||
                            Gate::check('manage allowance option') || Gate::check('manage loan option') ||
                            Gate::check('manage deduction option') || Gate::check('manage goal type') ||
                            Gate::check('manage training type') || Gate::check('manage award type') ||
                            Gate::check('manage termination type') || Gate::check('manage job category') ||
                            Gate::check('manage job stage') || Gate::check('manage performance type') ||
                            Gate::check('manage competencies'))
                            <li
                                class="dash-item dash-hasmenu {{ Request::segment(1) == 'holiday-calender' ||
                                Request::segment(1) == 'leavetype' || Request::segment(1) == 'leave' ||
                                Request::segment(1) == 'attendanceemployee' || Request::segment(1) == 'bulkattendance' ||
                                Request::segment(1) == 'indicator' || Request::segment(1) == 'appraisal' ||
                                Request::segment(1) == 'goaltracking' || Request::segment(1) == 'trainer' ||
                                Request::segment(1) == 'event' || Request::segment(1) == 'meeting' ||
                                Request::segment(1) == 'account-assets' || Request::segment(1) == 'leavetype' ||
                                Request::segment(1) == 'meeting-calender' || Request::segment(1) == 'document-upload' ||
                                Request::segment(1) == 'document' || Request::segment(1) == 'performanceType' ||
                                Request::segment(1) == 'branch' || Request::segment(1) == 'department' ||
                                Request::segment(1) == 'designation' || Request::segment(1) == 'employee' ||
                                Request::segment(1) == 'leave_requests' || Request::segment(1) == 'holidays' ||
                                Request::segment(1) == 'policies' || Request::segment(1) == 'leave_calender' ||
                                Request::segment(1) == 'award' || Request::segment(1) == 'transfer' ||
                                Request::segment(1) == 'resignation' || Request::segment(1) == 'training' ||
                                Request::segment(1) == 'travel' || Request::segment(1) == 'promotion' ||
                                Request::segment(1) == 'complaint' || Request::segment(1) == 'warning' ||
                                Request::segment(1) == 'termination' || Request::segment(1) == 'announcement' ||
                                Request::segment(1) == 'job' || Request::segment(1) == 'job-application' ||
                                Request::segment(1) == 'candidates-job-applications' || Request::segment(1) == 'job-onboard' ||
                                Request::segment(1) == 'custom-question' || Request::segment(1) == 'interview-schedule' ||
                                Request::segment(1) == 'career' || Request::segment(1) == 'holiday' ||
                                Request::segment(1) == 'setsalary' || Request::segment(1) == 'payslip' ||
                                Request::segment(1) == 'paysliptype' || Request::segment(1) == 'company-policy' ||
                                Request::segment(1) == 'job-stage' || Request::segment(1) == 'job-category' ||
                                Request::segment(1) == 'terminationtype' || Request::segment(1) == 'awardtype' ||
                                Request::segment(1) == 'trainingtype' || Request::segment(1) == 'goaltype' ||
                                Request::segment(1) == 'allowanceoption' || Request::segment(1) == 'competencies' ||
                                Request::segment(1) == 'loanoption' || Request::segment(1) == 'deductionoption'
                                    ? 'active dash-trigger'
                                    : '' }}">
                                <a href="#!" class="dash-link "><span class="dash-micon"><i
                                            class="ti ti-user"></i></span><span
                                        class="dash-mtext">{{ __('HRM System') }}</span><span class="dash-arrow">
                                        <i data-feather="chevron-right"></i></span>
                                </a>
                                <ul class="dash-submenu">
                                    @can('manage employee')
                                        <li
                                            class="dash-item  {{ Request::segment(1) == 'employee' ? 'active dash-trigger' : '' }}   ">
                                            @if (\Auth::user()->type == 'Employee')
                                                @php
                                                    $employee = App\Models\Employee::where(
                                                        'user_id',
                                                        \Auth::user()->id,
                                                    )->first();
                                                @endphp
                                                <a class="dash-link"
                                                    href="{{ route('employee.show', \Illuminate\Support\Facades\Crypt::encrypt($employee->id)) }}">{{ __('Employee') }}</a>
                                            @else
                                                <a href="{{ route('employee.index') }}" class="dash-link">
                                                    {{ __('Employee Setup') }}
                                                </a>
                                            @endif
                                        </li>
                                    @endcan
                                    @if (Gate::check('manage set salary') || Gate::check('manage pay slip'))
                                        <li class="dash-item dash-hasmenu  {{ Request::segment(1) == 'setsalary' || Request::segment(1) == 'payslip' ? 'active dash-trigger' : '' }}">
                                            <a class="dash-link" href="#">{{ __('Payroll Setup') }}<span
                                                    class="dash-arrow"><i data-feather="chevron-right"></i></span></a>
                                            <ul class="dash-submenu">
                                                @can('manage set salary')
                                                    <li
                                                        class="dash-item {{ request()->is('setsalary*') ? 'active' : '' }}">
                                                        <a class="dash-link"
                                                            href="{{ route('setsalary.index') }}">{{ __('Set salary') }}</a>
                                                    </li>
                                                @endcan
                                                @can('manage pay slip')
                                                    <li
                                                        class="dash-item {{ request()->is('payslip*') ? 'active' : '' }}">
                                                        <a class="dash-link"
                                                            href="{{ route('payslip.index') }}">{{ __('Payslip') }}</a>
                                                    </li>
                                                @endcan
                                            </ul>
                                        </li>
                                    @endif

                                    @if (Gate::check('manage leave') || Gate::check('manage attendance'))
                                        <li
                                            class="dash-item dash-hasmenu  {{ Request::segment(1) == 'leave' || Request::segment(1) == 'attendanceemployee' ? 'active dash-trigger' : '' }}">
                                            <a class="dash-link"
                                                href="#">{{ __('Leave Management Setup') }}<span
                                                    class="dash-arrow"><i data-feather="chevron-right"></i></span></a>
                                            <ul class="dash-submenu">
                                                @can('manage leave')
                                                    <li
                                                        class="dash-item {{ Request::route()->getName() == 'leave.index' ? 'active' : '' }}">
                                                        <a class="dash-link"
                                                            href="{{ route('leave.index') }}">{{ __('Manage Leave') }}</a>
                                                    </li>
                                                @endcan
                                                @can('manage attendance')
                                                    <li class="dash-item dash-hasmenu {{ Request::segment(1) == 'attendanceemployee' ? 'active dash-trigger' : '' }}"
                                                        href="#navbar-attendance" data-toggle="collapse" role="button"
                                                        aria-expanded="{{ Request::segment(1) == 'attendanceemployee' ? 'true' : 'false' }}">
                                                        <a class="dash-link" href="#">{{ __('Attendance') }}<span
                                                                class="dash-arrow"><i
                                                                    data-feather="chevron-right"></i></span></a>
                                                        <ul class="dash-submenu">
                                                            <li
                                                                class="dash-item {{ Request::route()->getName() == 'attendanceemployee.index' ? 'active' : '' }}">
                                                                <a class="dash-link"
                                                                    href="{{ route('attendanceemployee.index') }}">{{ __('Mark Attendance') }}</a>
                                                            </li>
                                                            @can('create attendance')
                                                                <li
                                                                    class="dash-item {{ Request::route()->getName() == 'attendanceemployee.bulkattendance' ? 'active' : '' }}">
                                                                    <a class="dash-link"
                                                                        href="{{ route('attendanceemployee.bulkattendance') }}">{{ __('Bulk Attendance') }}</a>
                                                                </li>
                                                            @endcan
                                                        </ul>
                                                    </li>
                                                @endcan

                                            </ul>
                                        </li>
                                    @endif

                                    @if (Gate::check('manage indicator') || Gate::check('manage appraisal') || Gate::check('manage goal tracking'))
                                        <li class="dash-item dash-hasmenu {{ Request::segment(1) == 'indicator' || Request::segment(1) == 'appraisal' || Request::segment(1) == 'goaltracking' ? 'active dash-trigger' : '' }}"
                                            href="#navbar-performance" data-toggle="collapse" role="button"
                                            aria-expanded="{{ Request::segment(1) == 'indicator' || Request::segment(1) == 'appraisal' || Request::segment(1) == 'goaltracking' ? 'true' : 'false' }}">
                                            <a class="dash-link" href="#">{{ __('Performance Setup') }}<span
                                                    class="dash-arrow"><i data-feather="chevron-right"></i></span></a>
                                            <ul
                                                class="dash-submenu {{ Request::segment(1) == 'indicator' || Request::segment(1) == 'appraisal' || Request::segment(1) == 'goaltracking' ? 'show' : 'collapse' }}">
                                                @can('manage indicator')
                                                    <li
                                                        class="dash-item {{ request()->is('indicator*') ? 'active' : '' }}">
                                                        <a class="dash-link"
                                                            href="{{ route('indicator.index') }}">{{ __('Indicator') }}</a>
                                                    </li>
                                                @endcan
                                                @can('manage appraisal')
                                                    <li
                                                        class="dash-item {{ request()->is('appraisal*') ? 'active' : '' }}">
                                                        <a class="dash-link"
                                                            href="{{ route('appraisal.index') }}">{{ __('Appraisal') }}</a>
                                                    </li>
                                                @endcan
                                                @can('manage goal tracking')
                                                    <li
                                                        class="dash-item  {{ request()->is('goaltracking*') ? 'active' : '' }}">
                                                        <a class="dash-link"
                                                            href="{{ route('goaltracking.index') }}">{{ __('Goal Tracking') }}</a>
                                                    </li>
                                                @endcan
                                            </ul>
                                        </li>
                                    @endif

                                    @if (Gate::check('manage training') || Gate::check('manage trainer'))
                                        <li class="dash-item dash-hasmenu {{ Request::segment(1) == 'trainer' || Request::segment(1) == 'training' ? 'active dash-trigger' : '' }}"
                                            href="#navbar-training" data-toggle="collapse" role="button"
                                            aria-expanded="{{ Request::segment(1) == 'trainer' || Request::segment(1) == 'training' ? 'true' : 'false' }}">
                                            <a class="dash-link" href="#">{{ __('Training Setup') }}<span
                                                    class="dash-arrow"><i data-feather="chevron-right"></i></span></a>
                                            <ul class="dash-submenu">
                                                @can('manage training')
                                                    <li
                                                        class="dash-item {{ request()->is('training*') ? 'active' : '' }}">
                                                        <a class="dash-link"
                                                            href="{{ route('training.index') }}">{{ __('Training List') }}</a>
                                                    </li>
                                                @endcan
                                                @can('manage trainer')
                                                    <li
                                                        class="dash-item {{ request()->is('trainer*') ? 'active' : '' }}">
                                                        <a class="dash-link"
                                                            href="{{ route('trainer.index') }}">{{ __('Trainer') }}</a>
                                                    </li>
                                                @endcan

                                            </ul>
                                        </li>
                                    @endif

                                    @if (Gate::check('manage job') ||
                                            Gate::check('create job') ||
                                            Gate::check('manage job application') ||
                                            Gate::check('manage job onBoard') ||
                                            Gate::check('manage custom question') ||
                                            Gate::check('show interview schedule') ||
                                            Gate::check('show career'))
                                        <li
                                            class="dash-item dash-hasmenu {{ Request::segment(1) == 'job' || Request::segment(1) == 'job-application' || Request::segment(1) == 'candidates-job-applications' || Request::segment(1) == 'job-onboard' || Request::segment(1) == 'custom-question' || Request::segment(1) == 'interview-schedule' || Request::segment(1) == 'career' ? 'active dash-trigger' : '' }}    ">
                                            <a class="dash-link" href="#">{{ __('Recruitment Setup') }}<span
                                                    class="dash-arrow"><i data-feather="chevron-right"></i></span></a>
                                            <ul class="dash-submenu">
                                                @can('manage job')
                                                    <li
                                                        class="dash-item {{ Request::route()->getName() == 'job.index' || Request::route()->getName() == 'job.create' || Request::route()->getName() == 'job.edit' || Request::route()->getName() == 'job.show' ? 'active' : '' }}">
                                                        <a class="dash-link"
                                                            href="{{ route('job.index') }}">{{ __('Jobs') }}</a>
                                                    </li>
                                                @endcan
                                                @can('create job')
                                                    <li
                                                        class="dash-item {{ Request::route()->getName() == 'job.create' ? 'active' : '' }} ">
                                                        <a class="dash-link"
                                                            href="{{ route('job.create') }}">{{ __('Job Create') }}</a>
                                                    </li>
                                                @endcan
                                                @can('manage job application')
                                                    <li
                                                        class="dash-item {{ request()->is('job-application*') ? 'active' : '' }}">
                                                        <a class="dash-link"
                                                            href="{{ route('job-application.index') }}">{{ __('Job Application') }}</a>
                                                    </li>
                                                @endcan
                                                @can('manage job onBoard')
                                                    <li
                                                        class="dash-item {{ request()->is('candidates-job-applications') ? 'active' : '' }}">
                                                        <a class="dash-link"
                                                            href="{{ route('job.application.candidate') }}">{{ __('Job Candidate') }}</a>
                                                    </li>
                                                @endcan
                                                @can('manage job onBoard')
                                                    <li
                                                        class="dash-item {{ request()->is('job-onboard*') ? 'active' : '' }}">
                                                        <a class="dash-link"
                                                            href="{{ route('job.on.board') }}">{{ __('Job On-boarding') }}</a>
                                                    </li>
                                                @endcan
                                                @can('manage custom question')
                                                    <li
                                                        class="dash-item  {{ request()->is('custom-question*') ? 'active' : '' }}">
                                                        <a class="dash-link"
                                                            href="{{ route('custom-question.index') }}">{{ __('Custom Question') }}</a>
                                                    </li>
                                                @endcan
                                                @can('show interview schedule')
                                                    <li
                                                        class="dash-item {{ request()->is('interview-schedule*') ? 'active' : '' }}">
                                                        <a class="dash-link"
                                                            href="{{ route('interview-schedule.index') }}">{{ __('Interview Schedule') }}</a>
                                                    </li>
                                                @endcan
                                                @can('show career')
                                                    <li
                                                        class="dash-item {{ request()->is('career*') ? 'active' : '' }}">
                                                        <a class="dash-link"
                                                            href="{{ route('career', [\Auth::user()->creatorId(), $lang]) }}">{{ __('Career') }}</a>
                                                    </li>
                                                @endcan
                                            </ul>
                                        </li>
                                    @endif

                                    @if (Gate::check('manage award') ||
                                            Gate::check('manage transfer') ||
                                            Gate::check('manage resignation') ||
                                            Gate::check('manage travel') ||
                                            Gate::check('manage promotion') ||
                                            Gate::check('manage complaint') ||
                                            Gate::check('manage warning') ||
                                            Gate::check('manage termination') ||
                                            Gate::check('manage announcement') ||
                                            Gate::check('manage holiday'))
                                        <li
                                            class="dash-item dash-hasmenu {{ Request::segment(1) == 'holiday-calender' || Request::segment(1) == 'holiday' || Request::segment(1) == 'policies' || Request::segment(1) == 'award' || Request::segment(1) == 'transfer' || Request::segment(1) == 'resignation' || Request::segment(1) == 'travel' || Request::segment(1) == 'promotion' || Request::segment(1) == 'complaint' || Request::segment(1) == 'warning' || Request::segment(1) == 'termination' || Request::segment(1) == 'announcement' || Request::segment(1) == 'competencies' ? 'active dash-trigger' : '' }}">
                                            <a class="dash-link" href="#">{{ __('HR Admin Setup') }}<span
                                                    class="dash-arrow"><i data-feather="chevron-right"></i></span></a>
                                            <ul class="dash-submenu">
                                                @can('manage award')
                                                    <li
                                                        class="dash-item {{ request()->is('award*') ? 'active' : '' }}">
                                                        <a class="dash-link"
                                                            href="{{ route('award.index') }}">{{ __('Award') }}</a>
                                                    </li>
                                                @endcan
                                                @can('manage transfer')
                                                    <li
                                                        class="dash-item  {{ request()->is('transfer*') ? 'active' : '' }}">
                                                        <a class="dash-link"
                                                            href="{{ route('transfer.index') }}">{{ __('Transfer') }}</a>
                                                    </li>
                                                @endcan
                                                @can('manage resignation')
                                                    <li
                                                        class="dash-item {{ request()->is('resignation*') ? 'active' : '' }}">
                                                        <a class="dash-link"
                                                            href="{{ route('resignation.index') }}">{{ __('Resignation') }}</a>
                                                    </li>
                                                @endcan
                                                @can('manage travel')
                                                    <li
                                                        class="dash-item {{ request()->is('travel*') ? 'active' : '' }}">
                                                        <a class="dash-link"
                                                            href="{{ route('travel.index') }}">{{ __('Trip') }}</a>
                                                    </li>
                                                @endcan
                                                @can('manage promotion')
                                                    <li
                                                        class="dash-item {{ request()->is('promotion*') ? 'active' : '' }}">
                                                        <a class="dash-link"
                                                            href="{{ route('promotion.index') }}">{{ __('Promotion') }}</a>
                                                    </li>
                                                @endcan
                                                @can('manage complaint')
                                                    <li
                                                        class="dash-item {{ request()->is('complaint*') ? 'active' : '' }}">
                                                        <a class="dash-link"
                                                            href="{{ route('complaint.index') }}">{{ __('Complaints') }}</a>
                                                    </li>
                                                @endcan
                                                @can('manage warning')
                                                    <li
                                                        class="dash-item {{ request()->is('warning*') ? 'active' : '' }}">
                                                        <a class="dash-link"
                                                            href="{{ route('warning.index') }}">{{ __('Warning') }}</a>
                                                    </li>
                                                @endcan
                                                @can('manage termination')
                                                    <li
                                                        class="dash-item {{ request()->is('termination*') ? 'active' : '' }}">
                                                        <a class="dash-link"
                                                            href="{{ route('termination.index') }}">{{ __('Termination') }}</a>
                                                    </li>
                                                @endcan
                                                @can('manage announcement')
                                                    <li
                                                        class="dash-item {{ request()->is('announcement*') ? 'active' : '' }}">
                                                        <a class="dash-link"
                                                            href="{{ route('announcement.index') }}">{{ __('Announcement') }}</a>
                                                    </li>
                                                @endcan
                                                @can('manage holiday')
                                                    <li
                                                        class="dash-item {{ request()->is('holiday*') || request()->is('holiday-calender') ? 'active' : '' }}">
                                                        <a class="dash-link"
                                                            href="{{ route('holiday.index') }}">{{ __('Holidays') }}</a>
                                                    </li>
                                                @endcan
                                            </ul>
                                        </li>
                                    @endif

                                    @can('manage event')
                                        <li class="dash-item {{ request()->is('event*') ? 'active' : '' }}">
                                            <a class="dash-link"
                                                href="{{ route('event.index') }}">{{ __('Event Setup') }}</a>
                                        </li>
                                    @endcan
                                    @can('manage meeting')
                                        <li class="dash-item {{ request()->is('meeting*') ? 'active' : '' }}">
                                            <a class="dash-link"
                                                href="{{ route('meeting.index') }}">{{ __('Meeting') }}</a>
                                        </li>
                                    @endcan
                                    @can('manage assets')
                                        <li class="dash-item {{ request()->is('account-assets*') ? 'active' : '' }}">
                                            <a class="dash-link"
                                                href="{{ route('account-assets.index') }}">{{ __('Employees Asset Setup ') }}</a>
                                        </li>
                                    @endcan
                                    @can('manage document')
                                        <li class="dash-item {{ request()->is('document-upload*') ? 'active' : '' }}">
                                            <a class="dash-link"
                                                href="{{ route('document-upload.index') }}">{{ __('Document Setup') }}</a>
                                        </li>
                                    @endcan
                                    @can('manage company policy')
                                        <li class="dash-item {{ request()->is('company-policy*') ? 'active' : '' }}">
                                            <a class="dash-link"
                                                href="{{ route('company-policy.index') }}">{{ __('Company policy') }}</a>
                                        </li>
                                    @endcan


                                    @if (\Auth::user()->type == 'company' || \Auth::user()->type == 'HR')
                                        <li
                                            class="dash-item {{ Request::segment(1) == 'leavetype' ||
                                            Request::segment(1) == 'document' ||
                                            Request::segment(1) == 'performanceType' ||
                                            Request::segment(1) == 'branch' ||
                                            Request::segment(1) == 'department' ||
                                            Request::segment(1) == 'designation' ||
                                            Request::segment(1) == 'job-stage' ||
                                            Request::segment(1) == 'competencies' ||
                                            Request::segment(1) == 'job-category' ||
                                            Request::segment(1) == 'terminationtype' ||
                                            Request::segment(1) == 'awardtype' ||
                                            Request::segment(1) == 'trainingtype' ||
                                            Request::segment(1) == 'goaltype' ||
                                            Request::segment(1) == 'paysliptype' ||
                                            Request::segment(1) == 'allowanceoption' ||
                                            Request::segment(1) == 'loanoption' ||
                                            Request::segment(1) == 'deductionoption'
                                                ? 'active dash-trigger'
                                                : '' }}">
                                            <a class="dash-link"
                                                href="{{ route('branch.index') }}">{{ __('HRM System Setup') }}</a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif
                    @endif
 --}}
                    <!--------------------- End HRM ----------------------------------->



                    <!--------------------- Start Customer ----------------------------------->

                    @if ($show_dashboard == 1)
                        @if (Gate::check('manage constant custom field') ||
                            Gate::check('manage customer') || Gate::check('manage proposal') )
                            <li
                                class="dash-item dash-hasmenu
                                {{ Request::route()->getName() == 'print-setting' ||
                                Request::segment(1) == 'customer' ||
                                Request::segment(1) == 'proposal' ||
                                Request::segment(1) == 'custom-field' ? ' active dash-trigger'
                                    : '' }}">
                                <a href="#!" class="dash-link"><span class="dash-micon"><i class="ti ti-shopping-cart"></i></span><span
                                        class="dash-mtext">{{ __('Customer') }}</span><span
                                        class="dash-arrow">
                                        <i data-feather="chevron-right"></i></span>
                                </a>
                                <ul class="dash-submenu">

                                               @if (Gate::check('manage customer'))
                                                    <li
                                                        class="dash-item {{ Request::segment(1) == 'customer' ? 'active' : '' }}">
                                                        <a class="dash-link"
                                                            href="{{ route('customer.index') }}">{{ __('Customer') }}</a>
                                                    </li>

                                                @endif

                                     {{-- @can('manage purchase')
                                        <li
                                            class="dash-item {{ Request::route()->getName() == 'purchase.index' || Request::route()->getName() == 'purchase.create' || Request::route()->getName() == 'purchase.edit' || Request::route()->getName() == 'purchase.show' ? ' active' : '' }}">
                                            <a class="dash-link"
                                                href="{{ route('purchase.index') }}">{{ __('Cargo') }}</a>
                                        </li>
                                    @endcan --}}
                                    @if (Gate::check('manage invoice') || Gate::check('manage revenue') || Gate::check('manage credit note'))

                                          <li
                                            class="dash-item dash-hasmenu {{  Request::segment(1) == 'proposal' || Request::segment(1) == 'invoice' || Request::segment(1) == 'revenue' || Request::segment(1) == 'credit-note' ? 'active dash-trigger' : '' }}">
                                            <a class="dash-link" href="#">{{ __('License') }}<span
                                                    class="dash-arrow"><i data-feather="chevron-right"></i></span></a>
                                            <ul class="dash-submenu">


                                                {{--  @can('manage cargo')

                                                    <li
                                                        class="dash-item {{ Request::route()->getName() == 'cargo.index' || Request::route()->getName() == 'cargo.create' || Request::route()->getName() == 'cargo.edit' || Request::route()->getName() == 'cargo.show' ? ' active' : '' }}">
                                                        <a class="dash-link"
                                                            href="{{ route('cargo.index') }}">{{ __('cargo') }}</a>
                                                    </li>
                                                @endcan --}}
                                                @can('manage invoice')

                                                    <li
                                                        class="dash-item {{ Request::route()->getName() == 'invoice.index' || Request::route()->getName() == 'invoice.create' || Request::route()->getName() == 'invoice.edit' || Request::route()->getName() == 'invoice.show' ? ' active' : '' }}">
                                                        <a class="dash-link"
                                                            href="{{ route('invoice.index') }}">{{ __('New License') }}</a>
                                                    </li>
                                                @endcan
                                                 @can('manage revenue')
                                                    <li
                                                        class="dash-item {{ Request::route()->getName() == 'revenue.index' || Request::route()->getName() == 'revenue.create' || Request::route()->getName() == 'revenue.edit' ? ' active' : '' }}">
                                                        <a class="dash-link"
                                                            href="{{ route('revenue.index') }}">{{ __('Revenue') }}</a>
                                                    </li>
                                                @endcan
                                                @can('manage credit note')
                                                    <li
                                                        class="dash-item {{ Request::route()->getName() == 'custom-credit.note' ? ' active' : '' }}">
                                                        <a class="dash-link"
                                                            href="{{ route('custom-credit.note') }}">{{ __('Credit Note') }}</a>
                                                    </li>
                                                @endcan
                                            </ul>
                                        </li>
                                    @endif

                                    {{-- @if (Gate::check('manage visa') || Gate::check('view visa') || Gate::check('add visa') || Gate::check('edit visa') || Gate::check('delete visa') || Gate::check('create visa'))
                                    <li
                                        class="dash-item {{ Request::segment(1) == 'visa' ? 'active' : '' }}">
                                        <a href="{{ route('visa.index') }}"
                                            class="dash-link">{{ __('Visa') }}
                                        </a>
                                    </li>
                                @endif --}}




                                </ul>
                            </li>
                        @endif
                    @endif

                    <!--------------------- End Customer ----------------------------------->


                    <!--------------------- Start Vender ----------------------------------->

                    @if ($show_dashboard == 1)
                        @if (Gate::check('manage bill') || Gate::check('manage payment') ||
                            Gate::check('manage debit note') || Gate::check('manage chart of account') ||
                            Gate::check('manage journal entry') || Gate::check('balance sheet report') ||
                            Gate::check('ledger report') || Gate::check('trial balance report') )
                            <li
                                class="dash-item dash-hasmenu
                                {{ Request::route()->getName() == 'print-setting' ||
                                Request::segment(1) == 'custom-field' || Request::segment(1) == 'chart-of-account-type' ||
                                Request::segment(1) == 'bill' || Request::segment(1) == 'expense' ||
                                Request::segment(1) == 'payment' || Request::segment(1) == 'debit-note'  ? ' active dash-trigger'
                                    : '' }}">
                                <a href="#!" class="dash-link"><span class="dash-micon"><i
                                            class="ti ti-layers-difference"></i></span><span
                                        class="dash-mtext">{{ __('Vender') }}</span><span
                                        class="dash-arrow">
                                        <i data-feather="chevron-right"></i></span>
                                </a>
                                <ul class="dash-submenu">

                                    @if (Gate::check('manage vender'))
                                                    <li
                                                        class="dash-item {{ Request::segment(1) == 'vender' ? 'active' : '' }}">
                                                        <a class="dash-link"
                                                            href="{{ route('vender.index') }}">{{ __('Vendors') }}</a>
                                                    </li>
                                                @endif
                                    @if (Gate::check('manage bill') ||
                                        Gate::check('manage payment') ||
                                        Gate::check('manage debit note'))
                                        <li
                                            class="dash-item dash-hasmenu {{ Request::segment(1) == 'bill' ||  Request::segment(1) == 'expense' || Request::segment(1) == 'payment' || Request::segment(1) == 'debit-note' ? 'active dash-trigger' : '' }}">
                                            <a class="dash-link" href="#">{{ __('Expenses') }}<span
                                                    class="dash-arrow"><i data-feather="chevron-right"></i></span></a>
                                            <ul class="dash-submenu">

                                                 @can('manage bill')
                                                    <li
                                                        class="dash-item {{ Request::route()->getName() == 'bill.index' || Request::route()->getName() == 'bill.create' || Request::route()->getName() == 'bill.edit' || Request::route()->getName() == 'bill.show' ? ' active' : '' }}">
                                                        <a class="dash-link"
                                                            href="{{ route('bill.index') }}">{{ __('Bill') }}</a>
                                                    </li>
                                                    {{-- <li
                                                        class="dash-item {{ Request::route()->getName() == 'expense.index' || Request::route()->getName() == 'expense.create' || Request::route()->getName() == 'expense.edit' || Request::route()->getName() == 'expense.show' ? ' active' : '' }}">
                                                        <a class="dash-link"
                                                            href="{{ route('expense.index') }}">{{ __('Expense') }}</a>
                                                    </li> --}}
                                                @endcan
                                                @can('manage payment')
                                                    <li
                                                        class="dash-item {{ Request::route()->getName() == 'payment.index' || Request::route()->getName() == 'payment.create' || Request::route()->getName() == 'payment.edit' ? ' active' : '' }}">
                                                        <a class="dash-link"
                                                            href="{{ route('payment.index') }}">{{ __('Payment') }}</a>
                                                    </li>
                                                @endcan
                                                @can('manage debit note')
                                                    <li
                                                        class="dash-item  {{ Request::route()->getName() == 'custom-debit.note' ? ' active' : '' }}">
                                                        <a class="dash-link"
                                                            href="{{ route('custom-debit.note') }}">{{ __('Debit Note') }}</a>
                                                    </li>
                                                @endcan
                                            </ul>
                                        </li>
                                    @endif



                                </ul>
                            </li>
                        @endif
                    @endif

                    <!--------------------- End Customer ----------------------------------->

                    <!--------------------- Start Account ----------------------------------->

                    @if ($show_dashboard == 1)
                        @if (Gate::check('manage budget plan') || Gate::check('income vs expense report') ||
                            Gate::check('manage goal') || Gate::check('manage constant tax') ||
                            Gate::check('manage constant category') || Gate::check('manage constant unit') ||
                            Gate::check('manage constant custom field') || Gate::check('manage print settings') ||
                            Gate::check('manage proposal') || Gate::check('manage bank account') ||
                            Gate::check('manage bank transfer') || Gate::check('manage chart of account') ||
                            Gate::check('manage journal entry') || Gate::check('balance sheet report') ||
                            Gate::check('ledger report') || Gate::check('trial balance report') )
                            <li
                                class="dash-item dash-hasmenu
                                {{ Request::route()->getName() == 'print-setting' ||
                                Request::segment(1) == 'bank-account' ||
                                Request::segment(1) == 'taxes' || Request::segment(1) == 'product-category' ||
                                Request::segment(1) == 'product-unit' || Request::segment(1) == 'payment-method' ||
                                Request::segment(1) == 'custom-field' || Request::segment(1) == 'chart-of-account-type' ||
                                (Request::segment(1) == 'transaction' && Request::segment(2) != 'ledger' &&
                                Request::segment(2) != 'balance-sheet-report' && Request::segment(2) != 'trial-balance') ||
                                Request::segment(1) == 'goal' || Request::segment(1) == 'budget' ||
                                Request::segment(1) == 'chart-of-account' || Request::segment(1) == 'journal-entry' ||
                                Request::segment(2) == 'ledger' || Request::segment(2) == 'balance-sheet' ||
                                Request::segment(2) == 'trial-balance' || Request::segment(2) == 'profit-loss' ||
                                (Request::route()->getName() == 'report.balance.sheet') || (Request::route()->getName() == 'trial-balance-report') ? ' active dash-trigger'
                                    : '' }}">
                                <a href="#!" class="dash-link"><span class="dash-micon"><i
                                            class="ti ti-box"></i></span><span
                                        class="dash-mtext">{{ __('Finance') }}</span><span
                                        class="dash-arrow">
                                        <i data-feather="chevron-right"></i></span>
                                </a>
                                <ul class="dash-submenu">

                                    @if (Gate::check('manage bank account') || Gate::check('manage bank transfer'))
                                        <li
                                            class="dash-item dash-hasmenu {{ Request::segment(1) == 'bank-account' || Request::segment(1) == 'bank-transfer' ? 'active dash-trigger' : '' }}">
                                            <a class="dash-link" href="#">{{ __('Banking') }}<span
                                                    class="dash-arrow"><i data-feather="chevron-right"></i></span></a>
                                            <ul class="dash-submenu">
                                                <li
                                                    class="dash-item {{ Request::route()->getName() == 'bank-account.index' || Request::route()->getName() == 'bank-account.create' || Request::route()->getName() == 'bank-account.edit' ? ' active' : '' }}">
                                                    <a class="dash-link"
                                                        href="{{ route('bank-account.index') }}">{{ __('Account') }}</a>
                                                </li>
                                                <li
                                                    class="dash-item {{ Request::route()->getName() == 'bank-transfer.index' || Request::route()->getName() == 'bank-transfer.create' || Request::route()->getName() == 'bank-transfer.edit' ? ' active' : '' }}">
                                                    <a class="dash-link"
                                                        href="{{ route('bank-transfer.index') }}">{{ __('Transfer') }}</a>
                                                </li>
                                            </ul>
                                        </li>
                                    @endif
                                    <li
                                        class="dash-item {{ Request::segment(1) == 'productservice' ? 'active' : '' }}">
                                        <a href="{{ route('productservice.index') }}"
                                            class="dash-link">{{ __('Account Categgoty') }}
                                        </a>
                                    </li>
                                    @if (Gate::check('manage chart of account') ||
                                        Gate::check('manage journal entry') ||
                                        Gate::check('ledger report') ||
                                        Gate::check('bill report') ||
                                        Gate::check('income vs expense report') ||
                                        Gate::check('trial balance report'))
                                        <li class="dash-item dash-hasmenu {{ Request::segment(1) == 'chart-of-account' ||
                                            Request::segment(1) == 'journal-entry' ||
                                            Request::segment(2) == 'profit-loss' ||
                                            Request::segment(2) == 'ledger' ||
                                            Request::segment(2) == 'trial-balance-report' ||
                                            Request::segment(2) == 'balance-sheet-report' ||
                                            Request::segment(2) == 'trial-balance' || (Request::route()->getName() == 'report.balance.sheet') || (Request::route()->getName() == 'trial-balance-report') ? 'active dash-trigger'
                                                : '' }}">
                                            <a class="dash-link" href="#">{{ __('Double Entry') }}<span
                                                    class="dash-arrow"><i data-feather="chevron-right"></i></span></a>
                                            <ul class="dash-submenu">
                                                @can('manage chart of account')
                                                    <li
                                                        class="dash-item {{ Request::route()->getName() == 'chart-of-account.index' || Request::route()->getName() == 'chart-of-account.show' ? ' active' : '' }}">
                                                        <a class="dash-link"
                                                            href="{{ route('chart-of-account.index') }}">{{ __('Chart of Accounts') }}</a>
                                                    </li>
                                                @endcan
                                                @can('manage journal entry')
                                                    <li
                                                        class="dash-item {{ Request::route()->getName() == 'journal-entry.edit' || Request::route()->getName() == 'journal-entry.create' || Request::route()->getName() == 'journal-entry.index' || Request::route()->getName() == 'journal-entry.show' ? ' active' : '' }}">
                                                        <a class="dash-link"
                                                            href="{{ route('journal-entry.index') }}">{{ __('Journal Account') }}</a>
                                                    </li>
                                                @endcan
                                                @can('ledger report')
                                                    <li
                                                        class="dash-item {{ Request::route()->getName() == 'report.ledger' ? ' active' : '' }}">
                                                        <a class="dash-link"
                                                            href="{{ route('report.ledger') }}">{{ __('Ledger Summary') }}</a>
                                                    </li>
                                                @endcan
                                                @can('bill report')
                                                    <li
                                                        class="dash-item {{ Request::route()->getName() == 'report.balance.sheet' ? ' active' : '' }}">
                                                        <a class="dash-link"
                                                            href="{{ route('report.balance.sheet') }}">{{ __('Balance Sheet') }}</a>
                                                    </li>
                                                @endcan
                                                @can('income vs expense report')
                                                    <li
                                                        class="dash-item {{ Request::route()->getName() == 'report.profit.loss' ? ' active' : '' }}">
                                                        <a class="dash-link"
                                                            href="{{ route('report.profit.loss') }}">{{ __('Profit & Loss') }}</a>
                                                    </li>
                                                @endcan
                                                @can('trial balance report')
                                                    <li
                                                        class="dash-item {{ Request::route()->getName() == 'trial.balance' ? ' active' : '' }}">
                                                        <a class="dash-link"
                                                            href="{{ route('trial.balance') }}">{{ __('Trial Balance') }}</a>
                                                    </li>
                                                @endcan
                                            </ul>
                                        </li>
                                    @endif
                                    @if (\Auth::user()->type == 'company')
                                        <li class="dash-item {{ Request::segment(1) == 'budget' ? 'active' : '' }}">
                                            <a class="dash-link"
                                                href="{{ route('budget.index') }}">{{ __('Budget Planner') }}</a>
                                        </li>
                                    @endif
                                    @if (Gate::check('manage goal'))
                                        <li class="dash-item {{ Request::segment(1) == 'goal' ? 'active' : '' }}">
                                            <a class="dash-link"
                                                href="{{ route('goal.index') }}">{{ __('Financial Goal') }}</a>
                                        </li>
                                    @endif
                                    @if (Gate::check('manage constant tax') ||
                                            Gate::check('manage constant category') ||
                                            Gate::check('manage constant unit') ||
                                            Gate::check('manage constant custom field'))
                                        <li
                                            class="dash-item {{ Request::segment(1) == 'taxes' || Request::segment(1) == 'product-category' || Request::segment(1) == 'product-unit' || Request::segment(1) == 'payment-method' || Request::segment(1) == 'custom-field' || Request::segment(1) == 'chart-of-account-type' ? 'active dash-trigger' : '' }}">
                                            <a class="dash-link"
                                                href="{{ route('taxes.index') }}">{{ __('Accounting Setup') }}</a>
                                        </li>
                                    @endif

                                    {{-- @if (Gate::check('manage print settings'))
                                        <li
                                            class="dash-item {{ Request::route()->getName() == 'print-setting' ? ' active' : '' }}">
                                            <a class="dash-link"
                                                href="{{ route('print.setting') }}">{{ __('Print Settings') }}</a>
                                        </li>
                                    @endif --}}

                                </ul>
                            </li>
                        @endif
                    @endif

                    <!--------------------- End Account ----------------------------------->

                    <!--------------------- Start CRM ----------------------------------->

                    {{-- @if ($show_dashboard == 1)
                        @if (Gate::check('manage lead') ||
                                Gate::check('manage deal') ||
                                Gate::check('manage form builder') ||
                                Gate::check('manage contract'))
                            <li
                                class="dash-item dash-hasmenu {{ Request::segment(1) == 'stages' || Request::segment(1) == 'labels' || Request::segment(1) == 'sources' || Request::segment(1) == 'lead_stages' || Request::segment(1) == 'pipelines' || Request::segment(1) == 'deals' || Request::segment(1) == 'leads' || Request::segment(1) == 'form_builder' || Request::segment(1) == 'contractType' || Request::segment(1) == 'form_response' || Request::segment(1) == 'contract' ? ' active dash-trigger' : '' }}">
                                <a href="#!" class="dash-link"><span class="dash-micon"><i
                                            class="ti ti-layers-difference"></i></span><span
                                        class="dash-mtext">{{ __('CRM System') }}</span><span class="dash-arrow"><i
                                            data-feather="chevron-right"></i></span></a>
                                <ul
                                    class="dash-submenu {{ Request::segment(1) == 'stages' || Request::segment(1) == 'labels' || Request::segment(1) == 'sources' || Request::segment(1) == 'lead_stages' || Request::segment(1) == 'leads' || Request::segment(1) == 'form_builder' || Request::segment(1) == 'form_response' || Request::segment(1) == 'deals' || Request::segment(1) == 'pipelines' ? 'show' : '' }}">
                                    @can('manage lead')
                                        <li
                                            class="dash-item {{ Request::route()->getName() == 'leads.list' || Request::route()->getName() == 'leads.index' || Request::route()->getName() == 'leads.show' ? ' active' : '' }}">
                                            <a class="dash-link" href="{{ route('leads.index') }}">{{ __('Leads') }}</a>
                                        </li>
                                    @endcan
                                    @can('manage deal')
                                        <li
                                            class="dash-item {{ Request::route()->getName() == 'deals.list' || Request::route()->getName() == 'deals.index' || Request::route()->getName() == 'deals.show' ? ' active' : '' }}">
                                            <a class="dash-link" href="{{ route('deals.index') }}">{{ __('Deals') }}</a>
                                        </li>
                                    @endcan
                                    @can('manage form builder')
                                        <li
                                            class="dash-item {{ Request::segment(1) == 'form_builder' || Request::segment(1) == 'form_response' ? 'active open' : '' }}">
                                            <a class="dash-link"
                                                href="{{ route('form_builder.index') }}">{{ __('Form Builder') }}</a>
                                        </li>
                                    @endcan
                                    @can('manage contract')
                                        <li
                                            class="dash-item  {{ Request::route()->getName() == 'contract.index' || Request::route()->getName() == 'contract.show' ? 'active' : '' }}">
                                            <a class="dash-link"
                                                href="{{ route('contract.index') }}">{{ __('Contract') }}</a>
                                        </li>
                                    @endcan
                                    @if (Gate::check('manage lead stage') ||
                                            Gate::check('manage pipeline') ||
                                            Gate::check('manage source') ||
                                            Gate::check('manage label') ||
                                            Gate::check('manage contract type') ||
                                            Gate::check('manage stage'))
                                        <li
                                            class="dash-item  {{ Request::segment(1) == 'stages' || Request::segment(1) == 'labels' || Request::segment(1) == 'sources' || Request::segment(1) == 'lead_stages' || Request::segment(1) == 'pipelines' || Request::segment(1) == 'product-category' || Request::segment(1) == 'product-unit' || Request::segment(1) == 'contractType' || Request::segment(1) == 'payment-method' || Request::segment(1) == 'custom-field' || Request::segment(1) == 'chart-of-account-type' ? 'active dash-trigger' : '' }}">
                                            <a class="dash-link"
                                                href="{{ route('pipelines.index') }}   ">{{ __('CRM System Setup') }}</a>

                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif
                    @endif --}}

                    <!--------------------- End CRM ----------------------------------->

                    <!--------------------- Start Project ----------------------------------->

                    {{-- @if ($show_dashboard == 1)
                        @if (Gate::check('manage project') || Gate::check('manage project task') ||
                            Gate::check('manage timesheet') || Gate::check('manage bug report') ||
                            Gate::check('manage project task stage') || Gate::check('manage bug status'))
                            <li
                                class="dash-item dash-hasmenu
                                {{ Request::segment(1) == 'project' ||
                                Request::segment(1) == 'bugs-report' ||
                                Request::segment(1) == 'bugstatus' ||
                                Request::segment(1) == 'project-task-stages' ||
                                Request::segment(1) == 'calendar' ||
                                Request::segment(1) == 'timesheet-list' ||
                                Request::segment(1) == 'taskboard' ||
                                Request::segment(1) == 'timesheet-list' ||
                                Request::segment(1) == 'taskboard' ||
                                Request::segment(1) == 'projects' ||
                                Request::segment(1) == 'project_report'
                                    ? 'active dash-trigger'
                                    : '' }}">
                                <a href="#!" class="dash-link"><span class="dash-micon"><i
                                            class="ti ti-share"></i></span><span
                                        class="dash-mtext">{{ __('Project System') }}</span><span
                                        class="dash-arrow"><i data-feather="chevron-right"></i></span></a>
                                <ul class="dash-submenu">
                                    @can('manage project')
                                        <li
                                            class="dash-item  {{ Request::segment(1) == 'project' || Request::route()->getName() == 'projects.list' || Request::route()->getName() == 'projects.list' || Request::route()->getName() == 'projects.index' || Request::route()->getName() == 'projects.show' || request()->is('projects/*') ? 'active' : '' }}">
                                            <a class="dash-link"
                                                href="{{ route('projects.index') }}">{{ __('Projects') }}</a>
                                        </li>
                                    @endcan
                                    @can('manage project type')
                                    <li class="dash-item dash-hasmenu  {{ (Request::segment(1) == 'project-types') ? ' active' : '' }}">
                                        <a href="{{ route('project-types.index') }}" class="dash-link">
                                            {{__('Project Types')}}
                                        </a>
                                    </li>
                                    @endcan
                                    @can('manage project task')
                                        <li class="dash-item {{ request()->is('taskboard*') ? 'active' : '' }}">
                                            <a class="dash-link"
                                                href="{{ route('taskBoard.view', 'list') }}">{{ __('Tasks') }}</a>
                                        </li>
                                    @endcan
                                    @can('manage timesheet')
                                        <li class="dash-item {{ request()->is('timesheet-list*') ? 'active' : '' }}">
                                            <a class="dash-link"
                                                href="{{ route('timesheet.list') }}">{{ __('Timesheet') }}</a>
                                        </li>
                                    @endcan
                                    @can('manage bug report')
                                        <li class="dash-item {{ request()->is('bugs-report*') ? 'active' : '' }}">
                                            <a class="dash-link"
                                                href="{{ route('bugs.view', 'list') }}">{{ __('Bug') }}</a>
                                        </li>
                                    @endcan
                                    @can('manage project task')
                                        <li class="dash-item {{ request()->is('calendar*') ? 'active' : '' }}">
                                            <a class="dash-link"
                                                href="{{ route('task.calendar', ['all']) }}">{{ __('Task Calendar') }}</a>
                                        </li>
                                    @endcan
                                    @if (\Auth::user()->type != 'super admin')
                                        <li
                                            class="dash-item  {{ Request::segment(1) == 'time-tracker' ? 'active open' : '' }}">
                                            <a class="dash-link"
                                                href="{{ route('time.tracker') }}">{{ __('Tracker') }}</a>
                                        </li>
                                    @endif
                                    @if (\Auth::user()->type == 'company')
                                        <li
                                            class="dash-item  {{ Request::route()->getName() == 'project_report.index' || Request::route()->getName() == 'project_report.show' ? 'active' : '' }}">
                                            <a class="dash-link"
                                                href="{{ route('project_report.index') }}">{{ __('Project Report') }}</a>
                                        </li>
                                    @endif
                                    @if (Gate::check('manage project task stage') || Gate::check('manage bug status'))
                                        <li
                                            class="dash-item dash-hasmenu {{ Request::segment(1) == 'bugstatus' || Request::segment(1) == 'project-task-stages' ? 'active dash-trigger' : '' }}">
                                            <a class="dash-link"
                                                href="#">{{ __('Project System Setup') }}<span
                                                    class="dash-arrow"><i data-feather="chevron-right"></i></span></a>
                                            <ul class="dash-submenu">
                                                @can('manage project task stage')
                                                    <li
                                                        class="dash-item  {{ Request::route()->getName() == 'project-task-stages.index' ? 'active' : '' }}">
                                                        <a class="dash-link"
                                                            href="{{ route('project-task-stages.index') }}">{{ __('Project Task Stages') }}</a>
                                                    </li>
                                                @endcan
                                                @can('manage bug status')
                                                    <li
                                                        class="dash-item {{ Request::route()->getName() == 'bugstatus.index' ? 'active' : '' }}">
                                                        <a class="dash-link"
                                                            href="{{ route('bugstatus.index') }}">{{ __('Bug Status') }}</a>
                                                    </li>
                                                @endcan
                                            </ul>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif
                    @endif --}}

                    <!--------------------- End Project ----------------------------------->



                    <!--------------------- Start User Managaement System ----------------------------------->

                    @if ( \Auth::user()->type != 'super admin' &&
                            (Gate::check('manage user') || Gate::check('manage role') || Gate::check('manage client')))
                        <li
                            class="dash-item dash-hasmenu {{ Request::segment(1) == 'users' ||
                            Request::segment(1) == 'roles' ||
                            Request::segment(1) == 'clients' ||
                            Request::segment(1) == 'userlogs'
                                ? ' active dash-trigger'
                                : '' }}">
                            <a href="#!"
                                class="dash-link {{ Request::segment(1) == 'users' || Request::segment(1) == 'roles' || Request::segment(1) == 'clients' ? ' active dash-trigger' : '' }}"><span
                                    class="dash-micon"><i class="ti ti-users"></i></span><span
                                    class="dash-mtext">{{ __('User Management') }}</span><span class="dash-arrow"><i
                                        data-feather="chevron-right"></i></span></a>
                            <ul class="dash-submenu">
                                @can('manage user')
                                    <li
                                        class="dash-item {{ Request::route()->getName() == 'users.index' || Request::route()->getName() == 'users.create' || Request::route()->getName() == 'users.edit' || Request::route()->getName() == 'user.userlog' ? ' active' : '' }}">
                                        <a class="dash-link" href="{{ route('users.index') }}">{{ __('User') }}</a>
                                    </li>
                                @endcan
                                @can('manage role')
                                    <li
                                        class="dash-item {{ Request::route()->getName() == 'roles.index' || Request::route()->getName() == 'roles.create' || Request::route()->getName() == 'roles.edit' ? ' active' : '' }} ">
                                        <a class="dash-link" href="{{ route('roles.index') }}">{{ __('Role') }}</a>
                                    </li>
                                @endcan
                                @can('manage client')
                                    <li
                                        class="dash-item {{ Request::route()->getName() == 'clients.index' || Request::segment(1) == 'clients' || Request::route()->getName() == 'clients.edit' ? ' active' : '' }}">
                                        <a class="dash-link"
                                            href="{{ route('clients.index') }}">{{ __('Client') }}</a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    @endif

                    <!--------------------- End User Managaement System----------------------------------->


                    <!--------------------- Start Products System ----------------------------------->

                    {{-- @if (Gate::check('manage product & service'))
                        <li class="dash-item dash-hasmenu">
                            <a href="#!" class="dash-link ">
                                <span class="dash-micon"><i class="ti ti-shopping-cart"></i></span><span
                                    class="dash-mtext">{{ __('Products System') }}</span><span class="dash-arrow">
                                    <i data-feather="chevron-right"></i></span>
                            </a>
                            <ul class="dash-submenu">
                                @if (Gate::check('manage product & service'))
                                    <li
                                        class="dash-item {{ Request::segment(1) == 'productservice' ? 'active' : '' }}">
                                        <a href="{{ route('productservice.index') }}"
                                            class="dash-link">{{ __('Product & Services') }}
                                        </a>
                                    </li>
                                    <li class="dash-item {{ Request::segment(1) == 'productstock' ? 'active' : '' }}">
                                        <a href="{{ route('productstock.index') }}"
                                            class="dash-link">{{ __('Product Stock') }}
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif --}}

                  {{--  @if (Gate::check('manage visa') || Gate::check('view visa') || Gate::check('add visa') || Gate::check('edit visa') || Gate::check('delete visa') || Gate::check('create visa'))
                        <li class="dash-item dash-hasmenu {{ Request::segment(1) == 'visa' ? ' active dash-trigger' : '' }}">
                            <a href="#!" class="dash-link ">
                                <span class="dash-micon"><i class="ti ti-shopping-cart"></i></span><span
                                    class="dash-mtext">{{ __('Visa System') }}</span><span class="dash-arrow">
                                    <i data-feather="chevron-right"></i></span>
                            </a>
                            <ul class="dash-submenu {{ Request::segment(1) == 'visa' ? 'show' : '' }}">

                            </ul>
                        </li>
                    @endif

                     @if (Gate::check('manage product & service'))
                        <li class="dash-item dash-hasmenu">
                            <a href="#!" class="dash-link ">
                                <span class="dash-micon"><i class="ti ti-shopping-cart"></i></span><span
                                    class="dash-mtext">{{ __('Products System') }}</span><span class="dash-arrow">
                                    <i data-feather="chevron-right"></i></span>
                            </a>
                            <ul class="dash-submenu">
                                @if (Gate::check('manage product & service'))
                                    <li
                                        class="dash-item {{ Request::segment(1) == 'productservice' ? 'active' : '' }}">
                                        <a href="{{ route('productservice.index') }}"
                                            class="dash-link">{{ __('Product & Services') }}
                                        </a>
                                    </li>
                                    <li class="dash-item {{ Request::segment(1) == 'productstock' ? 'active' : '' }}">
                                        <a href="{{ route('productstock.index') }}"
                                            class="dash-link">{{ __('Product Stock') }}
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif

                     --}}

                    <!--------------------- End Products System ----------------------------------->

                    <!--------------------- Start POs System ----------------------------------->

                    {{-- @if($show_dashboard == 1)
                        @if (Gate::check('manage warehouse') ||
                                Gate::check('manage purchase') ||
                                Gate::check('manage quotation') ||
                                Gate::check('create barcode') ||
                                Gate::check('manage pos') ||
                                Gate::check('manage print settings'))
                            <li
                                class="dash-item dash-hasmenu {{ Request::segment(1) == 'warehouse' || Request::segment(1) == 'purchase' || Request::route()->getName() == 'pos.barcode' || Request::route()->getName() == 'pos.print' || Request::route()->getName() == 'pos.show' ? ' active dash-trigger' : '' }}">
                                <a href="#!" class="dash-link"><span class="dash-micon"><i
                                            class="ti ti-layers-difference"></i></span><span
                                        class="dash-mtext">{{ __('POS System') }}</span><span class="dash-arrow"><i
                                            data-feather="chevron-right"></i></span></a>
                                <ul
                                    class="dash-submenu {{ Request::segment(1) == 'warehouse' || Request::segment(1) == 'purchase' || Request::route()->getName() == 'pos.barcode' || Request::route()->getName() == 'pos.print' || Request::route()->getName() == 'pos.show' ? 'show' : '' }}">
                                    @can('manage warehouse')
                                        <li
                                            class="dash-item {{ Request::route()->getName() == 'warehouse.index' || Request::route()->getName() == 'warehouse.show' ? ' active' : '' }}">
                                            <a class="dash-link"
                                                href="{{ route('warehouse.index') }}">{{ __('Warehouse') }}</a>
                                        </li>
                                    @endcan
                                    @can('manage purchase')
                                        <li
                                            class="dash-item {{ Request::route()->getName() == 'purchase.index' || Request::route()->getName() == 'purchase.create' || Request::route()->getName() == 'purchase.edit' || Request::route()->getName() == 'purchase.show' ? ' active' : '' }}">
                                            <a class="dash-link"
                                                href="{{ route('purchase.index') }}">{{ __('Purchase') }}</a>
                                        </li>
                                    @endcan
                                    @can('manage quotation')
                                        <li
                                            class="dash-item {{ Request::route()->getName() == 'quotation.index' || Request::route()->getName() == 'quotations.create' || Request::route()->getName() == 'quotation.edit' || Request::route()->getName() == 'quotation.show' ? ' active' : '' }}">
                                            <a class="dash-link"
                                                href="{{ route('quotation.index') }}">{{ __('Quotation') }}</a>
                                        </li>
                                    @endcan
                                    @can('manage pos')
                                        <li
                                            class="dash-item {{ Request::route()->getName() == 'pos.index' ? ' active' : '' }}">
                                            <a class="dash-link" href="{{ route('pos.index') }}">{{ __(' Add POS') }}</a>
                                        </li>

                                        <li
                                            class="dash-item {{ Request::route()->getName() == 'pos.report' || Request::route()->getName() == 'pos.show' ? ' active' : '' }}">
                                            <a class="dash-link" href="{{ route('pos.report') }}">{{ __('POS') }}</a>
                                        </li>
                                    @endcan
                                    @can('manage warehouse')
                                        <li
                                            class="dash-item {{ Request::route()->getName() == 'warehouse-transfer.index' || Request::route()->getName() == 'warehouse-transfer.show' ? ' active' : '' }}">
                                            <a class="dash-link"
                                                href="{{ route('warehouse-transfer.index') }}">{{ __('Transfer') }}</a>
                                        </li>
                                    @endcan
                                    @can('create barcode')
                                        <li
                                            class="dash-item {{ Request::route()->getName() == 'pos.barcode' || Request::route()->getName() == 'pos.print' ? ' active' : '' }}">
                                            <a class="dash-link"
                                                href="{{ route('pos.barcode') }}">{{ __('Print Barcode') }}</a>
                                        </li>
                                    @endcan
                                    @can('manage pos')
                                        <li
                                            class="dash-item {{ Request::route()->getName() == 'pos-print-setting' ? ' active' : '' }}">
                                            <a class="dash-link"
                                                href="{{ route('pos.print.setting') }}">{{ __('Print Settings') }}</a>
                                        </li>
                                    @endcan

                                </ul>
                            </li>
                        @endif
                    @endif --}}

                    <!--------------------- End POs System ----------------------------------->

                    {{-- <li class="dash-item dash-hasmenu {{ Request::segment(1) == 'support' ? 'active' : '' }}">
                        <a href="{{ route('support.index') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-headphones"></i></span><span
                                class="dash-mtext">{{ __('Support System') }}</span>
                        </a>
                    </li> --}}
                   {{-- <li class="dash-item dash-hasmenu {{ Request::segment(1) == 'zoom-meeting' || Request::segment(1) == 'zoom-meeting-calender' ? 'active' : '' }}">
                        <a href="{{ route('zoom-meeting.index') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-user-check"></i></span><span
                                class="dash-mtext">{{ __('Zoom Meeting') }}</span>
                        </a>
                    </li>
                    <li class="dash-item dash-hasmenu {{ Request::segment(1) == 'chats' ? 'active' : '' }}">
                        <a href="{{ url('chats') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-message-circle"></i></span><span
                                class="dash-mtext">{{ __('Messenger') }}</span>
                        </a>
                    </li>

                    @if (\Auth::user()->type == 'company')
                        <li
                            class="dash-item dash-hasmenu  {{ Request::route()->getName() == 'notification-templates.index' || Request::route()->getName() == 'email_template.index' || Request::segment(1) == 'notification-templates-lang' || Request::segment(1) == 'email_template_lang' ? 'dash-trigger active' : '' }}">
                            <a href="#!" class="dash-link"><span class="dash-micon"><i
                                        class="ti ti-receipt"></i></span><span
                                    class="dash-mtext">{{ __('Templates') }}</span><span class="dash-arrow"><i
                                        data-feather="chevron-right"></i></span></a>
                            <ul class="dash-submenu ">
                                <li
                                    class="dash-item {{ Request::segment(1) == 'email_template' || Request::route()->getName() == 'manage.email.language' ? 'active' : '' }}">
                                    <a class="dash-link"
                                        href="{{ route('email_template.index') }}">{{ __('Email Template') }}</a>
                                </li>
                                <li
                                    class="dash-item {{ Request::route()->getName() == 'notification-templates.index' || Request::segment(1) == 'notification-templates-lang' ? 'active' : '' }}">
                                    <a class="dash-link"
                                        href="{{ route('notification_templates.index') }}">{{ __('Notification Template') }}</a>
                                </li>

                            </ul>

                        </li>
                        @include('landingpage::menu.landingpage')
                    @endif--}}



                    <!--------------------- Start System Setup ----------------------------------->

                    @if (Gate::check('manage company settings'))
                        <li
                            class="dash-item dash-hasmenu {{ Request::segment(1) == 'settings' ? ' active' : '' }}">
                            <a href="{{ route('settings') }}" class="dash-link">
                                <span class="dash-micon"><i class="ti ti-settings"></i></span><span
                                    class="dash-mtext">{{ __('Settings') }}</span>
                            </a>
                        </li>
                    @endif




                    <!--------------------- End System Setup ----------------------------------->

            </ul>
        @endif
        @if (\Auth::user()->type == 'client')
            <ul class="dash-navbar">
                {{-- Client user: use main account dashboard --}}
                <li class="dash-item dash-hasmenu {{ Request::segment(1) == 'dashboard' || Request::segment(1) == null || Request::segment(1) == 'account-dashboard' ? ' active' : '' }}">
                    <a href="{{ route('dashboard') }}" class="dash-link">
                        <span class="dash-micon"><i class="ti ti-home"></i></span><span
                            class="dash-mtext">{{ __('Dashboard') }}</span>
                    </a>
                </li>

                {{-- Client user: Only show Accounting System > Sales > Ticket --}}
                <li
                    class="dash-item dash-hasmenu {{ Request::segment(1) == 'invoice' ? ' active dash-trigger' : '' }}">
                    <a href="#!" class="dash-link"><span class="dash-micon"><i
                                class="ti ti-box"></i></span><span
                            class="dash-mtext">{{ __('Accounting System ') }}</span><span
                            class="dash-arrow">
                            <i data-feather="chevron-right"></i></span>
                    </a>
                    <ul class="dash-submenu">
                        <li
                            class="dash-item dash-hasmenu {{ Request::segment(1) == 'invoice' ? 'active dash-trigger' : '' }}">
                            <a class="dash-link" href="#">{{ __('Sales') }}<span
                                    class="dash-arrow"><i data-feather="chevron-right"></i></span></a>
                            <ul class="dash-submenu">
                                <li
                                    class="dash-item {{ Request::route()->getName() == 'invoice.index' || Request::route()->getName() == 'invoice.create' || Request::route()->getName() == 'invoice.edit' || Request::route()->getName() == 'invoice.show' ? ' active' : '' }}">
                                    <a class="dash-link"
                                        href="{{ route('invoice.index') }}">{{ __('Ticket') }}</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
            </ul>
        @endif


    </div>
</div>
</nav>
