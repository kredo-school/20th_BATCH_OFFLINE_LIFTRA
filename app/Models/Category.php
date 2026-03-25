<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    // Category.php
    use HasFactory;

    protected $fillable = ['name', 'color_id', 'icon_id', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function goals()
    {
        return $this->hasMany(Goal::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function icon()
    {
        return $this->belongsTo(Icon::class);
    }

    public function getProgressAttribute()
    {
        $goals = $this->goals;
        $totalGoals = $goals->count();
        if ($totalGoals === 0) return 0;
        
        $completedGoals = $goals->filter(fn($goal) => $goal->progress >= 100)->count();
        return round(($completedGoals / $totalGoals) * 100);
    }
}
