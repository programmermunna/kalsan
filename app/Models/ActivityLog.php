<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'project_id',
        'task_id',
        'deal_id',
        'log_type',
        'remark',
    ];

    private static $userData = NULL;

    public function getRemark(){
        self::$userData = self::fetchgetRemark();
        return self::$userData;
    }


    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function userdetail()
    {
        return $this->hasOne('App\Models\UserDetail', 'user_id', 'user_id');
    }

    public function fetchgetRemark()
    {
        $remark = json_decode($this->remark, true);

        if($remark)
        {
            $user_name = $this->user ? $this->user->name : '';

            if($this->log_type == 'Invite User')
            {
                return $user_name . ' ' . __('has invited') . ' <b>' . $remark['title'] . '</b>';
            }
            elseif($this->log_type == 'User Assigned to the Task')
            {
                return $user_name . ' ' . __('has assigned task ') . ' <b>' . $remark['task_name'] . '</b> ' . __(' to') . ' <b>' . $remark['member_name'] . '</b>';
            }
            elseif($this->log_type == 'User Removed from the Task')
            {
                return $user_name . ' ' . __('has removed ') . ' <b>' . $remark['member_name'] . '</b>' . __(' from task') . ' <b>' . $remark['task_name'] . '</b>';
            }
            elseif($this->log_type == 'Upload File')
            {
                return $user_name . ' ' . __('Upload new file') . ' <b>' . $remark['file_name'] . '</b>';
            }
            elseif($this->log_type == 'Create Bug')
            {
                return $user_name . ' ' . __('Created new bug') . ' <b>' . $remark['title'] . '</b>';
            }
            elseif($this->log_type == 'Create Milestone')
            {
                return $user_name . ' ' . __('Create new milestone') . " <b>" . $remark['title'] . "</b>";
            }
            elseif($this->log_type == 'Create Task')
            {
                return $user_name . ' ' . __('Create new Task') . " <b>" . $remark['title'] . "</b>";
            }
            elseif($this->log_type == 'Move Task')
            {
                return $user_name . ' ' . __('Moved the Task') . " <b>" . $remark['title'] . "</b> " . __('from') . " " . __(ucwords($remark['old_stage'])) . " " . __('to') . " " . __(ucwords($remark['new_stage']));
            }
            elseif($this->log_type == 'Create Expense')
            {
                return $user_name . ' ' . __('Create new Expense') . " <b>" . $remark['title'] . "</b>";
            }
            elseif($this->log_type == 'Create Task')
            {
                return $user_name . ' ' . __('Create new Task') . " <b>" . $remark['title'] . "</b>";
            }
            elseif($this->log_type == 'Add Product')
            {
                return $user_name . ' ' . __('Add new Products') . " <b>" . $remark['title'] . "</b>";
            }
            elseif($this->log_type == 'Update Sources')
            {
                return $user_name . ' ' . __('Update Sources');
            }
            elseif($this->log_type == 'Create Deal Call')
            {
                return $user_name . ' ' . __('Create new Deal Call');
            }
            elseif($this->log_type == 'Create Deal Email')
            {
                return $user_name . ' ' . __('Create new Deal Email');
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
