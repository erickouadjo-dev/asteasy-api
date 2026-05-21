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

        Schema::create('TB_FACTURE', function (Blueprint $table) {
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('IS_DELETE')->default(false);
            $table->bigIncrements('ID'); //id
            $table->unsignedBigInteger('ENTREPRISE_ID')->nullable(); //id_entreprise (FK)
            $table->foreign('ENTREPRISE_ID')->references('ID')->on('TB_ENTREPRISE')->nullable();
            $table->decimal('MONTANT', 10, 2)->nullable(); //montant
            $table->date('DATE_FACTURE')->nullable(); //date_facture
            $table->string('STATUT', 50)->nullable(); //statut
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('TB_FACTURE');
    }
};
