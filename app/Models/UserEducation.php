<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserEducation extends Model
{
    use HasFactory;
    protected $table = 'user_educations';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'school_name',
        'degree',
        'field',
        'country',
        'start_date',
        'end_date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
