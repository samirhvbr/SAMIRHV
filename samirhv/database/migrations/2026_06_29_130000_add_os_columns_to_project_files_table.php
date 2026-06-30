<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Agrupamento de downloads por SO: adiciona os/arch/file_type (+ released_at)
 * à tabela project_files. Guards com hasColumn — idempotente. O backfill
 * (downloads:backfill-os) classifica os arquivos já existentes.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_files', function (Blueprint $table) {
            if (! Schema::hasColumn('project_files', 'os')) {
                $table->string('os', 16)->nullable()->index()->after('version');
            }
            if (! Schema::hasColumn('project_files', 'arch')) {
                $table->string('arch', 16)->nullable()->after('os');
            }
            if (! Schema::hasColumn('project_files', 'file_type')) {
                $table->string('file_type', 16)->nullable()->after('arch');
            }
            if (! Schema::hasColumn('project_files', 'released_at')) {
                $table->timestamp('released_at')->nullable()->after('file_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('project_files', function (Blueprint $table) {
            foreach (['os', 'arch', 'file_type', 'released_at'] as $col) {
                if (Schema::hasColumn('project_files', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
