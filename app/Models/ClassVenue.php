<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassVenue extends Model
{
    protected $table = 'class_venue'; // Ensure this matches your table name
    protected $primaryKey = 'cv_id'; // Set your primary key
    public $timestamps = false; // Disable timestamps if not used

    // Define the inverse relationship
    public function classes()
    {
        return $this->hasMany(ClassRoom::class, 'cv_id', 'cv_id');
    }
}