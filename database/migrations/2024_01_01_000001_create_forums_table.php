<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forums', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 120)->unique();
            $table->text('description')->nullable();
            $table->enum('type', ['global', 'niche'])->default('global');
            $table->string('icon', 10)->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index('type');
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forums');
    }
};
