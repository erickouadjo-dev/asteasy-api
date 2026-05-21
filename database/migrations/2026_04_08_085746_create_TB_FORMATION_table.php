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

        Schema::create('TB_FORMATION', function (Blueprint $table) {
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('IS_DELETE')->default(false);

            $table->bigIncrements('ID'); //id
            $table->string('INTITULE', 200);
            $table->string('DESCRIPTION', 500)->nullable();
            $table->enum('VALIDITE_TYPE', ['6M', '12M', '24M', '36M'])->comment('Standard :6m, 12m, 24m, 36 mois')->nullable();
            $table->enum('VALIDITE_DATE', ['DATE_A_DATE', 'FIN_DE_MOIS'])->comment('Date à date // Fin de mois')->nullable();
            $table->enum('VALIDITE_MODIFIABLE', ['OUI', 'NON'])->comment('OUI // NON')->nullable();
            $table->longText('FICHIERS_IMAGES')->nullable();
            $table->string('MODIFICATION')->nullable();
         
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('TB_FORMATION');
    }
};

