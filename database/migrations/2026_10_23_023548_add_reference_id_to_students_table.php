<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up()
{
    Schema::table('students', function (Blueprint $table) {
        if (!Schema::hasColumn('students', 'reference_id')) {
            // Tinanggal natin ang ->after('id') para hindi maghanap ng 'id' column
            $table->string('reference_id')->nullable();
        }
    });
}

public function down()
{
    Schema::table('students', function (Blueprint $table) {
        $table->dropColumn('reference_id');
    });
}
};
