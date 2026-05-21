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

        Schema::create('TB_ABONNEMENT', function (Blueprint $table) {
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('IS_DELETE')->default(false);
            $table->bigIncrements('ID'); //id

            $table->unsignedBigInteger('ENTREPRISE_ID')->nullable();
            $table->foreign('ENTREPRISE_ID')->references('ID')->on('TB_ENTREPRISE')->nullable();

            $table->unsignedBigInteger('PLAN_ID')->nullable();
            $table->foreign('PLAN_ID')->references('ID')->on('TB_PLAN')->nullable();

            $table->date('DATE_DEBUT')->nullable();
            $table->date('DATE_FIN')->nullable();

            $table->string('STATUT', 50)->nullable(); // actif, inactif, suspendu

        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('TB_ABONNEMENT');
    }
};
