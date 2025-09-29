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
        Schema::table('patronymes', function (Blueprint $table) {
            if (!Schema::hasColumn('patronymes', 'province_id')) {
                $table->foreignId('province_id')->nullable()->after('region_id')->constrained()->nullOnDelete();
            }
            if (!Schema::hasColumn('patronymes', 'commune_id')) {
                $table->foreignId('commune_id')->nullable()->after('province_id')->constrained()->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patronymes', function (Blueprint $table) {
            if (Schema::hasColumn('patronymes', 'commune_id')) {
                $table->dropConstrainedForeignId('commune_id');
            }
            if (Schema::hasColumn('patronymes', 'province_id')) {
                $table->dropConstrainedForeignId('province_id');
            }
        });
    }
};
