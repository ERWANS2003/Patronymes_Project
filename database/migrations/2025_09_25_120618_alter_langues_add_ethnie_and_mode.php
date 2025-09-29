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
        Schema::table('langues', function (Blueprint $table) {
            if (!Schema::hasColumn('langues', 'ethnie_id')) {
                $table->foreignId('ethnie_id')->nullable()->after('nom')->constrained('ethnies')->nullOnDelete();
            }
            if (!Schema::hasColumn('langues', 'mode_transmission')) {
                $table->string('mode_transmission')->nullable()->after('ethnie_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('langues', function (Blueprint $table) {
            if (Schema::hasColumn('langues', 'mode_transmission')) {
                $table->dropColumn('mode_transmission');
            }
            if (Schema::hasColumn('langues', 'ethnie_id')) {
                $table->dropConstrainedForeignId('ethnie_id');
            }
        });
    }
};
