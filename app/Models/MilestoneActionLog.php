<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MilestoneActionLog extends Model
{
    public function milestoneAction()
    {
        return $this->belongsTo(MilestoneAction::class);
    }
}
