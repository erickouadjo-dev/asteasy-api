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

        Schema::create('TB_MATRICE_RISQUE', function (Blueprint $table) {
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('IS_DELETE')->default(false);
            $table->bigIncrements('ID'); 
            $table->string('CODE');
            $table->string('COULEUR_CODE');
    });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('TB_MATRICE_RISQUE');
    }
};
