<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use App\Notifications\TaskStartNotification;

class SendTaskStartNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-task-start-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications to users for tasks starting today';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now()->toDateString();

        $tasks = Task::whereDate('start_date', $today)
            ->whereNull('start_notified_at')
            ->get();

        foreach ($tasks as $task) {
            if ($task->user) {
                $task->user->notify(new TaskStartNotification($task));
                $task->start_notified_at = now();
                $task->save();
            }
        }

        $this->info("Dispatched notifications for {$tasks->count()} tasks.");
    }
}
