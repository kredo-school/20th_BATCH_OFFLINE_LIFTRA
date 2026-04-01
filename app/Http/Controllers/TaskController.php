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
        $today = now()->toDateString();
        
        // Base query: get tasks for user, EXCLUDING completed tasks that are in the past
        $baseQuery = Auth::user()->tasks()
            ->where(function($query) use ($today) {
                // Keep incomplete tasks OR completed tasks that are due today or in the future
                $query->where('completed', false)
                      ->orWhere(function($q) use ($today) {
                          $q->where('completed', true)
                            ->where(function($subQ) use ($today) {
                                // For repeating tasks, end_date must be >= today (or null representing forever)
                                // For non-repeating tasks, due_date must be >= today
                                $subQ->where(function($q2) use ($today) {
                                    $q2->whereNotNull('repeat_type')
                                       ->where(function($q3) use ($today) {
                                           $q3->whereNull('end_date')->orWhere('end_date', '>=', $today);
                                       });
                                })->orWhere(function($q2) use ($today) {
                                    $q2->whereNull('repeat_type')
                                       ->where('due_date', '>=', $today);
                                });
                            });
                      });
            })
            ->orderByRaw('COALESCE(due_date, start_date) ASC'); // Order logically

        $view = $request->get('view', 'matrix');

        if ($view === 'list') {
            $tasks = $baseQuery->paginate(15);
            $taskGroups = []; // Not needed for list view
        } elseif ($view === 'completed') {
            // Fetch ONLY completed tasks that are strictly in the past
            $tasks = Auth::user()->tasks()
                ->where('completed', true)
                ->where(function($query) use ($today) {
                    $query->where(function($q) use ($today) {
                        $q->whereNotNull('repeat_type')
                          ->whereNotNull('end_date')
                          ->where('end_date', '<', $today);
                    })->orWhere(function($q) use ($today) {
                        $q->whereNull('repeat_type')
                          ->where('due_date', '<', $today);
                    });
                })
                ->orderByRaw('COALESCE(due_date, end_date) DESC')
                ->paginate(15);
            $taskGroups = [];
        } else {
            // Matrix view gets all (filtered) without pagination for the grid
            $tasks = $baseQuery->get();
            $taskGroups = [
                'importantUrgent' => $tasks->where('priority_type', Task::IMPORTANT_URGENT),
                'importantNotUrgent' => $tasks->where('priority_type', Task::IMPORTANT_NOT_URGENT),
                'notImportantUrgent' => $tasks->where('priority_type', Task::NOT_IMPORTANT_URGENT),
                'notImportantNotUrgent' => $tasks->where('priority_type', Task::NOT_IMPORTANT_NOT_URGENT),
            ];
        }

        return view('tasks.index', compact('tasks', 'taskGroups', 'view'));

    }

    public function complete(Request $request, Task $task)
    {
        $task->completed = !$task->completed;
        
        // Auto-generation logic for completed repeating tasks
        if ($task->completed && $task->repeat_type) {
            $currentDate = \Carbon\Carbon::parse($task->start_date);
            $nextDate = null;
            $interval = (int) $task->repeat_interval;

            switch ((int) $task->repeat_type) {
                case 1: // Daily
                    $nextDate = clone $currentDate;
                    $nextDate->addDays($interval);
                    break;
                case 2: // Weekly
                    if ($task->days_of_week) {
                        $days = is_string($task->days_of_week) ? json_decode($task->days_of_week, true) : $task->days_of_week;
                        if (is_array($days) && count($days) > 0) {
                            $currentDayIndex = $currentDate->dayOfWeek; // 0 (Sun) - 6 (Sat)
                            $nextDayDiff = 7; 

                            // Map string days to Carbon day indexes
                            $dayMap = [
                                'sun' => 0, 'mon' => 1, 'tue' => 2, 'wed' => 3, 'thu' => 4, 'fri' => 5, 'sat' => 6
                            ];

                            foreach ($days as $targetDay) {
                                // Some formats might be 1-7 or mon-sun. Handle string formats standard from our UI
                                $targetCarbonDay = is_numeric($targetDay) ? (int)$targetDay : ($dayMap[strtolower($targetDay)] ?? 0);
                                if($targetCarbonDay == 7) $targetCarbonDay = 0;
                                
                                $diff = $targetCarbonDay - $currentDayIndex;
                                if ($diff <= 0) {
                                    $diff += 7; // Next occurrence of that day
                                }
                                
                                if ($diff < $nextDayDiff) {
                                    $nextDayDiff = $diff;
                                }
                            }
                            
                            $nextDate = clone $currentDate;
                            $nextDate->addDays($nextDayDiff);
                            
                            // If jumping to the next week block based on interval
                            if ($nextDate->weekOfYear !== $currentDate->weekOfYear && $interval > 1) {
                                $nextDate->addWeeks($interval - 1);
                            }
                        } else {
                            // Fallback if no days checked
                            $nextDate = clone $currentDate;
                            $nextDate->addWeeks($interval);
                        }
                    } else {
                        // Fallback if days_of_week is completely null
                        $nextDate = clone $currentDate;
                        $nextDate->addWeeks($interval);
                    }
                    break;
                case 3: // Monthly
                     $nextDate = clone $currentDate;
                     $nextDate->addMonths($interval);
                     if ($task->day_of_month) {
                         $nextDate->day = min((int)$task->day_of_month, $nextDate->daysInMonth);
                     }
                    break;
            }

            // Verify the next date doesn't exceed the end_date if provided
            $shouldGenerate = true;
            if ($task->end_date && $nextDate && $nextDate->gt(\Carbon\Carbon::parse($task->end_date))) {
                $shouldGenerate = false;
            }

            if ($shouldGenerate && $nextDate) {
                // Generate the next task instance
                $newTask = $task->replicate();
                
                // Keep repeating configuration for the newly generated task
                $newTask->completed = false;
                $newTask->start_date = clone $nextDate;
                 // Set the due_date properly for the UI
                $newTask->due_date = clone $nextDate;
                
                $newTask->save();
                
                // Remove the repeating configuration from the currently completed task
                // This makes it a statically completed historic task
                $task->repeat_type = null;
                $task->repeat_interval = 1;
                $task->days_of_week = null;
                $task->day_of_month = null;
                $task->end_date = clone $currentDate; // Record the date it was finished
                $task->due_date = clone $currentDate; 
            }
        }

        $task->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'completed' => $task->completed
            ]);
        }

        return back();
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'priority_type' => 'required'
        ]);

        $task = new Task();
        $task->user_id = Auth::id();
        $task->title = $request->title;
        $task->description = $request->description;
        $task->priority_type = $request->priority_type;
        $task->completed = false;

        if ($request->is_repeat) {
            $task->repeat_type = $request->repeat_type;
            $task->repeat_interval = $request->repeat_interval ?? 1;
            
            $task->days_of_week = ($request->repeat_type == 2 && $request->has('days_of_week')) ? json_encode($request->days_of_week) : null;
            $task->day_of_month = ($request->repeat_type == 3) ? $request->day_of_month : null;

            // Handle initial due_date creation for repeating tasks so it explicitly shows up on UI right away
            $startDate = \Carbon\Carbon::parse($request->start_date_repeat ?? now()->toDateString());
            $initialDueDate = clone $startDate;

            if ($task->repeat_type == 2 && $task->days_of_week) {
                // If it's a weekly repeat, find the very first valid day starting from the start_date
                $days = json_decode($task->days_of_week, true);
                if (is_array($days) && count($days) > 0) {
                    $dayMap = ['sun' => 0, 'mon' => 1, 'tue' => 2, 'wed' => 3, 'thu' => 4, 'fri' => 5, 'sat' => 6];
                    $currentDayIndex = $startDate->dayOfWeek;
                    $nextDayDiff = 7;

                    foreach ($days as $targetDay) {
                        $targetCarbonDay = is_numeric($targetDay) ? (int)$targetDay : ($dayMap[strtolower($targetDay)] ?? 0);
                        if($targetCarbonDay == 7) $targetCarbonDay = 0;
                        
                        $diff = $targetCarbonDay - $currentDayIndex;
                        if ($diff < 0) {
                            $diff += 7; // It's next week
                        }
                        
                        if ($diff < $nextDayDiff) {
                            $nextDayDiff = $diff;
                        }
                    }
                    $initialDueDate->addDays($nextDayDiff);
                }
            } elseif ($task->repeat_type == 3 && $task->day_of_month) {
                 // For monthly, match the day of month
                 $targetDay = min((int)$task->day_of_month, $startDate->daysInMonth);
                 if ($startDate->day > $targetDay) {
                     // If we've already passed that day this month, push to next month
                     $initialDueDate->addMonth();
                     $initialDueDate->day = min($targetDay, $initialDueDate->daysInMonth);
                 } else {
                     $initialDueDate->day = $targetDay;
                 }
            }

            $task->start_date = $startDate->toDateString();
            $task->due_date = $initialDueDate->toDateString(); // Start date essentially serves as first due date
            $task->end_date = $request->end_date;

            $task->task_time = !$request->has('all_day_repeat') ? $request->task_time_repeat : null;
        } else {
            $task->due_date = $request->due_date;
            $task->start_date = $request->start_date_no_repeat; // Allow nullable from UI
            $task->task_time = !$request->has('all_day_no_repeat') ? $request->task_time_no_repeat : null;
        }

        $task->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Task created successfully!',
                'task' => $task
            ]);
        }

        return back();
    }

    public function show(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }
        return view('tasks.show', compact('task'));
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
            'priority_type' => 'required'
        ]);

        $task->title = $request->title;
        $task->description = $request->description;
        $task->priority_type = $request->priority_type;

        if ($request->is_repeat) {
            $task->repeat_type = $request->repeat_type;
            $task->repeat_interval = $request->repeat_interval ?? 1;
            
            $task->days_of_week = ($request->repeat_type == 2 && $request->has('days_of_week')) ? json_encode($request->days_of_week) : null;
            $task->day_of_month = ($request->repeat_type == 3) ? $request->day_of_month : null;

            // Handle initial due_date recalculation if start_date or rules change
            $startDate = \Carbon\Carbon::parse($request->start_date_repeat ?? now()->toDateString());
            $initialDueDate = clone $startDate;

            if ($task->repeat_type == 2 && $task->days_of_week) {
                $days = json_decode($task->days_of_week, true);
                if (is_array($days) && count($days) > 0) {
                    $dayMap = ['sun' => 0, 'mon' => 1, 'tue' => 2, 'wed' => 3, 'thu' => 4, 'fri' => 5, 'sat' => 6];
                    $currentDayIndex = $startDate->dayOfWeek;
                    $nextDayDiff = 7;

                    foreach ($days as $targetDay) {
                        $targetCarbonDay = is_numeric($targetDay) ? (int)$targetDay : ($dayMap[strtolower($targetDay)] ?? 0);
                        if($targetCarbonDay == 7) $targetCarbonDay = 0;
                        
                        $diff = $targetCarbonDay - $currentDayIndex;
                        if ($diff < 0) {
                            $diff += 7; // It's next week
                        }
                        
                        if ($diff < $nextDayDiff) {
                            $nextDayDiff = $diff;
                        }
                    }
                    $initialDueDate->addDays($nextDayDiff);
                }
            } elseif ($task->repeat_type == 3 && $task->day_of_month) {
                 $targetDay = min((int)$task->day_of_month, $startDate->daysInMonth);
                 if ($startDate->day > $targetDay) {
                     $initialDueDate->addMonth();
                     $initialDueDate->day = min($targetDay, $initialDueDate->daysInMonth);
                 } else {
                     $initialDueDate->day = $targetDay;
                 }
            }

            $task->start_date = $startDate->toDateString();
            $task->due_date = $initialDueDate->toDateString();
            $task->end_date = $request->end_date;

            $task->task_time = !$request->has('all_day_repeat') ? $request->task_time_repeat : null;
        } else {
            $task->repeat_type = null;
            $task->repeat_interval = 1;
            $task->days_of_week = null;
            $task->day_of_month = null;
            $task->end_date = null;
            
            $task->due_date = $request->due_date;
            $task->start_date = $request->start_date_no_repeat; // Fix: Use the input start_date instead of due_date
            $task->task_time = !$request->has('all_day_no_repeat') ? $request->task_time_no_repeat : null;
        }

        // Reset notification flag if start_date changes
        if ($task->isDirty('start_date')) {
            $task->start_notified_at = null;
        }

        $task->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Task updated successfully!',
                'task' => $task
            ]);
        }

        return back();
    }

    public function destroy(Request $request, Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        $task->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Task deleted successfully!'
            ]);
        }

        return back();
    }
}
