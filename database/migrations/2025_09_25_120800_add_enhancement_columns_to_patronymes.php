<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patronymes', function (Blueprint $table) {
            $table->integer('views_count')->default(0)->after('frequence');
            $table->boolean('is_featured')->default(false)->after('views_count');
            $table->foreignId('groupe_ethnique_id')->nullable()->constrained()->onDelete('set null')->after('commune_id');
            $table->foreignId('ethnie_id')->nullable()->constrained()->onDelete('set null')->after('groupe_ethnique_id');
            $table->foreignId('langue_id')->nullable()->constrained()->onDelete('set null')->after('ethnie_id');
            $table->foreignId('mode_transmission_id')->nullable()->constrained()->onDelete('set null')->after('langue_id');
        });
    }

    public function down(): void
    {
        Schema::table('patronymes', function (Blueprint $table) {
            $table->dropColumn(['views_count', 'is_featured', 'groupe_ethnique_id', 'ethnie_id', 'langue_id', 'mode_transmission_id']);
        });
    }
};
