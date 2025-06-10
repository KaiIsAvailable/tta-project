<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    use HasFactory;

    protected $table = 'payment_setting';
    
    protected $fillable = ['pSetting_id', 'pSign', 'pChop'];

    // Disable timestamps since you do not want them
    public $timestamps = false; 
    protected $primaryKey = 'pSetting_id';
}