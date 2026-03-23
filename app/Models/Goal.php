<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    // Goal.php
    use HasFactory;

    protected $fillable = ['category_id', 'title', 'description', 'target_age', 'target_date', 'progress', 'user_id'];

    protected $casts = [
        'target_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function milestones()
    {
        return $this->hasMany(Milestone::class);
    }
}
