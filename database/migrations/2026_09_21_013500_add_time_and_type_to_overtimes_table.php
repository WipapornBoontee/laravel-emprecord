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
        Schema::table('overtimes', function (Blueprint $table) {
            if (!Schema::hasColumn('overtimes', 'start_time')) {
                $table->time('start_time')->nullable()->after('date');
            }
            if (!Schema::hasColumn('overtimes', 'end_time')) {
                $table->time('end_time')->nullable()->after('start_time');
            }
            if (!Schema::hasColumn('overtimes', 'break_minutes')) {
                $table->unsignedSmallInteger('break_minutes')->default(0)->after('end_time');
            }
            if (!Schema::hasColumn('overtimes', 'ot_type')) {
                $table->string('ot_type', 30)->default('normal')->after('break_minutes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('overtimes', function (Blueprint $table) {
            $table->dropColumn(['start_time', 'end_time', 'break_minutes', 'ot_type']);
        });
    }
};
