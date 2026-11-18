<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hours that are walk-in only by default, e.g. "15:00,16:00".
        if (Schema::hasTable('capacity_settings') && !Schema::hasColumn('capacity_settings', 'walk_in_hours')) {
            Schema::table('capacity_settings', function (Blueprint $table) {
                $table->string('walk_in_hours', 120)->default('');
            });
        }

        // "online" or "walkin" for one hour of one date.
        if (Schema::hasTable('capacity_overrides') && !Schema::hasColumn('capacity_overrides', 'mode')) {
            Schema::table('capacity_overrides', function (Blueprint $table) {
                $table->string('mode', 10)->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('capacity_settings', 'walk_in_hours')) {
            Schema::table('capacity_settings', fn (Blueprint $t) => $t->dropColumn('walk_in_hours'));
        }

        if (Schema::hasColumn('capacity_overrides', 'mode')) {
            Schema::table('capacity_overrides', fn (Blueprint $t) => $t->dropColumn('mode'));
        }
    }
};
