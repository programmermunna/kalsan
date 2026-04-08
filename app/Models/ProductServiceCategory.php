<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ProductServiceCategory extends Model
{
    protected $fillable = [
        'name',
        'type',
        'created_by',
    ];

    public static $categoryType = [
        'Product',
        'Service',
        'Income',
        'Expense',
    ];

    public static $catTypes = [
        'product' => 'Product',
        'service' => 'Service',
        'income' => 'Income',
        'expense' => 'Expense',
        'asset'=> 'Asset',
        'liability' => 'Liability',
        'equity' => 'Equity',
        'costs of good sold' => 'Costs of Goods Sold',
    ];

    public function categories()
    {
        return $this->hasMany('App\Models\Revenue', 'category_id', 'id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'category_id');
    }

    public function bills()
    {
        return $this->hasMany(Bill::class, 'category_id');
    }

    public function incomeCategoryRevenueAmount()
    {
        $year    = date('Y');
        $revenue = $this->hasMany('App\Models\Revenue', 'category_id', 'id')->where('created_by', \Auth::user()->creatorId())->whereRAW('YEAR(date) =?', [$year])->sum('amount');
        $invoices     =  $this->invoices()->with('items')->get()
        ->sum->getTotal();

        $totalIncome = (!empty($revenue) ? $revenue : 0) + (!empty($invoices) ? ($invoices) : 0);

        return $totalIncome;
    }

    public function expenseCategoryAmount()
    {
        $year    = date('Y');
        $payment = $this->hasMany('App\Models\Payment', 'category_id', 'id')
            ->where('created_by', \Auth::user()->creatorId())
            ->whereRAW('YEAR(date) =?', [$year])
            ->sum('amount');

            $bills     =  $this->bills()->with(['items'])->get()
            ->sum->getTotal();
        $totalExpense = (!empty($payment) ? $payment : 0) + (!empty($bills) ? ($bills) : 0);

        return $totalExpense;

    }
    public static function getallCategories()
    {

        $cat = ProductServiceCategory::select('product_service_categories.*', \DB::raw("COUNT(pu.category_id) product_services"))
            ->leftjoin('product_services as pu','product_service_categories.id' ,'=','pu.category_id')
            ->where('product_service_categories.created_by', '=', Auth::user()->creatorId())
            ->where('product_service_categories.type', 0)
            ->orderBy('product_service_categories.id', 'DESC')->groupBy('product_service_categories.id')->get();

        return $cat;
    }

    public function chartAccount()
    {
        return $this->hasOne('App\Models\ChartOfAccount', 'id', 'chart_account_id');
    }
}
