<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCertification extends Model
{
    use HasFactory;
    protected $table = 'user_certifications';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'title',
        'issuer',
        'obtained_date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
