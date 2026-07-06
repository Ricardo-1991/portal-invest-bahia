<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_contents', function (Blueprint $table) {
            $table->id();
            // home | fazenda | ativo | servico | informacoes | contatos
            $table->string('key')->unique();
            // Campos traduzíveis (JSON via spatie/laravel-translatable).
            $table->json('title')->nullable();
            $table->json('body')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_contents');
    }
};
