<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('students', 'suffix')) {
            Schema::table('students', function (Blueprint $table) {
                $table->string('suffix')->nullable()->after('middle_name');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('students', 'suffix')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropColumn('suffix');
            });
        }
    }
};
