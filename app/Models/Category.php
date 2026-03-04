<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    // Category.php
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function goals()
    {
        return $this->hasMany(Goal::class);
    }

    public function getProgressAttribute()
    {
        // フロント表示用（現時点では0%固定）
        // 今後、goalsテーブルから完了率計算に変更可能
        return 0;
    }
}
