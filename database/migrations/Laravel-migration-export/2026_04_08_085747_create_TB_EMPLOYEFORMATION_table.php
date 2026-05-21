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

        Schema::create('TB_EMPLOYE_FORMATION', function (Blueprint $table) {
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('IS_DELETE')->default(false);

            $table->bigIncrements('ID'); //id
            $table->unsignedBigInteger('EMPLOYE_ID');
            $table->foreign('EMPLOYE_ID')->references('ID')->on('TB_EMPLOYE');

            $table->unsignedBigInteger('FORMATION_ID');
            $table->foreign('FORMATION_ID')->references('ID')->on('TB_FORMATION');

            $table->dateTime('DATE_REALISATION');
            $table->dateTime('DATE_VALIDITE')->comment('Modifiable');
            $table->foreign('DATE_VALIDITE')->references('VALIDITE_DATE')->on('TB_FORMATION');
            $table->longtext('FICHIERS_IMAGES')->nullable();
            $table->enum('STATUT', ['ACTIF', 'EXPIRE'])->comment('ACTIF / Expiré');
            $table->bigInteger('MODIFICATION');

        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('TB_EMPLOYE_FORMATION');
    }
};

