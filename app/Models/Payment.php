<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    // Define the table name explicitly if not the plural of the model name
    protected $table = 'payment';

    // The primary key column
    protected $primaryKey = 'payment_id';

    // Disable auto-increment since you're using a BIGINT for the primary key
    public $incrementing = false;

    // If you are using timestamps, you can disable them if not needed
    public $timestamps = false;

    // Specify the fields that are mass assignable
    protected $fillable = [
        'student_id',
        'payment_date',
        'payment_amount',
        'payment_method',
        'paid_for',
        'payment_status',
    ];

    // Optionally, define the cast types for your columns
    protected $casts = [
        'payment_date' => 'date',
        'payment_amount' => 'double',
        'paid_for' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');  // Payment belongs to one Student
    }
}
