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
        Schema::create('menu_usuario', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255);
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('menu_id');
            $table->unsignedBigInteger('empresa_id');
            $table->unsignedBigInteger('agencia_id');
            $table->boolean('habilitado')->default(true);
            $table->timestamps();

            $table->foreign('usuario_id')
                ->references('id')
                ->on('usuario')
                ->onDelete('cascade');
            $table->foreign('menu_id')
                ->references('id')
                ->on('menu')
                ->onDelete('cascade');
            $table->foreign('empresa_id')
                ->references('id')
                ->on('empresa')
                ->onDelete('cascade');

            $table->foreign('agencia_id')
                ->references('id')
                ->on('agencia')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_usuario');
    }
};
