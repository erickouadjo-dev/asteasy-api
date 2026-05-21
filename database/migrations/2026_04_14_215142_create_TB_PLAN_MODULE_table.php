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

        Schema::create('TB_PLAN_MODULE', function (Blueprint $table) {
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('IS_DELETE')->default(false);
            $table->bigIncrements('ID'); //id
            
            $table->unsignedBigInteger('PLAN_ID')->nullable();
            $table->foreign('PLAN_ID')->references('ID')->on('TB_PLAN')->nullable();

            $table->unsignedBigInteger('MODULE_ID')->nullable();
            $table->foreign('MODULE_ID')->references('ID')->on('TB_MODULE')->nullable();
           
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('TB_PLAN_MODULE');
    }
};
