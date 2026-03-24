<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalendarEvent extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'start_date',
        'end_date',
        'source',
        'google_event_id',
        'is_synced',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
