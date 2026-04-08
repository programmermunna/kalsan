<?php

namespace App\Http\Controllers;

use App\Models\ProjectType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectTypeController extends Controller
{
    /**
     * Display a listing of the project types.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    { if (\Auth::user()->can('manage project type')) {
        $projectTypes = ProjectType::all();
        return view('project_types.index', compact('projectTypes'));
    }else{
        return redirect()->back()->with('error', __('Permission denied.'));
    }
    }

    /**
     * Show the form for creating a new project type.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {  if (\Auth::user()->can('create project type')) {
        return view('project_types.create');
     } else{
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Store a newly created project type in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (\Auth::user()->can('create project type')) {
        $request->validate([
            'project_type_name' => 'required|string|max:255|unique:project_types',
        ]);

        $projectType = new ProjectType();
        $projectType->project_type_name = $request->project_type_name;
        $projectType->created_by = Auth::id();
        $projectType->save();

        return redirect()->route('project-types.index')
            ->with('success', 'Project Type created successfully.');
    }else{
        return redirect()->back()->with('error', __('Permission denied.'));
    }
    }

    /**
     * Display the specified project type.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (\Auth::user()->can('show project type')) {
        $projectType = ProjectType::findOrFail($id);
        return view('project_types.show', compact('projectType'));
        }else{
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for editing the specified project type.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    if (\Auth::user()->can('edit project type')) {
        $projectType = ProjectType::findOrFail($id);
        return view('project_types.edit', compact('projectType'));
        }else{
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Update the specified project type in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('edit project type')) {
        $request->validate([
            'project_type_name' => 'required|string|max:255|unique:project_types,project_type_name,' . $id . ',project_type_id',
        ]);

        $projectType = ProjectType::findOrFail($id);
        $projectType->project_type_name = $request->project_type_name;
        $projectType->save();

        return redirect()->route('project-types.index')
            ->with('success', 'Project Type updated successfully.');
    }else{
        return redirect()->back()->with('error', __('Permission denied.'));
    }
    }

    /**
     * Remove the specified project type from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Auth::user()->can('delete project type')) {
        $projectType = ProjectType::findOrFail($id);
        $projectType->delete();

        return redirect()->route('project-types.index')
            ->with('success', 'Project Type deleted successfully.');
        }else{
            return redirect()->back()->with('error', __('Permission denied.'));

        }
    }
}
