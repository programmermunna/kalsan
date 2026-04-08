<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use Illuminate\Http\Request;

class DesignationController extends Controller
{
    public function index()
    {

        if(\Auth::user()->can('manage designation'))
        {
            $designations = Designation::where('created_by', '=', \Auth::user()->creatorId())->get();

            return view('designation.index', compact('designations'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if(\Auth::user()->can('create designation'))
        {
            $branchs     = Branch::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');

            $departments = Department::where('created_by', '=', \Auth::user()->creatorId())->get();
            $departments = $departments->pluck('name', 'id');

            return view('designation.create', compact('departments','branchs'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function store(Request $request)
    {

        if(\Auth::user()->can('create designation'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'branch_id' => 'required',
                                   'department_id' => 'required',
                                   'name' => 'required|max:20',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $designation                = new Designation();
            $designation->branch_id     = $request->branch_id;
            $designation->department_id = $request->department_id;
            $designation->name          = $request->name;
            $designation->created_by    = \Auth::user()->creatorId();

            $designation->save();

            return redirect()->route('designation.index')->with('success', __('Designation  successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show(Designation $designation)
    {
        return redirect()->route('designation.index');
    }

    public function edit(Designation $designation)
    {

        if(\Auth::user()->can('edit designation'))
        {
            if($designation->created_by == \Auth::user()->creatorId())
            {
                if (!empty($designation->branch_id)) {
                    $branchs     = Branch::where('id', $designation->branch_id)->first()->pluck('name', 'id');
                } else {
                    $branchs     = Branch::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
                }
                $departments = Department::where('id', $designation->department_id)->first();
                $departments = $departments->pluck('name', 'id');

                return view('designation.edit', compact('designation', 'departments','branchs'));
            }
            else
            {
                return response()->json(['error' => __('Permission denied.')], 401);
            }
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function update(Request $request, Designation $designation)
    {
        if(\Auth::user()->can('edit designation'))
        {
            if($designation->created_by == \Auth::user()->creatorId())
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'branch_id' => 'required',
                                       'department_id' => 'required',
                                       'name' => 'required|max:20',
                                   ]
                );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }
                try {
                    $branch = Department::where('id', $request->department_id)->where('created_by', '=', \Auth::user()->creatorId())->first()->branch->id;
                } catch (\Exception $e) {
                    $branch = null;
                }
                $designation->branch_id     = $branch;
                $designation->name          = $request->name;
                $designation->department_id = $request->department_id;
                $designation->save();

                return redirect()->route('designation.index')->with('success', __('Designation  successfully updated.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(Designation $designation)
    {
        if(\Auth::user()->can('delete designation'))
        {
            if($designation->created_by == \Auth::user()->creatorId())
            {
                $employee     = Employee::where('designation_id', $designation->id)->get();
                if (count($employee) == 0) {
                    $designation->delete();

                    return redirect()->route('designation.index')->with('success', __('Designation successfully deleted.'));
                } else {
                    return redirect()->route('designation.index')->with('error', __('This designation has employees. Please remove the employee from this designation.'));
                }
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
