<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            if (!Schema::hasColumn('enrollments', 'current_step')) {
                $table->unsignedTinyInteger('current_step')->default(1)->index();
            }

            if (!Schema::hasColumn('enrollments', 'step2_at')) {
                $table->timestamp('step2_at')->nullable();
            }

            if (!Schema::hasColumn('enrollments', 'step2_by')) {
                $table->string('step2_by')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            foreach (['current_step', 'step2_at', 'step2_by'] as $column) {
                if (Schema::hasColumn('enrollments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
