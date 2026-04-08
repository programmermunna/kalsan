<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'lead_id',
        'log_type',
        'remark',
    ];

    private static $userData = NULL;
    
    public function getLeadRemark(){
        self::$userData = self::fetchgetLeadRemark();
        return self::$userData;
    }

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function fetchgetLeadRemark()
    {
        $remark = json_decode($this->remark, true);
        if($remark)
        {
            $user = $this->user;

            if($user)
            {
                $user_name = $user->name;
            }
            else
            {
                $user_name = '';
            }

            if($this->log_type == 'Upload File')
            {
                return $user_name . ' ' . __('Upload new file') . ' <b>' . $remark['file_name'] . '</b>';
            }
            elseif($this->log_type == 'Add Product')
            {
                return $user_name . ' ' . __('Add new Products') . " <b>" . $remark['title'] . "</b>";
            }
            elseif($this->log_type == 'Update Sources')
            {
                return $user_name . ' ' . __('Update Sources');
            }
            elseif($this->log_type == 'create lead call')
            {
                return $user_name . ' ' . __('Create new Lead Call');
            }
            elseif($this->log_type == 'create lead email')
            {
                return $user_name . ' ' . __('Create new Lead Email');
            }
            elseif($this->log_type == 'Move')
            {
                return $user_name . " " . __('Moved the deal') . " <b>" . $remark['title'] . "</b> " . __('from') . " " . __(ucwords($remark['old_status'])) . " " . __('to') . " " . __(ucwords($remark['new_status']));
            }
        }
        else
        {
            return $this->remark;
        }
    }

    public function logIcon()
    {
        $type = $this->log_type;
        $icon = '';

        if(!empty($type))
        {
            if($type == 'Move')
            {
                $icon = 'ti-arrows-maximize';
            }
            elseif($type == 'Add Product')
            {
                $icon = 'ti-layout-grid-add';
            }
            elseif($type == 'Upload File')
            {
                $icon = 'ti-cloud-upload';
            }
            elseif($type == 'Update Sources')
            {
                $icon = 'ti-brand-open-source';
            }
            elseif($type == 'create lead call')
            {
                $icon = 'ti-phone-plus';
            }
            elseif($type == 'create lead email')
            {
                $icon = 'ti-mail';
            }
        }

        return $icon;
    }
}
