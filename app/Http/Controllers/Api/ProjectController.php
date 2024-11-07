<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
  // List all tasks for the authenticated user
    public function index(Request $request)
    {
        $datas = ProjectResource::collection($request->user()->projects()->orderBy('id', 'DESC')->get());
        return response()->json($datas, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            return $request->user()->projects()->create($request->all());
            //code...
        } catch (\Throwable $th) {
            Log::error($th->getFile());
            Log::error($th->getLine());
            Log::error($th->getMessage());

            return response()->json(['error' => 'Internal Server Error'], 500);
        }

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
        return response()->json($project, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {

        if ($project->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project->update($request->all());
        return $project;
    }

    /**
     * Remove the specified resource from storage.
     */
    // Delete a task
    public function delete(Request $request, Project $project)
    {
        if ($project->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $project->delete();

        $project->tasks()->delete();

        return response()->json(['success' => 'Project Deleted Successfully'], 200);
    }
}
