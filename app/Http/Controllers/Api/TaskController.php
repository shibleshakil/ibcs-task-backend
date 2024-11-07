<?php

namespace App\Http\Controllers\Api;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Mail\TaskNotificationMail;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class TaskController extends Controller
{
    // List all tasks for the authenticated user
    public function index(Request $request)
    {
        $query = Task::query();

        $query = $query->where('user_id', $request->user()->id);

        if ($request->project_id) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->priority) {
            $query->where('priority', $request->priority);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }
        $filteredTasks = $query->with(['project'])->orderBy('id', 'DESC')->get();
        $datas = TaskResource::collection($filteredTasks);

        return response()->json($datas, 200);
    }

    // Create a new task
    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'deadline' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $task = $request->user()->tasks()->create($request->all());

            // Send email notification to the admin
            if (env('ADMIN_EMAIL')) {
                Mail::to(env('ADMIN_EMAIL'))
                ->send(new TaskNotificationMail($task, 'New Task Created By '. $request->user()->name ?? ''));
            }
            DB::commit();
            return $task;

            return $request->user()->tasks()->create($request->all());

        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getFile());
            Log::error($th->getLine());
            Log::error($th->getMessage());

            return response()->json(['error' => 'Internal Server Error'], 500);
        }

    }


    public function edit(Task $task){
        return response()->json($task, 200);
    }

    // Update a task
    public function update(Request $request, Task $task)
    {
        if ($task->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'project_id' => 'required',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'deadline' => 'required|date',
            'status' => 'in:pending,processing,completed',
        ]);

        DB::beginTransaction();

        try {
            $task->update($request->all());
            // Send email notification to admin
            if (env('ADMIN_EMAIL')) {
                Mail::to(env('ADMIN_EMAIL'))
                    ->send(new TaskNotificationMail($task, 'A Task Updated By ' . $request->user()->name ?? ''));
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
    public function delete(Request $request, Task $task)
    {
        if ($task->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

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
