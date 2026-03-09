<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserExperience extends Model
{
    protected $table = 'user_experiences';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'job_title',
        'company_name',
        'employment_type',
        'start_date',
        'end_date',
        'currently_working',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
