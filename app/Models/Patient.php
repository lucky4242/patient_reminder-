<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = ['name', 'phone_number', 'email', 'gender', 'drug_name', 'dosage', 'reminder_time'];
}
