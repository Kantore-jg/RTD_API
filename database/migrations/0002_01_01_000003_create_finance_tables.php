<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('description');
            $table->enum('type', ['Revenu', 'Dépense']);
            $table->decimal('montant', 15, 2);
            $table->enum('statut', ['Validé', 'Encaissé', 'En attente'])->default('En attente');
            $table->timestamps();

            $table->index(['organization_id', 'type']);
            $table->index(['organization_id', 'date']);
        });

        Schema::create('company_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('description')->nullable();
            $table->decimal('montant', 15, 2);
            $table->string('receipt')->nullable();
            $table->string('account')->nullable();
            $table->enum('statut', ['Validé', 'En attente', 'Rejeté'])->default('En attente');
            $table->timestamps();
        });

        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('bank_name');
            $table->string('account_number');
            $table->string('account_holder');
            $table->enum('type', ['BIF', 'USDT'])->default('BIF');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('company_payments');
        Schema::dropIfExists('finance_records');
    }
};
