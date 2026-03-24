<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Habit;
use App\Models\Task;
use App\Models\MilestoneAction;
use App\Models\CalendarEvent;

class CalendarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $view = $request->get('view', 'week');
        $selectedDate = $request->get('date') ? Carbon::parse($request->get('date')) : Carbon::today();
        
        // For Week view
        $weekStart = $selectedDate->copy()->startOfWeek(Carbon::SUNDAY);
        $weekDates = [];
        for ($i = 0; $i < 7; $i++) {
            $weekDates[] = $weekStart->copy()->addDays($i);
        }

        // For Month view
        $monthStart = $selectedDate->copy()->startOfMonth();
        $monthEnd = $selectedDate->copy()->endOfMonth();
        $calendarStart = $monthStart->copy()->startOfWeek(Carbon::SUNDAY);
        $calendarEnd = $monthEnd->copy()->endOfWeek(Carbon::SATURDAY);
        
        $monthDates = [];
        $currentDate = $calendarStart->copy();
        while ($currentDate <= $calendarEnd) {
            $monthDates[] = $currentDate->copy();
            $currentDate->addDay();
        }

        // Fetch activity counts for indicators
        $rangeStart = ($view === 'month') ? $calendarStart : $weekStart;
        $rangeEnd = ($view === 'month') ? $calendarEnd : $weekStart->copy()->addDays(6);
        $activityCounts = $this->getActivityCounts($rangeStart, $rangeEnd);

        // Fetch detailed data for the selected date (CRITICAL FIX: Fetch before compact)
        $tasks = $this->getTasksForDate($selectedDate);
        $habits = $this->getHabitsForDate($selectedDate);
        $actions = $this->getActionsForDate($selectedDate);
        $googleEvents = $this->getGoogleEventsForDate($selectedDate);

        if ($request->ajax()) {
            if ($request->header('X-Requested-Part') === 'dashboard') {
                return view('calendar.partials.daily-dashboard', compact(
                    'selectedDate',
                    'tasks',
                    'habits',
                    'actions',
                    'googleEvents'
                ))->render();
            }

            return view('calendar.partials.app-container', compact(
                'view', 
                'selectedDate', 
                'weekDates',
                'monthDates',
                'monthStart',
                'tasks', 
                'habits', 
                'actions',
                'googleEvents',
                'activityCounts'
            ))->render();
        }

        return view('calendar.index', compact(
            'view', 
            'selectedDate', 
            'weekDates',
            'monthDates',
            'monthStart',
            'tasks', 
            'habits', 
            'actions',
            'googleEvents',
            'activityCounts'
        ));
    }

    private function getActivityCounts(Carbon $start, Carbon $end)
    {
        $counts = [];
        $current = $start->copy();
        
        // This is a bit unoptimized but works for the current scale.
        // For production with many users/items, we'd use aggregate queries.
        while ($current <= $end) {
            $dateStr = $current->format('Y-m-d');
            
            $taskCount = Auth::user()->tasks()
                ->where(function ($query) use ($current) {
                    // Non-repeating tasks: match exact due_date
                    $query->where(function ($q) use ($current) {
                        // Assuming `is_repeat` is a boolean/tinyint. If it doesn't exist, we fallback to checking if repeat_type is null
                        $q->whereNull('repeat_type')->whereDate('due_date', $current);
                    })
                    // Repeating tasks: match within start and end date bounds
                    ->orWhere(function ($q) use ($current) {
                        $q->whereNotNull('repeat_type')
                          ->whereDate('start_date', '<=', $current)
                          ->where(function ($q2) use ($current) {
                              $q2->whereNull('end_date')
                                 ->orWhereDate('end_date', '>=', $current);
                          });
                    });
                })->count();

            $habitCount = Auth::user()->habits()
                ->where('start_date', '<=', $current)
                ->get()
                ->filter(function ($habit) use ($current) {
                    return $habit->occursOn($current);
                })->count();

            $actionCount = MilestoneAction::whereHas('milestone.goal.category', function ($query) {
                    $query->where('user_id', Auth::id());
                })
                ->where('start_date', '<=', $current)
                ->where(function ($query) use ($current) {
                    $query->whereNull('end_date')
                          ->orWhere('end_date', '>=', $current);
                })->count();

            $googleEventCount = Auth::user()->calendarEvents()
                ->whereDate('start_date', '<=', $current)
                ->where(function ($query) use ($current) {
                    $query->whereNull('end_date')
                          ->orWhereDate('end_date', '>=', $current);
                })->count();

            $counts[$dateStr] = [
                'tasks' => $taskCount,
                'habits' => $habitCount,
                'actions' => $actionCount,
                'google' => $googleEventCount,
                'total' => $taskCount + $habitCount + $actionCount + $googleEventCount
            ];
            
            $current->addDay();
        }
        
        return $counts;
    }

    private function getTasksForDate(Carbon $date)
    {
        return Auth::user()->tasks()
            ->where(function ($query) use ($date) {
                // Non-repeating tasks: match exact due_date
                $query->where(function ($q) use ($date) {
                    $q->whereNull('repeat_type')->whereDate('due_date', $date);
                })
                // Repeating tasks: match within start and end date bounds
                ->orWhere(function ($q) use ($date) {
                    $q->whereNotNull('repeat_type')
                      ->whereDate('start_date', '<=', $date)
                      ->where(function ($q2) use ($date) {
                          $q2->whereNull('end_date')
                             ->orWhereDate('end_date', '>=', $date);
                      });
                });
            })
            ->get();
    }

    private function getHabitsForDate(Carbon $date)
    {
        $habits = Auth::user()->habits()
            ->where('start_date', '<=', $date)
            ->get()
            ->filter(function ($habit) use ($date) {
                return $habit->occursOn($date);
            });
            
        foreach($habits as $habit) {
            $habit->time_text = $habit->habit_time 
                ? \Carbon\Carbon::parse($habit->habit_time)->format('H:i')
                : 'All Day';
        }
        
        return $habits;
    }

    private function getActionsForDate(Carbon $date)
    {
        return MilestoneAction::whereHas('milestone.goal.category', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->where('start_date', '<=', $date)
            ->where(function ($query) use ($date) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', $date);
            })
            ->get();
    }

    private function getGoogleEventsForDate(Carbon $date)
    {
        return Auth::user()->calendarEvents()
            ->whereDate('start_date', '<=', $date)
            ->where(function ($query) use ($date) {
                $query->whereNull('end_date')
                      ->orWhereDate('end_date', '>=', $date);
            })
            ->get();
    }

    public function getEventsJson(Request $request)
    {
        $date = Carbon::parse($request->get('date', today()));
        
        $googleEvents = $this->getGoogleEventsForDate($date);
        $tasks = $this->getTasksForDate($date);
        $habits = $this->getHabitsForDate($date);

        // Map all to a common format
        $allEvents = [];
        
        foreach($googleEvents as $e) {
            $allEvents[] = ['title' => $e->title, 'type' => 'google'];
        }
        foreach($tasks as $t) {
            $allEvents[] = ['title' => $t->title, 'type' => 'task'];
        }
        foreach($habits as $h) {
            $allEvents[] = ['title' => $h->title, 'type' => 'habit'];
        }

        return response()->json(['events' => $allEvents]);
    }
}
