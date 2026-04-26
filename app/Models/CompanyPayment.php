<?php

namespace App\Models;

use App\Traits\FormatsDatesSerialization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyPayment extends Model
{
    use HasFactory, FormatsDatesSerialization;

    protected $fillable = [
        'organization_id',
        'date',
        'description',
        'montant',
        'receipt',
        'account',
        'payment_method_id',
        'statut',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date:Y-m-d',
            'montant' => 'decimal:2',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
