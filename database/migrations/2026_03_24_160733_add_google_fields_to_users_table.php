<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('google_id')->nullable()->after('id');
            $table->string('avatar')->nullable()->after('google_id');
            $table->string('google_token')->nullable()->after('avatar');
            $table->string('google_refresh_token')->nullable()->after('google_token');
            $table->string('phone')->nullable()->after('google_refresh_token');
            $table->enum('role', ['user', 'venue_owner', 'admin'])->default('user')->after('phone');
            $table->boolean('is_active')->default(true)->after('role');
            $table->string('password')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['google_id', 'avatar', 'google_token', 'google_refresh_token', 'phone', 'role', 'is_active']);
            $table->string('password')->nullable(false)->change();
        });
    }
};
