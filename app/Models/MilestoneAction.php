<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MilestoneAction extends Model
{
    // MilestoneAction.php
    protected $fillable = ['milestone_id', 'title', 'repeat_type', 'repeat_interval', 'days_of_week', 'start_date', 'end_date'];

    protected $casts = [
        'days_of_week' => 'array',
        'start_date' => 'date',
        'end_date' => 'date'
    ];

    public function milestone()
    {
        return $this->belongsTo(Milestone::class);
    }

    public function logs()
    {
        return $this->hasMany(MilestoneActionLog::class);
    }

    public function occursOn(\Carbon\Carbon $date)
    {
        if (!$this->start_date) return false;

        $currentDate = $date->copy()->startOfDay();
        $startDate = \Carbon\Carbon::parse($this->start_date)->startOfDay();

        if ($currentDate->lessThan($startDate)) {
            return false;
        }

        if ($this->end_date) {
            $endDate = \Carbon\Carbon::parse($this->end_date)->startOfDay();
            if ($currentDate->greaterThan($endDate)) {
                return false;
            }
        }

        // 1=daily, 2=weekly, 3=monthly
        if ($this->repeat_type == 1) {
            $interval = $this->repeat_interval > 0 ? $this->repeat_interval : 1;
            $diffInDays = $startDate->diffInDays($currentDate);
            return $diffInDays % $interval == 0;
        }

        if ($this->repeat_type == 2) {
            $days = (array)($this->days_of_week ?? []);
            return in_array(strtolower($currentDate->format('D')), array_map('strtolower', $days));
        }

        if ($this->repeat_type == 3) {
            // Match the day of the month
            return $currentDate->day == $startDate->day;
        }

        return false;
    }
}
