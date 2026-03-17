<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    protected $fillable = ['milestone_id', 'title', 'completed'];

    protected $casts = [
        'completed' => 'boolean'
    ];

    public function milestone()
    {
        return $this->belongsTo(Milestone::class);
    }
}
