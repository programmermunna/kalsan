<?php

namespace App\Exports;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EmployeeExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $data = Employee::get();
        foreach($data as $k => $employee)
        {
            unset($employee->id,$employee->password,$employee->user_id,$employee->employee_id,$employee->biometric_emp_id,$employee->documents,$employee->salary_type,$employee->account,$employee->tax_payer_id,$employee->is_active,$employee->created_by,$employee->created_at,$employee->updated_at);
            $data[$k]["branch_id"]=!empty($employee->branch)?$employee->branch->name:'-';
            $data[$k]["department_id"]=!empty($employee->department)?$employee->department->name:'-';
            $data[$k]["designation_id"]= !empty($employee->designation) ? $employee->designation->name : '-';
            $data[$k]["salary"]=Employee::employee_salary($employee->salary);

        }
        return $data;
    }

    public function headings(): array
    {
        return [
            "Name",
            "Date of Birth",
            "Gender",
            "Phone Number",
            "Address",
            "Email ID",
            "Branch",
            "Department",
            "Designation",
            "Date of Join",
            "Account Holder Name",
            "Account Number",
            "Bank Name",
            "Bank Identifier Code",
            "Branch Location",
            "Salary",

        ];
    }
}
