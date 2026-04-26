<?php

namespace App\Models;

use App\Traits\FormatsDatesSerialization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory, FormatsDatesSerialization;

    protected $fillable = [
        'bank_name',
        'account_number',
        'account_holder',
        'type',
    ];
}
