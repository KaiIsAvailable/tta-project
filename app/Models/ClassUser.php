<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassUser extends Model
{
    use HasFactory;

    protected $table = 'class_user'; // Define the table name

    protected $primaryKey = 'class_user_id'; // Set primary key

    public $timestamps = false; // Disable timestamps if not needed

    protected $fillable = [
        'class_id',
        'user_id',
    ];

    // Relationship with ClassRoom model
    public function class()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id', 'class_id');
    }

    // Relationship with User model
    public function instructor()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
