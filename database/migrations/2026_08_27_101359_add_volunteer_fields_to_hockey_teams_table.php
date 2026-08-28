<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hockey_teams', function (Blueprint $table) {
            $table->string('manager_name')->nullable();
            $table->string('manager_email')->nullable();
            $table->unsignedInteger('required_volunteers')->default(2);
            $table->boolean('active')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('hockey_teams', function (Blueprint $table) {
            $table->dropColumn([
                'manager_name',
                'manager_email',
                'required_volunteers',
                'active',
            ]);
        });
    }
};