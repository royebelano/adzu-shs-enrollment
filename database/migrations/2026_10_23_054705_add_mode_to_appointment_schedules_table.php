<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::table('appointment_schedules', function (Blueprint $table) {
        $table->string('mode')->default('Online')->after('end_time'); // 'Online', 'Walk-In', 'Closed'
    });
}

public function down(): void
{
    Schema::table('appointment_schedules', function (Blueprint $table) {
        $table->dropColumn('mode');
    });
}
};
