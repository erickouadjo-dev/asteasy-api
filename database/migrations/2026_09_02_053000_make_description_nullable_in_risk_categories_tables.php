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
        Schema::disableForeignKeyConstraints();

        Schema::table('TB_RISK_CATEGORY', function (Blueprint $table) {
            if (Schema::hasColumn('TB_RISK_CATEGORY', 'DESCRIPTION')) {
                $table->text('DESCRIPTION')->nullable()->change();
            }
        });

        Schema::table('TB_RISK_SUBCATEGORY', function (Blueprint $table) {
            if (Schema::hasColumn('TB_RISK_SUBCATEGORY', 'DESCRIPTION')) {
                $table->text('DESCRIPTION')->nullable()->change();
            }
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('TB_RISK_CATEGORY', function (Blueprint $table) {
            if (Schema::hasColumn('TB_RISK_CATEGORY', 'DESCRIPTION')) {
                $table->text('DESCRIPTION')->nullable(false)->change();
            }
        });

        Schema::table('TB_RISK_SUBCATEGORY', function (Blueprint $table) {
            if (Schema::hasColumn('TB_RISK_SUBCATEGORY', 'DESCRIPTION')) {
                $table->text('DESCRIPTION')->nullable(false)->change();
            }
        });

        Schema::enableForeignKeyConstraints();
    }
};
