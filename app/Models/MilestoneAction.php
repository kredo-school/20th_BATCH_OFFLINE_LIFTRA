<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MilestoneAction extends Model
{
    // MilestoneAction.php

    public function milestone()
    {
        return $this->belongsTo(Milestone::class);
    }

    public function logs()
    {
        return $this->hasMany(MilestoneActionLog::class);
    }
}
