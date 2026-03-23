<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Habit;
use App\Models\HabitLog;

class HabitController extends Controller
{

    public function index(Request $request)
    {

        $user = Auth::user();

        $date = $request->date
            ? Carbon::parse($request->date)
            : Carbon::today();

        $startOfWeek = $date->copy()->startOfWeek();

        $weekDates = [];

        for ($i=0;$i<7;$i++) {
            $weekDates[] = $startOfWeek->copy()->addDays($i);
        }

        $habits = Habit::where('user_id',$user->id)
            ->where(function($q) {
                $q->whereNull('end_date')
                  ->orWhereDate('end_date', '>=', Carbon::today());
            })
            ->get();

        $todayHabits = Habit::where('user_id', $user->id)
            ->whereDate('start_date', '<=', $date->toDateString())
            ->where(function($q) use ($date) {
                $q->whereNull('end_date')
                  ->orWhereDate('end_date', '>=', $date->toDateString());
            })
            ->get()
            ->filter(function($habit) use ($date){
                return $habit->occursOn($date);
            });

        $todayLogs = HabitLog::whereDate('date',$date)
            ->whereIn('habit_id',$todayHabits->pluck('id'))
            ->get()
            ->keyBy('habit_id');

        foreach($habits as $habit){
            $habit->streak = $habit->calculateStreak(Carbon::today());
        }

        foreach($todayHabits as $habit) {
            $habit->time_text = $habit->habit_time 
                ? \Carbon\Carbon::parse($habit->habit_time)->format('H:i')
                : 'All Day';
        }

        foreach($habits as $habit) {
            $habit->time_text = $habit->habit_time 
                ? \Carbon\Carbon::parse($habit->habit_time)->format('H:i')
                : 'All Day';
        }

        // Calculate habit counts for the calendar view
        $calendarCounts = [];
        foreach ($weekDates as $day) {
            $count = Habit::where('user_id', $user->id)
                ->whereDate('start_date', '<=', $day->toDateString())
                ->where(function($q) use ($day) {
                    $q->whereNull('end_date')
                      ->orWhereDate('end_date', '>=', $day->toDateString());
                })
                ->get()
                ->filter(fn($h) => $h->occursOn($day))
                ->count();
            $calendarCounts[$day->toDateString()] = $count;
        }

        return view('habits.index',[
            'weekDates'=>$weekDates,
            'selectedDate'=>$date,
            'todayHabits'=>$todayHabits,
            'habits'=>$habits,
            'todayLogs'=>$todayLogs,
            'calendarCounts' => $calendarCounts
        ]);

    }

    public function store(Request $request)
    {

        $request->merge([
            'habit_time' => $request->habit_time ? \Carbon\Carbon::parse($request->habit_time)->format('H:i') : null,
        ]);

        $request->validate([
            'title' => 'required|string|max:255',
            'repeat_type' => 'required|in:1,2,3',
            'repeat_interval' => 'nullable|integer|min:1',
            'days_of_week' => 'nullable|array',
            'day_of_month' => 'nullable|integer|min:1|max:31',
            'habit_time' => 'nullable|date_format:H:i',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date'
        ]);

        Habit::create([
            'user_id'=>auth()->id(),
            'title'=>$request->title,
            'repeat_type'=>$request->repeat_type,
            'repeat_interval'=>$request->repeat_interval ?? 1,
            'days_of_week'=>$request->days_of_week,
            'day_of_month'=>$request->day_of_month ?? null,
            'habit_time'=>$request->habit_time ?? null,
            'start_date'=>$request->start_date,
            'end_date'=>$request->end_date
        ]);

        return redirect()->route('habits.index');

    }

    public function update(Request $request, Habit $habit)
    {
        $request->merge([
            'habit_time' => $request->habit_time ? \Carbon\Carbon::parse($request->habit_time)->format('H:i') : null,
        ]);

        $request->validate([
            'title' => 'required|string|max:255',
            'repeat_type' => 'required|in:1,2,3',
            'repeat_interval' => 'nullable|integer|min:1',
            'days_of_week' => 'nullable|array',
            'day_of_month' => 'nullable|integer|min:1|max:31',
            'habit_time' => 'nullable|date_format:H:i',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date'
        ]);

        $repeatChanged = ($habit->repeat_type != $request->repeat_type || $habit->repeat_interval != $request->repeat_interval);

        if ($repeatChanged) {
            // 1. End the old habit yesterday
            $habit->update([
                'end_date' => Carbon::yesterday()->toDateString()
            ]);

            // 2. Create a new habit starting today
            Habit::create([
                'parent_id' => $habit->parent_id ?? $habit->id,
                'user_id' => auth()->id(),
                'title' => $request->title,
                'repeat_type' => $request->repeat_type,
                'repeat_interval' => $request->repeat_interval ?? 1,
                'days_of_week' => $request->days_of_week,
                'day_of_month' => $request->day_of_month ?? null,
                'habit_time' => $request->habit_time ?? null,
                'start_date' => Carbon::today()->toDateString(),
                'end_date' => $request->end_date
            ]);
        } else {
            // Standard update if frequency hasn't changed
            $habit->update([
                'title' => $request->title,
                'repeat_type' => $request->repeat_type,
                'repeat_interval' => $request->repeat_interval ?? 1,
                'days_of_week' => $request->days_of_week,
                'day_of_month' => $request->day_of_month ?? null,
                'habit_time' => $request->habit_time ?? null,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date
            ]);
        }

        return redirect()->route('habits.index');
    }

    public function destroy(Habit $habit)
    {
        $series = $habit->getAllInSeries();
        
        foreach ($series as $h) {
            $h->delete();
        }

        return redirect()->route('habits.index');
    }

public function getHabitsByDate(Request $request)
{
    $user = Auth::user();
    $date = Carbon::parse($request->date);

    $todayHabits = Habit::where('user_id', $user->id)
        ->whereDate('start_date', '<=', $date->toDateString())
        ->where(function($q) use ($date) {
            $q->whereNull('end_date')
              ->orWhereDate('end_date', '>=', $date->toDateString());
        })
        ->get()
        ->filter(function($habit) use ($date){
            return $habit->occursOn($date);
        });

    $todayLogs = HabitLog::whereDate('date',$date)
        ->whereIn('habit_id', $todayHabits->pluck('id'))
        ->get()
        ->keyBy('habit_id');

    foreach($todayHabits as $habit){
        // Streak calculation (still useful if streak is placed back in partial later)
        $habit->streak = $habit->calculateStreak($date);

        // Time text
        $habit->time_text = $habit->habit_time 
            ? \Carbon\Carbon::parse($habit->habit_time)->format('H:i')
            : 'All Day';
    }

    return view('habits.partials.today-habits',[
        'todayHabits' => $todayHabits,
        'todayLogs' => $todayLogs,
        'selectedDate' => $date
    ]);
}

    public function toggle(Request $request, Habit $habit)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $date = Carbon::parse($request->date)->format('Y-m-d');
        
        $log = HabitLog::where('habit_id', $habit->id)
            ->whereDate('date', $date)
            ->first();

        if ($log) {
            $log->is_completed = !$log->is_completed;
            $log->save();
        } else {
            HabitLog::create([
                'habit_id' => $habit->id,
                'date' => $date,
                'is_completed' => true
            ]);
        }

        return response()->json(['success' => true]);
    }
}