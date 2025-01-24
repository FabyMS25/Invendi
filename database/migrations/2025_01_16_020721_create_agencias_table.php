<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
     public function up()
    {
        Schema::create('agencia', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id');
            $table->string('nombre', 255);
            $table->string('direccion', 255)->nullable();
            $table->string('telefonos', 255)->nullable();
            $table->string('geolocalizacion', 255)->nullable();
            $table->string('correo_agencia', 255)->nullable();
            $table->boolean('habilitado')->default(true);
            $table->timestamps();

            $table->foreign('empresa_id')
                ->references('id')
                ->on('empresa')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agencia');
    }
};
