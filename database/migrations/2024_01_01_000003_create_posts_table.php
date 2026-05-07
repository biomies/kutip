<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('forum_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subforum_id')->nullable()->constrained()->nullOnDelete();
            $table->text('content');
            $table->unsignedInteger('reply_count')->default(0);
            $table->timestamp('last_reply_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('forum_id');
            $table->index('subforum_id');
            $table->index('created_at');
            $table->index('last_reply_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
