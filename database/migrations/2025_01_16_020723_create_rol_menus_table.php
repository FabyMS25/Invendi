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
        Schema::create('rol_menu', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255);
            $table->unsignedBigInteger('empresa_id');
            $table->unsignedBigInteger('menu_id');
            $table->boolean('habilitado')->default(true);
            $table->timestamps();

            $table->foreign('empresa_id')
                ->references('id')
                ->on('empresa')
                ->onDelete('cascade');

            $table->foreign('menu_id')
                ->references('id')
                ->on('menu')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rol_menu');
    }
};
