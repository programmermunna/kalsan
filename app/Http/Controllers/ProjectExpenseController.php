<?php

namespace App\Http\Controllers;

use App\Models\ProjectExpense;
use App\Models\Project;
use App\Models\Utility;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class ProjectExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($project_id)
    {
        if(\Auth::user()->can('manage project expense'))
        {
            $project     = Project::find($project_id);
            $amount      = $project->expense->sum('amount');
            $expense_cnt = Utility::projectCurrencyFormat($project_id, $amount) . '/' . Utility::projectCurrencyFormat($project_id, $project->budget);

            return view('project_expense.index', compact('project', 'expense_cnt'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($project_id)
    {
        if(\Auth::user()->can('create project expense'))
        {
            $project = Project::find($project_id);

            return view('project_expense.create', compact('project'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $project_id)
    {
        if(\Auth::user()->can('create project expense'))
        {
            $usr       = \Auth::user();
            $validator = Validator::make(
                $request->all(), [
                                'name' => 'required|max:120',
                                'amount' => 'required|numeric|min:0',
                            ]
            );

            if($validator->fails())
            {
                return redirect()->back()->with('error', Utility::errorFormat($validator->getMessageBag()));
            }

            $post               = $request->all();
            $post['project_id'] = $project_id;
            $post['date']       = (!empty($request->date)) ? date("Y-m-d H:i:s", strtotime($request->start_date)): null;
            $post['created_by'] = $usr->id;

            if($request->hasFile('attachment'))
            {
                $fileNameToStore    = time() . '.' . $request->attachment->getClientOriginalExtension();
                $path               = $request->file('attachment')->storeAs('expense', $fileNameToStore);
                $post['attachment'] = $path;
            }

            $expense = ProjectExpense::create($post);

            // Make entry in activity log
            ActivityLog::create(
                [
                    'user_id' => $usr->id,
                    'project_id' => $project_id,
                    'log_type' => 'Create Expense',
                    'remark' => json_encode(['title' => $expense->name]),
                ]
            );

            return redirect()->back()->with('success', __('Expense added successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ProjectExpense $projectExpense)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($project_id, $expense_id)
    {
        if(\Auth::user()->can('edit expense'))
        {
            $project = Project::find($project_id);
            $expense = ProjectExpense::find($expense_id);

            return view('project_expense.edit', compact('project', 'expense'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $project_id, $expense_id)
    {
        if(\Auth::user()->can('edit project expense'))
        {
            $validator = Validator::make(
                $request->all(), [
                                'name' => 'required|max:120',
                                'amount' => 'required|numeric|min:0',
                            ]
            );

            if($validator->fails())
            {
                return redirect()->back()->with('error', Utility::errorFormat($validator->getMessageBag()));
            }

            $expense = ProjectExpense::find($expense_id);
            $expense->name = $request->name;
            $expense->date = date("Y-m-d H:i:s", strtotime($request->date));
            $expense->amount =$request->amount;
            $expense->task_id = $request->task_id;
            $expense->description = $request->description;

            if($request->hasFile('attachment'))
            {
                Utility::checkFileExistsnDelete([$expense->attachment]);

                $fileNameToStore    = time() . '.' . $request->attachment->extension();
                $path =  $request->file('attachment')->storeAs('expense', $fileNameToStore);
                $expense->attachment = $path;
            }

            $expense->save();

            return redirect()->back()->with('success', __('Expense Updated successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($expense_id)
    {
        if(\Auth::user()->can('delete project expense'))
        {
            $expense = ProjectExpense::find($expense_id);
            Utility::checkFileExistsnDelete([$expense->attachment]);
            $expense->delete();

            return redirect()->back()->with('success', __('Expense Deleted successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }
}
