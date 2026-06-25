<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('download_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_file_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip', 45);
            $table->string('user_agent', 1024)->nullable();
            $table->string('referer', 1024)->nullable();
            $table->string('method', 10)->default('GET');
            $table->boolean('is_bot')->default(false);
            $table->string('locale', 35)->nullable();
            $table->timestamps();

            $table->index('ip');
            $table->index('created_at');
            $table->index(['is_bot', 'created_at']);
            $table->index(['project_file_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('download_logs');
    }
};
