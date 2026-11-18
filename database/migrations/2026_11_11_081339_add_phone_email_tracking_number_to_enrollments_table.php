
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // phone_number and email are handled by
        // the 2026_11_11_085746 migration.
    }

    public function down(): void
    {
        // No columns to remove.
    }
};
