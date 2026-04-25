<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'domain',
        'address',
        'phone',
        'email',
        'company_email',
        'logo',
        'nif',
        'plan',
        'monthly_fee',
        'modules',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'modules' => 'array',
            'monthly_fee' => 'decimal:2',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function financeRecords(): HasMany
    {
        return $this->hasMany(FinanceRecord::class);
    }

    public function companyPayments(): HasMany
    {
        return $this->hasMany(CompanyPayment::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function channels(): HasMany
    {
        return $this->hasMany(Channel::class);
    }

    public function folders(): HasMany
    {
        return $this->hasMany(Folder::class);
    }

    public function archivedFiles(): HasMany
    {
        return $this->hasMany(ArchivedFile::class);
    }

    public function dynamicModules(): HasMany
    {
        return $this->hasMany(DynamicModule::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }
}
