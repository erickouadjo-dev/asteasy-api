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
        if (Schema::hasTable('TB_EVENT_DECLARATION')) {
            Schema::table('TB_EVENT_DECLARATION', function (Blueprint $table) {
                if (!Schema::hasColumn('TB_EVENT_DECLARATION', 'PAYS')) {
                    $table->string('PAYS', 255)->nullable()->after('EVENT_LOCALISATION');
                }
                if (!Schema::hasColumn('TB_EVENT_DECLARATION', 'ADRESSE')) {
                    $table->string('ADRESSE', 500)->nullable()->after('PAYS');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('TB_EVENT_DECLARATION')) {
            Schema::table('TB_EVENT_DECLARATION', function (Blueprint $table) {
                if (Schema::hasColumn('TB_EVENT_DECLARATION', 'ADRESSE')) {
                    $table->dropColumn('ADRESSE');
                }
                if (Schema::hasColumn('TB_EVENT_DECLARATION', 'PAYS')) {
                    $table->dropColumn('PAYS');
                }
            });
        }
    }
};
