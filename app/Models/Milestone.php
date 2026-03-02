<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    // Milestone.php
    use HasFactory;

    public function goal()
    {
        return $this->belongsTo(Goal::class);
    }

    public function milestoneActions()
    {
        return $this->hasMany(MilestoneAction::class);
    }
}
