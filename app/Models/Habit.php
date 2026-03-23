<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Habit extends Model
{
    use HasFactory;

protected $fillable = [
'parent_id',
'user_id',
'title',
'repeat_type',
'repeat_interval',
'days_of_week',
'day_of_month',
'habit_time',
'start_date',
'end_date'
];

protected $casts = [
'days_of_week' => 'array'
];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(Habit::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Habit::class, 'parent_id');
    }

    /**
     * Get all habits in this chain (ancestors and descendants).
     */
    public function getAllInSeries()
    {
        $root = $this;
        while ($root->parent_id) {
            $root = $root->parent;
        }

        $series = collect([$root]);
        $toProcess = collect([$root]);

        while ($toProcess->isNotEmpty()) {
            $current = $toProcess->shift();
            foreach ($current->children as $child) {
                $series->push($child);
                $toProcess->push($child);
            }
        }

        return $series;
    }

    public function logs()
    {
        return $this->hasMany(HabitLog::class);
    }

    public function occursOn(Carbon $date)
    {
        if (!$this->start_date) return false;

        $currentDate = $date->copy()->startOfDay();
        $startDate = Carbon::parse($this->start_date)->startOfDay();

        if ($currentDate->lessThan($startDate)) {
            return false;
        }

        if ($this->end_date) {
            $endDate = Carbon::parse($this->end_date)->startOfDay();
            if ($currentDate->greaterThan($endDate)) {
                return false;
            }
        }

        if ($this->repeat_type == 1) {
            $interval = $this->repeat_interval > 0 ? $this->repeat_interval : 1;
            $diffInDays = $startDate->diffInDays($currentDate);
            return $diffInDays % $interval == 0;
        }

        if ($this->repeat_type == 2) {
            $days = (array)($this->days_of_week ?? []);
            return in_array(strtolower($currentDate->format('D')), $days);
        }

        if ($this->repeat_type == 3) {
            return $currentDate->day == $this->day_of_month;
        }

        return false;
    }

    public function calculateStreak(Carbon $targetDate)
    {
        $completedDates = $this->logs()
            ->where('is_completed', '=', true)
            ->whereDate('date', '<=', $targetDate)
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
            ->toArray();

        $count = 0;
        $checkDate = $targetDate->copy()->startOfDay();
        $today = Carbon::today();

        if (!$this->start_date) return 0;
        $startDate = Carbon::parse($this->start_date)->startOfDay();

        while ($checkDate->greaterThanOrEqualTo($startDate)) {
            if ($this->occursOn($checkDate)) {
                $dateString = $checkDate->format('Y-m-d');
                $isCompleted = in_array($dateString, $completedDates);

                if ($isCompleted) {
                    $count++;
                } else {
                    if ($checkDate->greaterThanOrEqualTo($today)) {
                        // Tolerate uncompleted habit for today or future dates
                    } else {
                        // Missed a past scheduled day, streak broken
                        break;
                    }
                }
            }
            $checkDate->subDay();
        }

        return $count;
    }
}
