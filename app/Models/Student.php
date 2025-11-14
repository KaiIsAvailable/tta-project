<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    // Define the primary key for the model
    protected $primaryKey = 'student_id';

    // If your primary key is not auto-incrementing
    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = [
        'name', 'ic_number', 'belt_id', 'profile_picture', 'centre_id', 'fee', 'student_startDate',
    ];

    protected $casts = [
        'student_startDate' => 'datetime', // or 'date' if you don't need time
    ];

    // Define the relationship with CurrentBelt
    public function belt()
    {
        return $this->belongsTo(CurrentBelt::class, 'belt_id', 'BeltID');
    }

    public function centre()
    {
        return $this->belongsTo(Centre::class, 'centre_id', 'centre_id');
    }

    // In Student.php
    public function classes()
    {
        return $this->belongsToMany(ClassRoom::class, 'class_student', 'student_id', 'class_id');
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'student_id', 'student_id');
    }

    public function phone()
    {
        return $this->hasMany(Phone::class, 'student_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'student_id', 'student_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'student_id', 'student_id');
    }
}