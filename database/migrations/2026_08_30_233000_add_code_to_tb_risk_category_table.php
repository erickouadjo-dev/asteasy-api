<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('TB_RISK_CATEGORY', function (Blueprint $table) {
            if (!Schema::hasColumn('TB_RISK_CATEGORY', 'CODE')) {
                $table->string('CODE', 10)->nullable()->after('ID');
            }
        });

        // Set default codes for existing categories if any
        DB::table('TB_RISK_CATEGORY')->where('INTITULE', 'like', '%Vol%')->orWhere('INTITULE', 'like', '%Opération%')->update(['CODE' => 'OPS']);
        DB::table('TB_RISK_CATEGORY')->where('INTITULE', 'like', '%Sol%')->orWhere('INTITULE', 'like', '%Maintenance%')->update(['CODE' => 'SOL']);
        DB::table('TB_RISK_CATEGORY')->where('INTITULE', 'like', '%Environnement%')->orWhere('INTITULE', 'like', '%Piste%')->update(['CODE' => 'ENV']);
        DB::table('TB_RISK_CATEGORY')->where('INTITULE', 'like', '%Humain%')->orWhere('INTITULE', 'like', '%Organisation%')->update(['CODE' => 'FOH']);

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('TB_RISK_CATEGORY', function (Blueprint $table) {
            if (Schema::hasColumn('TB_RISK_CATEGORY', 'CODE')) {
                $table->dropColumn('CODE');
            }
        });

        Schema::enableForeignKeyConstraints();
    }
};
