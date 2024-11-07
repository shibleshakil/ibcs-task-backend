<?php
namespace App\Console\Commands;

use App\Mail\TaskNotificationMail;
use App\Models\Task;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendTaskDeadlineReminder extends Command
{
    protected $signature = 'send:task-deadline-reminder';
    protected $description = 'Send email reminders for upcoming task deadlines';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Find tasks with deadlines in the next 3days
        $tasks = Task::where('deadline', '>=', Carbon::now())
                     ->where('deadline', '<=', Carbon::now()->addDay(3))
                     ->where('status', '!=', 'completed')
                     ->with(['user'])
                     ->get();

        foreach ($tasks as $task) {
            Mail::to($task->user->email)->send(new TaskNotificationMail($task, 'Task Deadline Reminder'));
        }

        $this->info('Task deadline reminders sent successfully!');
    }
}
