<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPassword as CustomResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'images',
        'email',
        'password',
        'role',
        'approve',
        'student_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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
        ];
    }

    public function isStudent()
    {
        return $this->role === 'student';
    }

    public function isAdmin()
    {
        return $this->role === 'admin'; // Adjust the condition based on how you define an admin
    }

    public function isInstructor()
    {
        return $this->role === 'instructor';
    }

    public function isViewer()
    {
        return $this->role === 'viewer';
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPassword($token)); // ✅ Use custom notification
    }

    public function classes()
    {
        return $this->belongsToMany(ClassRoom::class, 'class_user', 'user_id', 'class_id');
    }

    public function students()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }
}
