<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Task extends Model
{
    use HasFactory;
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function logs()
    {
        return $this->hasMany(TaskLog::class);
    }

        // priority_type
        const IMPORTANT_URGENT = 1;
        const IMPORTANT_NOT_URGENT = 2;
        const NOT_IMPORTANT_URGENT = 3;
        const NOT_IMPORTANT_NOT_URGENT = 4;

    public function getPriorityLabelAttribute()
    {
        return match($this->priority_type) {
            self::IMPORTANT_URGENT => 'Important & Urgent',
            self::IMPORTANT_NOT_URGENT => 'Important & Not Urgent',
            self::NOT_IMPORTANT_URGENT => 'Not Important & Urgent',
            self::NOT_IMPORTANT_NOT_URGENT => 'Not Important & Not Urgent',
        };
    }

    public function getPriorityClassAttribute()
    {
        return match($this->priority_type) {
            self::IMPORTANT_URGENT => 'border-danger text-danger bg-danger bg-opacity-10',
            self::IMPORTANT_NOT_URGENT => 'border-warning text-warning bg-warning bg-opacity-10',
            self::NOT_IMPORTANT_URGENT => 'border-info text-info bg-info bg-opacity-10',
            self::NOT_IMPORTANT_NOT_URGENT => 'border-success text-success bg-success bg-opacity-10',
        };
    }

}
