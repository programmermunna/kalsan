<?php

namespace App\Models;

use App\Models\Company;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    public static function get_activity($module_type,$module_id)
    {
        $result=['name'=>'-'];
        if($module_type=='contact')
        {
            $contact= Contact::where('id',$module_id)->orderBy('id','desc')->first();
            if($contact)
            {
                $result =['name' => $contact->name];
            }
        }
        elseif($module_type=='company')
        {
            $company= Company::where('id',$module_id)->orderBy('id','desc')->first();
            if($company)
                {
                    $result =['name' => $company->name];
                }
        }
        elseif($module_type=='employee')
        {
            $employee= HrmEmployee::where('id',$module_id)->orderBy('id','desc')->first();
            if($employee)
                {
                    $result =['name' => $employee->first_name.' '.$employee->last_name];
                }
        }
        return $result;
    }
    public function logIcon()
    {
        $type = $this->log_type;
        $icon = '';

        if(!empty($type))
        {
            if($type == 'Invite User')
            {
                $icon = 'ti-user';
            }
            else if($type == 'User Assigned to the Task')
            {
                $icon = 'ti-user-check';
            }
            else if($type == 'User Removed from the Task')
            {
                $icon = 'ti-user-x';
            }
            else if($type == 'Upload File')
            {
                $icon = 'ti-cloud-upload';
            }
            else if($type == 'Create Milestone')
            {
                $icon = 'ti-crop';
            }
            else if($type == 'Create Bug')
            {
                $icon = 'ti-bug';
            }
            else if($type == 'Create Task')
            {
                $icon = 'ti-square-plus';
            }
            else if($type == 'Move Task')
            {
                $icon = 'ti-command';
            }
            else if($type == 'Create Expense')
            {
                $icon = 'ti-clipboard-list';
            }
            else if($type == 'Move')
            {
                $icon = 'ti-arrows-maximize';
            }
            elseif($type == 'Add Product')
            {
                $icon = 'ti-shopping-cart-plus';
            }
            elseif($type == 'Update Sources')
            {
                $icon = 'ti-brand-open-source';
            }
            elseif($type == 'Create Deal Call')
            {
                $icon = 'ti-phone-plus';
            }
            elseif($type == 'Create Deal Email')
            {
                $icon = 'ti-record-mail';
            }
            elseif($type == 'Create Invoice')
            {
                $icon = 'ti-file-plus';
            }
            elseif($type == 'Add Contact')
            {
                $icon = 'ti-notebook';
            }
            elseif($type == 'Create Task')
            {
                $icon = 'ti-list';
            }

        }

        return $icon;
    }


}
