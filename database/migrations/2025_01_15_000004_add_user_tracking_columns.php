<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_login_at')->nullable();
            $table->integer('login_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('preferences')->nullable();
            $table->string('timezone')->default('Africa/Ouagadougou');
            $table->string('language')->default('fr');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'last_login_at',
                'login_count',
                'is_active',
                'preferences',
                'timezone',
                'language'
            ]);
        });
    }
};
