<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Centre extends Model
{
    protected $table = 'student_centre';

    public function students()
    {
        return $this->hasMany(Student::class, 'centre_id', 'centre_id');
    }

    public function centre()
    {
        return $this->belongsTo(Centre::class, 'centre_id'); // Adjust the foreign key if necessary
    }
}
