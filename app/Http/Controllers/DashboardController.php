<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Task;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $taskCounts = Task::selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as processing,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed
            ")
            ->first();

        $upcomingTasks = Task::with('user')
                            ->where('status', '!=', 'completed')
                            ->whereBetween('deadline', [Carbon::now(), Carbon::now()->addDays(7)])
                            ->orderBy('deadline', 'asc')
                            ->get();

        return view('dashboard', [
            'totalTasks' => $taskCounts->total ?? 0,
            'pendingTasks' => $taskCounts->pending ?? 0,
            'processingTasks' => $taskCounts->processing ?? 0,
            'completedTasks' => $taskCounts->completed ?? 0,
            'upcomingTasks' => $upcomingTasks ?? 0
        ]);
    }



}
