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

        Schema::table('TB_ROLE_PERMISSION', function (Blueprint $table) {

            $table->unsignedBigInteger('FONCTIONNALITE_ID')->nullable();
            $table->foreign('FONCTIONNALITE_ID')->references('id')->on('TB_FONCTIONNALITE')->nullable();

        });

        Schema::table('TB_PLAN', function (Blueprint $table) {

            $table->longText('DESCRIPTION')->nullable(); //description

        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('TB_ROLE_PERMISSION', function (Blueprint $table) {

            $table->dropForeign(['FONCTIONNALITE_ID']);
            $table->dropColumn('FONCTIONNALITE_ID');
           
        });
        Schema::table('TB_PLAN', function (Blueprint $table) {

            $table->dropColumn('DESCRIPTION');
           
        });
        
    }
};
