<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::with(['user'])->orderBy('id', 'DESC')->get();

        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::where('role', 'User')->orderBy('name', 'ASC')->get();
        return view('projects.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'title' => ['required', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        Project::create([
            'user_id' => $request->user_id,
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return redirect()->route('projects.index')->with('success', 'New Project Created Successfully');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        $users = User::where('role', 'User')->orderBy('name', 'ASC')->get();

        return view('projects.edit', compact('project', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {

        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'title' => ['required', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $project->update([
            'user_id' => $request->user_id,
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return redirect()->route('projects.index')->with('success', 'Project`s Info Updated Successfully');


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $project->delete();
        $project->tasks()->delete();

        return response()->json(['success' => 'Project deleted successfully.']);
    }

    public function getUserProjects(User $user)
    {
        // Fetch the projects for the selected user
        $projects = $user->projects()->select('id', 'title')->get();

        return response()->json($projects);
    }

}
