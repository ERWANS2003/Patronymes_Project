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
            $table->text('patronyme_sexe_detail')->nullable()->after('patronyme_sexe');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patronymes', function (Blueprint $table) {
            $table->dropColumn('patronyme_sexe_detail');
        });
    }
};
