<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('label');                         // nome amigável exibido no site
            $table->string('filename');                      // nome no disco (downloads)
            $table->string('original_name');                 // nome original do upload
            $table->string('version', 30)->nullable();
            $table->unsignedBigInteger('size')->default(0);  // bytes
            $table->char('sha256', 64)->nullable();
            $table->boolean('is_available')->default(true);
            $table->unsignedBigInteger('downloads_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('project_id');
            $table->index(['project_id', 'filename']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_files');
    }
};
