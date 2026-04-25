<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dynamic_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('icon')->default('FileText');
            $table->boolean('show_in_sidebar')->default(false);
            $table->json('fields');
            $table->timestamps();
        });

        Schema::create('dynamic_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dynamic_module_id')->constrained()->cascadeOnDelete();
            $table->json('data');
            $table->string('submitted_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dynamic_entries');
        Schema::dropIfExists('dynamic_modules');
    }
};
