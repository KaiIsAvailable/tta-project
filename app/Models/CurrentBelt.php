<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrentBelt extends Model
{
    use HasFactory;

    protected $table = 'current_belts';
    protected $primaryKey = 'BeltID'; // Set primary key

    protected $fillable = [
        'BeltName',
        'BeltLevel',
        'Requirements'
    ];

    public function students()
    {
        return $this->hasMany(Student::class, 'belt_id', 'BeltID'); // Foreign key on students table
    }
}
