<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\TaskNotificationMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\TaskResource;
use App\Models\Project;
use Illuminate\Support\Facades\Mail;

class TaskController extends Controller
{
    // List all tasks for the authenticated user
    public function index(Request $request)
    {
        $tasks = Task::with(['user', 'project'])->orderBy('id', 'DESC')->get();

        return view('tasks.index', compact('tasks'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::where('role', 'User')->orderBy('name', 'ASC')->get();
        return view('tasks.create', compact('users'));
    }

    // Create a new task
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'project_id' => 'required',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'deadline' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $user = User::find($request->user_id);

            $task = Task::create([
                'user_id' => $request->user_id,
                'project_id' => $request->project_id,
                'title' => $request->title,
                'description' => $request->description,
                'priority' => $request->priority,
                'deadline' => $request->deadline,
            ]);

            // Send email notification to the admin
            if ($user) {
                Mail::to($user->email)
                ->send(new TaskNotificationMail($task, 'New Task Created For You'));
            }
            DB::commit();

            return redirect()->route('tasks.index')->with('success', 'New Task Created Successfully');


        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getFile());
            Log::error($th->getLine());
            Log::error($th->getMessage());

            return back()->with('error', 'An Error Occured');
        }

    }


    public function edit(Task $task){
        $users = User::where('role', 'User')->orderBy('name', 'ASC')->get();
        $projects = Project::where('user_id', $task->user_id)->orderBy('title', 'ASC')->get();
        return view('tasks.edit', compact('users', 'task', 'projects'));
    }

    // Update a task
    public function update(Request $request, Task $task)
    {
        $request->validate([
            'user_id' => 'required',
            'project_id' => 'required',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'deadline' => 'required|date',
        ]);

        DB::beginTransaction();

        try {
            $user = User::find($request->user_id);

            $task->update($request->all());
            // Send email notification to admin
            if ($user) {
                Mail::to($user->email)
                    ->send(new TaskNotificationMail($task, 'A Task Information Updated'));
            }
            DB::commit();
            return redirect()->route('tasks.index')->with('success', 'Task`s Info Updated Successfully');

        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getFile());
            Log::error($th->getLine());
            Log::error($th->getMessage());

            return back()->with('error', 'An Error Occured');
        }

    }

    // start a task
    public function start(Request $request, Task $task)
    {
        if ($task->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($task->status != 'pending') {
            return response()->json(['message' => 'Task Already started or completed'], 413);
        }

        try {
            $task->update(['status' => 'processing']);

            // Send email notification to admin
            if (env('ADMIN_EMAIL')) {
                Mail::to(env('ADMIN_EMAIL'))
                    ->send(new TaskNotificationMail($task, 'A Task is started By ' . $request->user()->name ?? ''));
            }

            DB::commit();

            return $task;

        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getFile());
            Log::error($th->getLine());
            Log::error($th->getMessage());

            return response()->json(['error' => 'Internal Server Error'], 500);
        }

    }

    // complete a task
    public function complete(Request $request, Task $task)
    {
        if ($task->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }


        if ($task->status != 'processing') {
            return response()->json(['message' => 'Task Already completed or isn`t started yet'], 413);
        }

        try {

            $task->update(['status' => 'completed']);

            // Send email notification to admin
            if (env('ADMIN_EMAIL')) {
                Mail::to(env('ADMIN_EMAIL'))
                    ->send(new TaskNotificationMail($task, 'A Task is completed By ' . $request->user()->name ?? ''));
            }

            DB::commit();

            return $task;
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getFile());
            Log::error($th->getLine());
            Log::error($th->getMessage());

            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    // Delete a task
    public function destroy(Task $task)
    {
        try {
            $task->delete();
            DB::commit();
            return response()->json(['success' => 'Task Deleted Successfully'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getFile());
            Log::error($th->getLine());
            Log::error($th->getMessage());

            return response()->json(['error' => 'Internal Server Error'], 500);
        }

    }

}

