<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;

class TaskController extends Controller
{
    private $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

        public function index()
    {
        $tasks = $this->task->where('user_id', Auth::id())->get();

        $importantUrgent = $tasks->where('priority_type', Task::IMPORTANT_URGENT);  //$tasks->where('priority_type', 1);
        $importantNotUrgent = $tasks->where('priority_type', Task::IMPORTANT_NOT_URGENT); //$tasks->where('priority_type', 2);
        $notImportantUrgent = $tasks->where('priority_type', Task::NOT_IMPORTANT_URGENT); //$tasks->where('priority_type', 3);
        $notImportantNotUrgent = $tasks->where('priority_type', Task::NOT_IMPORTANT_NOT_URGENT); //$tasks->where('priority_type', 4);

        return view('tasks.index', compact(
            'importantUrgent',
            'importantNotUrgent',
            'notImportantUrgent',
            'notImportantNotUrgent'
        ));

    }

    public function complete(Task $task)
    {
        $task->is_completed = !$task->is_completed;
        $task->save();

        return back();
    }
}
