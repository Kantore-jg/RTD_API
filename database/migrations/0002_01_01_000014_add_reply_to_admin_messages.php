<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admin_messages', function (Blueprint $table) {
            $table->text('reply_text')->nullable()->after('read');
            $table->timestamp('replied_at')->nullable()->after('reply_text');
        });
    }

    public function down(): void
    {
        Schema::table('admin_messages', function (Blueprint $table) {
            $table->dropColumn(['reply_text', 'replied_at']);
        });
    }
};
