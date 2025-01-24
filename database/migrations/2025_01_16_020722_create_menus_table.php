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
        Schema::create('menu', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255);
            $table->string('url', 255)->nullable();
            $table->string('aplicacion', 255)->nullable();
            $table->string('modulo', 255)->nullable();
            $table->integer('orden_modulo')->nullable();
            $table->string('agrupador', 255)->nullable();
            $table->boolean('habilitado')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu');
    }
};
