<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $tables = [
        'TB_EVENT_DECLARATION',
        'TB_EVENT_ANALYSE',
        'TB_SAFETY_ACTION',
        'TB_TASKS_SAFETY',
        'TB_TYPE_ORIGINE_ACTION',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'ENTREPRISE_ID')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->unsignedBigInteger('ENTREPRISE_ID')->nullable();
                    $table->foreign('ENTREPRISE_ID')->references('ID')->on('TB_ENTREPRISE')->onDelete('cascade');
                });
            }
        }

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'ENTREPRISE_ID')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropForeign(['ENTREPRISE_ID']);
                    $table->dropColumn('ENTREPRISE_ID');
                });
            }
        }

        Schema::enableForeignKeyConstraints();
    }
};
