<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {

            // School year details
            $this->addString($table, 'sy_from');
            $this->addString($table, 'sy_to');

            // Enrollment classification
            $this->addBoolean($table, 'graded');
            $this->addBoolean($table, 'non_graded');

            // Learner information
            $this->addBoolean($table, 'attended_elp');
            $this->addString($table, 'elp_specify');
            $this->addString($table, 'psa_no');

            $this->addString($table, 'bd_month');
            $this->addString($table, 'bd_day');
            $this->addString($table, 'bd_year');
            $this->addString($table, 'age');

            $this->addString($table, 'place_of_birth');
            $this->addString($table, 'religion');
            $this->addString($table, 'mother_tongue');

            // Indigenous Peoples
            $this->addBoolean($table, 'ip_yes');
            $this->addBoolean($table, 'ip_no');
            $this->addString($table, 'ip_specify');

            // 4Ps
            $this->addBoolean($table, 'fourps_yes');
            $this->addBoolean($table, 'fourps_no');
            $this->addString($table, 'fourps_id');

            // Current address
            $this->addString($table, 'cur_house');
            $this->addString($table, 'cur_street');
            $this->addString($table, 'cur_barangay');
            $this->addString($table, 'cur_city');
            $this->addString($table, 'cur_province');
            $this->addString($table, 'cur_country');
            $this->addString($table, 'cur_zip');

            // Permanent address
            $this->addBoolean($table, 'same_address_yes');
            $this->addBoolean($table, 'same_address_no');
            $this->addString($table, 'per_house');
            $this->addString($table, 'per_street');
            $this->addString($table, 'per_barangay');
            $this->addString($table, 'per_city');
            $this->addString($table, 'per_province');
            $this->addString($table, 'per_country');
            $this->addString($table, 'per_zip');

            // Father
            $this->addString($table, 'father_last');
            $this->addString($table, 'father_first');
            $this->addString($table, 'father_middle');
            $this->addString($table, 'father_contact');

            // Mother
            $this->addString($table, 'mother_last');
            $this->addString($table, 'mother_first');
            $this->addString($table, 'mother_middle');
            $this->addString($table, 'mother_contact');

            // Guardian
            $this->addString($table, 'guardian_last');
            $this->addString($table, 'guardian_first');
            $this->addString($table, 'guardian_middle');
            $this->addString($table, 'guardian_contact');

            // SNED
            $this->addBoolean($table, 'sned_yes');
            $this->addBoolean($table, 'sned_no');

            // Disability details
            $this->addJson($table, 'dx');
            $this->addBoolean($table, 'dx_cancer');
            $this->addBoolean($table, 'dx_noncancer');
            $this->addBoolean($table, 'dx_blind');
            $this->addBoolean($table, 'dx_lowvision');

            // Manifestations
            $this->addJson($table, 'manifest');

            // PWD
            $this->addBoolean($table, 'pwd_yes');
            $this->addBoolean($table, 'pwd_no');

            // Previous school
            $this->addString($table, 'last_grade');
            $this->addString($table, 'last_sy');
            $this->addString($table, 'last_school');
            $this->addString($table, 'school_id');

            // Semester
            $this->addBoolean($table, 'sem_1');
            $this->addBoolean($table, 'sem_2');

            // SHS details
            $this->addString($table, 'shs_track');
            $this->addString($table, 'shs_strand');

            // Learning modality
            $this->addJson($table, 'modality');

            // Parent/guardian confirmation
            $this->addString($table, 'parent_signature_name');
            $this->addString($table, 'sign_date');
        });
    }

    private function addString(Blueprint $table, string $column): void
    {
        if (!Schema::hasColumn('enrollments', $column)) {
            $table->string($column)->nullable();
        }
    }

    private function addBoolean(Blueprint $table, string $column): void
    {
        if (!Schema::hasColumn('enrollments', $column)) {
            $table->boolean($column)->nullable();
        }
    }

    private function addJson(Blueprint $table, string $column): void
    {
        if (!Schema::hasColumn('enrollments', $column)) {
            $table->json($column)->nullable();
        }
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $columns = [
                'sy_from', 'sy_to', 'graded', 'non_graded',
                'attended_elp', 'elp_specify', 'psa_no',
                'bd_month', 'bd_day', 'bd_year', 'age',
                'place_of_birth', 'religion', 'mother_tongue',
                'ip_yes', 'ip_no', 'ip_specify',
                'fourps_yes', 'fourps_no', 'fourps_id',
                'cur_house', 'cur_street', 'cur_barangay',
                'cur_city', 'cur_province', 'cur_country', 'cur_zip',
                'same_address_yes', 'same_address_no',
                'per_house', 'per_street', 'per_barangay',
                'per_city', 'per_province', 'per_country', 'per_zip',
                'father_last', 'father_first', 'father_middle', 'father_contact',
                'mother_last', 'mother_first', 'mother_middle', 'mother_contact',
                'guardian_last', 'guardian_first', 'guardian_middle', 'guardian_contact',
                'sned_yes', 'sned_no', 'dx', 'dx_cancer', 'dx_noncancer',
                'dx_blind', 'dx_lowvision', 'manifest', 'pwd_yes', 'pwd_no',
                'last_grade', 'last_sy', 'last_school', 'school_id',
                'sem_1', 'sem_2', 'shs_track', 'shs_strand',
                'modality', 'parent_signature_name', 'sign_date',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('enrollments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
