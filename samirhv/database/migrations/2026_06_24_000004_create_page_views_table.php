<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->string('path', 512);
            $table->string('method', 10)->default('GET');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip', 45);
            $table->string('user_agent', 1024)->nullable();
            $table->boolean('is_bot')->default(false);
            $table->string('device', 20)->nullable();   // desktop|mobile|tablet
            $table->string('browser', 30)->nullable();
            $table->string('os', 30)->nullable();
            $table->string('referer', 1024)->nullable();
            $table->string('locale', 35)->nullable();
            $table->timestamps();

            $table->index('created_at');
            $table->index(['is_bot', 'created_at']);
            $table->index('ip');
            $table->index('path');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_views');
    }
};
