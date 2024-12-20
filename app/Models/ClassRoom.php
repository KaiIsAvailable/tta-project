<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    use HasFactory;

    protected $table = 'classes'; // Specify the table name
    protected $primaryKey = 'class_id'; // Specify the correct primary key
    public $incrementing = true; // Assuming 'class_id' is an auto-increment integer
    protected $keyType = 'int'; // Assuming 'class_id' is an integer
    public $timestamps = false;

    protected $fillable = ['class_day', 'class_start_time', 'class_end_time', 'class_price', 'cv_id'];

    public function students()
    {
        return $this->belongsToMany(Student::class, 'class_student', 'class_id', 'student_id');
    }

    public function venue()
    {
        return $this->belongsTo(ClassVenue::class, 'cv_id', 'cv_id');
    }
}