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


        public function index(Request $request)
    {
        $tasks = Auth::user()->tasks;
        $tasks = Task::orderBy('due_date', 'asc')->get();
        
        $view = $request->get('view', 'matrix');

        $importantUrgent = $tasks->where('priority_type', Task::IMPORTANT_URGENT);  //$tasks->where('priority_type', 1);
        $importantNotUrgent = $tasks->where('priority_type', Task::IMPORTANT_NOT_URGENT); //$tasks->where('priority_type', 2);
        $notImportantUrgent = $tasks->where('priority_type', Task::NOT_IMPORTANT_URGENT); //$tasks->where('priority_type', 3);
        $notImportantNotUrgent = $tasks->where('priority_type', Task::NOT_IMPORTANT_NOT_URGENT); //$tasks->where('priority_type', 4);

        $taskGroups = [
            'importantUrgent' => $importantUrgent,
            'importantNotUrgent' => $importantNotUrgent,
            'notImportantUrgent' => $notImportantUrgent,
            'notImportantNotUrgent' => $notImportantNotUrgent,
        ];

        return view('tasks.index', compact('tasks','taskGroups','view'));

    }

    public function complete(Task $task)
    {
        $task->completed = !$task->completed;
        $task->save();

        return back();
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'due_date' => 'required',
            'priority_type' => 'required'
        ]);

        $this->task->user_id = Auth::id();
        $this->task->title = $request->title;
        $this->task->due_date = $request->due_date;
        $this->task->priority_type = $request->priority_type;
        $this->task->save();

        return back();
    }

    public function edit(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }
        return view('tasks.edit', compact('task'));
    }

    public function update(Request $request, Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required',
            'due_date' => 'required',
            'priority_type' => 'required'
        ]);

        $task->update([
            'title' => $request->title,
            'priority_type' => $request->priority_type,
            'due_date'  => $request->due_date
        ]);

        return redirect()->route('tasks.index');
    }

    public function destroy(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        $task->delete();
        return back();
    }
}
