<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassStudent extends Model
{
    use HasFactory;

    // Specify the table name if it's not the default 'class_students'
    protected $table = 'class_student'; 

    // Specify the primary key if it's not 'id'
    protected $primaryKey = 'class_student_id'; 

    // Disable timestamps if your table doesn't have created_at and updated_at columns
    public $timestamps = false; 

    // Define the fillable attributes
    protected $fillable = [
        'student_id',
        'class_id',
        // Optionally include centre_id if needed
        // 'centre_id',
    ];

    // Define the relationship to the Student model
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    // Define the relationship to the ClassRoom model
    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }
}