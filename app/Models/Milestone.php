<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    // Milestone.php
    use HasFactory;

    protected $fillable = ['goal_id', 'title', 'description', 'due_date', 'completed_at', 'order'];

    protected $casts = [
        'due_date' => 'date',
        'completed_at' => 'datetime'
    ];

    public function goal()
    {
        return $this->belongsTo(Goal::class);
    }

    public function actions()
    {
        return $this->hasMany(Action::class);
    }

    public function milestoneActions()
    {
        return $this->hasMany(MilestoneAction::class);
    }
}
