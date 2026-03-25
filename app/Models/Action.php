<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    protected $fillable = ['milestone_id', 'title', 'completed', 'due_date'];

    protected $casts = [
        'completed' => 'boolean',
        'due_date' => 'date',
    ];

    public function milestone()
    {
        return $this->belongsTo(Milestone::class);
    }
}
