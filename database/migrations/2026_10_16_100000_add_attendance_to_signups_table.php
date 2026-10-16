<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('signups', function (Blueprint $table) {
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('absence_confirmed_at')->nullable();
            $table->foreignId('absence_confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('checked_in_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('signups', function (Blueprint $table) {
            $table->dropConstrainedForeignId('checked_in_by');
            $table->dropConstrainedForeignId('absence_confirmed_by');
            $table->dropColumn(['checked_in_at', 'absence_confirmed_at']);
        });
    }
};
