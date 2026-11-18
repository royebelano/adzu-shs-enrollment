<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            if (!Schema::hasColumn('enrollments', 'sy_from')) {
                $table->string('sy_from', 4)->nullable();
            }

            if (!Schema::hasColumn('enrollments', 'sy_to')) {
                $table->string('sy_to', 4)->nullable();
            }

            if (!Schema::hasColumn('enrollments', 'sex_male')) {
                $table->boolean('sex_male')->default(false);
            }

            if (!Schema::hasColumn('enrollments', 'sex_female')) {
                $table->boolean('sex_female')->default(false);
            }
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $columns = [];

            foreach (['sy_from', 'sy_to', 'sex_male', 'sex_female'] as $column) {
                if (Schema::hasColumn('enrollments', $column)) {
                    $columns[] = $column;
                }
            }

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
