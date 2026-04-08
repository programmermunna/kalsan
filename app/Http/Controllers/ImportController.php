<?php

namespace App\Http\Controllers;
use App\Models\Utility;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function getTableWiseFields($table)
    {
        $error = '';
        switch ($table) {
            case 'attendance_employees':
                $extraFields = ['id', 'status', 'late', 'early_leaving', 'overtime', 'total_rest', 'created_by', 'created_at', 'updated_at'];
                $tableFields = Utility::getTableFields($table, $extraFields);
                if ($tableFields['status']) {
                    if (($key = array_search('employee_id', $tableFields['data'])) !== false) {
                        $tableFields['data'][$key] = 'employee_email';
                    }
                    $route = "attendance.import.data";
                }
                break;
            case 'customers':
                $extraFields = ['id', 'customer_id', 'tax_number', 'password', 'avatar', 'is_active', 'email_verified_at', 'lang', 'remember_token', 'created_by', 'created_at', 'updated_at'];
                $tableFields = Utility::getTableFields($table, $extraFields);
                if ($tableFields['status']) {
                    $route = "customer.import.data";
                }
                break;
            case 'venders':
                $extraFields = ['id', 'vender_id', 'password', 'avatar', 'is_active', 'email_verified_at', 'lang', 'remember_token', 'created_by', 'created_at', 'updated_at'];
                $tableFields = Utility::getTableFields($table, $extraFields);
                if ($tableFields['status']) {
                    $route = "vender.import.data";
                }
                break;
            case 'product_services':
                $extraFields = ['id', 'tax_id', 'category_id', 'unit_id', 'sale_chartaccount_id', 'expense_chartaccount_id','pro_image', 'created_by', 'created_at','updated_at'];
                $tableFields = Utility::getTableFields($table, $extraFields);
                if ($tableFields['status']) {
                    $route = "productservice.import.data";
                }
                break;

            default:
                $error = 'Something went wrong!';
                $tableFields['status'] = false;
                break;
        }

        if ($tableFields['status']) {
            $fields = $tableFields['data'];
        } else {
            $error = $tableFields['message'];
        }
        return [
            'route' => $route,
            'fields' => $fields,
            'error' => $error,
        ];
    }

    public function fileImport(Request $request)
    {
        session_start();

        $error = '';

        $html = '';

        $fields = [];
        $route = '';

        if ($request->hasFile('file') && $request->file->getClientOriginalName() != '') {
            $file_array = explode(".", $request->file->getClientOriginalName());

            $extension = end($file_array);
            if ($extension == 'csv') {
                $file_data = fopen($request->file->getRealPath(), 'r');
                $file_header = fgetcsv($file_data);

                $tableFields = $this->getTableWiseFields($request->table);
                if ($tableFields['error'] != '') {
                    $error = $tableFields['error'];
                } else {
                    $fields = $tableFields['fields'];
                }

                $limit = 0;
                $temp_data = [];
                while (($row = fgetcsv($file_data)) !== false) {
                    $limit++;
                    $html .= '<tr>';
                    for ($count = 0; $count < count($row); $count++) {
                        $html .= '<td>' . $row[$count] . '</td>';
                    }
                    $html .= '</tr>';
                    $temp_data[] = $row;
                }

                $_SESSION['file_data'] = $temp_data;
            } else {
                $error = 'Only <b>.csv</b> file allowed';
            }
        } else {

            $error = 'Please Select CSV File';
        }
        $output = array(
            'error' => $error,
            'output' => $html,
            'fields' => $fields,
        );

        return json_encode($output);
    }

    public function fileImportModal(Request $request)
    {
        $fields = [];
        $route  = '';
        $tableFields = $this->getTableWiseFields($request->table);
        if ($tableFields['error'] != '') {
            $error = $tableFields['error'];
        } else {
            $fields = json_encode($tableFields['fields']);
            $route = $tableFields['route'];
        }
        return view('import.import_modal', compact('fields', 'route'));
    }
}
