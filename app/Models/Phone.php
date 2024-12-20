<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{
    use HasFactory;

    protected $table = 'phone';
    protected $primaryKey = 'phone_id';
    protected $fillable = [
        'student_id',
        'phone_number',
        'phone_person',
    ];
    public $timestamps = false; 

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
