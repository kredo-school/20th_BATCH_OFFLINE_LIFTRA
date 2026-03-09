<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserEducation extends Model
{
    protected $table = 'user_educations';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'school_name',
        'degree',
        'field_of_study',
        'start_date',
        'end_date',
        'currently_education',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
