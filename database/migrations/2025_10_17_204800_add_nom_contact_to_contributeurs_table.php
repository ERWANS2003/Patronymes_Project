<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contributeurs', function (Blueprint $table) {
            if (!Schema::hasColumn('contributeurs', 'nom')) {
                $table->string('nom')->nullable()->after('utilisateur_id');
            }
            if (!Schema::hasColumn('contributeurs', 'contact')) {
                $table->string('contact')->nullable()->after('nom');
            }
        });
    }

    public function down(): void
    {
        Schema::table('contributeurs', function (Blueprint $table) {
            if (Schema::hasColumn('contributeurs', 'contact')) {
                $table->dropColumn('contact');
            }
            if (Schema::hasColumn('contributeurs', 'nom')) {
                $table->dropColumn('nom');
            }
        });
    }
};
