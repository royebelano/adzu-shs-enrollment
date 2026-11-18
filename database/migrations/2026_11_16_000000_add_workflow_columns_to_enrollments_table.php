<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $add = function (string $column, callable $define) use ($table) {
                if (!Schema::hasColumn('enrollments', $column)) {
                    $define($table);
                }
            };

            // Student ID number assigned when the student reaches Admission.
            $add('student_id', fn ($t) => $t->string('student_id', 20)->nullable()->unique());

            // When each step was reached.
            foreach (['step3_at', 'step4_at', 'step5_at', 'step6_at'] as $col) {
                $add($col, fn ($t) => $t->timestamp($col)->nullable());
            }

            // Step 3 - Admission checklist.
            foreach (['adm_f137', 'adm_psa', 'adm_rec_mod', 'adm_rec_gc'] as $col) {
                $add($col, fn ($t) => $t->boolean($col)->default(false));
            }
            $add('admission_remarks', fn ($t) => $t->text('admission_remarks')->nullable());

            // Step 4 - ID Capturing.
            $add('barcode', fn ($t) => $t->string('barcode', 40)->nullable());
            $add('id_capture_date', fn ($t) => $t->date('id_capture_date')->nullable());
            $add('id_capture_time', fn ($t) => $t->string('id_capture_time', 5)->nullable()); // H:i start
            $add('id_capture_end', fn ($t) => $t->string('id_capture_end', 5)->nullable());  // H:i end
            $add('id_captured', fn ($t) => $t->boolean('id_captured')->default(false));

            // Step 5 - GCO Assessment.
            $add('gco_date', fn ($t) => $t->date('gco_date')->nullable());
            $add('gco_time', fn ($t) => $t->string('gco_time', 5)->nullable()); // H:i
            $add('gco_status', fn ($t) => $t->string('gco_status', 20)->default('pending'));
        });
    }

    public function down(): void
    {
        $columns = [
            'student_id', 'step3_at', 'step4_at', 'step5_at', 'step6_at',
            'adm_f137', 'adm_psa', 'adm_rec_mod', 'adm_rec_gc', 'admission_remarks',
            'barcode', 'id_capture_date', 'id_capture_time', 'id_capture_end', 'id_captured',
            'gco_date', 'gco_time', 'gco_status',
        ];

        Schema::table('enrollments', function (Blueprint $table) use ($columns) {
            foreach ($columns as $column) {
                if (Schema::hasColumn('enrollments', $column)) {
                    if ($column === 'student_id') {
                        $table->dropUnique(['student_id']);
                    }
                    $table->dropColumn($column);
                }
            }
        });
    }
};
