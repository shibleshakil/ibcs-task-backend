<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function taskStatistics(Request $request){
        $taskCounts = Task::selectRaw("COUNT(*) as total_tasks")
        ->selectRaw("SUM(status = 'completed') as completed_tasks")
        ->selectRaw("SUM(status = 'pending') as pending_tasks")
        ->selectRaw("SUM(status = 'processing') as processing_tasks")
        ->where('user_id', $request->user()->id)
        ->first();

        return response()->json($taskCounts);

    }

    public function getUpcomingDeadlines(Request $request)
    {
        $upcomingTasks = Task::with(['project'])
            ->where('user_id', $request->user()->id)
            ->where('status', '!=', 'completed')
            ->whereDate('deadline', '>=', now())
            ->orderBy('deadline', 'asc')
            ->get();

        $datas = TaskResource::collection($upcomingTasks);

        return response()->json($datas);
    }

}
