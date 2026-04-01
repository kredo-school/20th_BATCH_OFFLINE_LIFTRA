<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendTaskStartNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-task-start-notifications';

    protected $description = 'Send notifications for tasks that are scheduled to start today.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now()->toDateString();
        
        $tasks = \App\Models\Task::where('completed', false)
            ->whereNotNull('start_date')
            ->where('start_date', '<=', $today)
            ->whereNull('start_notified_at')
            ->with('user')
            ->get();

        foreach ($tasks as $task) {
            $task->user->notify(new \App\Notifications\TaskStartNotification($task));
            $task->update(['start_notified_at' => now()]);
            
            $this->info("Notified user {$task->user->email} for task: {$task->title}");
        }

        $this->info('Task start notifications processed.');
    }
}
