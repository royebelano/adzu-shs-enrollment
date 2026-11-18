<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('deped_forms', function (Blueprint $table) {
            // Idinaragdag natin ang form_fields column at ginagawang nullable para hindi mag-error
            if (!Schema::hasColumn('deped_forms', 'form_fields')) {
                $table->json('form_fields')->nullable()->after('file_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('deped_forms', function (Blueprint $table) {
            $table->dropColumn('form_fields');
        });
    }
};
