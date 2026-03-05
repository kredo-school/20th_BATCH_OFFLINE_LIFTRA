<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
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
}
