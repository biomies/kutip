<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop unique constraint agar banyak deleted user boleh punya browser_token = null
            $table->dropUnique(['browser_token']);
            $table->string('browser_token', 64)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('browser_token', 64)->nullable(false)->change();
            $table->unique('browser_token');
        });
    }
};
