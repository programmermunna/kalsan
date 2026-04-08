<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookSetting extends Model
{
    protected $fillable = [
        'module',
        'url',
        'method',
        'created_by',
    ];

    public static $modules = [
        'new lead' => 'New Lead',
        'lead to deal conversion ' => 'Lead to Deal Conversion',
        'new project' => 'New Project',
        'task stage updated' => 'Task Stage Updated',
        'new deal' => 'New Deal',
        'new contract' => 'New Contract',
        'new task' => 'New Task',
        'new task comment' => 'New Task Comment',
        'new monthly payslip' => 'New Monthly Payslip',
        'new announcement' => 'New Announcement',
        'new support ticket' => 'New Support Ticket',
        'new meeting' => 'New Meeting',
        'new award' => 'New Award',
        'new holiday' => 'New Holiday',
        'new event' => 'New Event',
        'new company policy' => 'New Company Policy',
        'new invoice' => 'New Invoice',
        'new bill' => 'New Bill',
        'new budget' => 'New Budget',
        'new revenue' => 'New Revenue',
        'new invoice payment' => 'New Invoice Payment',

    ];

    public static $method =[
        'get' =>'GET',
        'post' =>'POST',
    ];


}
