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
        Schema::table('TB_SAFETY_ACTION', function (Blueprint $table) {
            if (!Schema::hasColumn('TB_SAFETY_ACTION', 'ID_EVENT_ANALYSE')) {
                $table->unsignedBigInteger('ID_EVENT_ANALYSE')->nullable()->after('DATE_OUVERTURE');
                $table->foreign('ID_EVENT_ANALYSE')->references('ID')->on('TB_EVENT_ANALYSE');
            }
        });

        Schema::table('TB_TASKS_SAFETY', function (Blueprint $table) {
            if (!Schema::hasColumn('TB_TASKS_SAFETY', 'ID_SAFETY_ACTION')) {
                $table->unsignedBigInteger('ID_SAFETY_ACTION')->nullable()->after('REF_ACTION');
                $table->foreign('ID_SAFETY_ACTION')->references('ID')->on('TB_SAFETY_ACTION');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('TB_TASKS_SAFETY', function (Blueprint $table) {
            if (Schema::hasColumn('TB_TASKS_SAFETY', 'ID_SAFETY_ACTION')) {
                $table->dropForeign(['ID_SAFETY_ACTION']);
                $table->dropColumn('ID_SAFETY_ACTION');
            }
        });

        Schema::table('TB_SAFETY_ACTION', function (Blueprint $table) {
            if (Schema::hasColumn('TB_SAFETY_ACTION', 'ID_EVENT_ANALYSE')) {
                $table->dropForeign(['ID_EVENT_ANALYSE']);
                $table->dropColumn('ID_EVENT_ANALYSE');
            }
        });
    }
};
