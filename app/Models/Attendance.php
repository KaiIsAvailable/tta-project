<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendance';
    
    protected $fillable = ['student_id', 'attendance_date', 'status', 'reason'];

    // Disable timestamps since you do not want them
    public $timestamps = false; 
    protected $primaryKey = 'attendance_id';
    // Define the relationship with Student
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }
}