<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Habit;
use App\Models\UserEducation;
use App\Models\UserExperience;
use App\Models\UserCertification;
use App\Models\UserSkill;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'birthday',
        'linkedin',
        'portfolio',
        'usersgoal',
        'profile_image',
        'has_completed_tour',
        'timezone',
        'language',
        'password',
        'google_access_token',
        'google_refresh_token',
        'google_token_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birthday' => 'date',
        ];
    }

    // User.php

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function goals()
    {
        return $this->hasMany(Goal::class);
    }

    public function habits()
    {
        return $this->hasMany(Habit::class);
    }

    public function journals()
    {
        return $this->hasMany(Journal::class);
    }

    public function userEducations()
    {
        return $this->hasMany(UserEducation::class);
    }

    public function userExperiences()
    {
        return $this->hasMany(UserExperience::class);
    }

    public function userCertifications()
    {
        return $this->hasMany(UserCertification::class);
    }

    public function userSkills()
    {
        return $this->hasMany(UserSkill::class);
    }

    public function calendarEvents()
    {
        return $this->hasMany(CalendarEvent::class);
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::created(function (User $user) {
            // Only generate sample habits in local environment
            if (app()->environment('local')) {
                $user->generateSampleHabits();
                $user->generateSampleProfile();
            }
        });
    }

    /**
     * Generate 5 sample habits for the user.
     */
    public function generateSampleHabits(): void
    {
        Habit::factory()->morningRun()->for($this)->create();
        Habit::factory()->readBook()->for($this)->create();
        Habit::factory()->yogaSession()->for($this)->create();
        Habit::factory()->waterPlants()->for($this)->create();
        Habit::factory()->payMonthlyBills()->for($this)->create();
    }

    /**
     * Generate sample profile data for the user.
     */
    public function generateSampleProfile(): void
    {
        UserEducation::factory()->count(2)->for($this)->create();
        UserExperience::factory()->count(2)->for($this)->create();
        UserCertification::factory()->count(2)->for($this)->create();
        UserSkill::factory()->count(5)->for($this)->create();
    }
}
