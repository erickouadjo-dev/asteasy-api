<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {

        Schema::table('TB_FONCTIONNALITE', function (Blueprint $table) {

            $table->string('DESCRIPTION', 255)->nullable(); //description
            $table->longText('LIEN')->nullable(); //lien
           
        });
        Schema::table('TB_MODULE', function (Blueprint $table) {

            $table->longText('LIEN')->nullable(); //lien
           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('TB_FONCTIONNALITE', function (Blueprint $table) {

            $table->dropColumn('DESCRIPTION');
            $table->dropColumn('LIEN');
           
        });
        Schema::table('TB_MODULE', function (Blueprint $table) {

            $table->dropColumn('LIEN');
        });
    }
};
