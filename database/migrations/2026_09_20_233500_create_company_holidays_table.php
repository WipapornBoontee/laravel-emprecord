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
        if (!Schema::hasTable('company_holidays')) {
            Schema::create('company_holidays', function (Blueprint $table) {
                $table->id();
                $table->string('name', 255)->comment('ชื่อวันหยุด เช่น วันสงกรานต์, วันแรงงาน');
                $table->date('holiday_date')->unique()->comment('วันที่หยุด');
                $table->boolean('is_recurring')->default(false)->comment('เกิดซ้ำทุกปีหรือไม่');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_holidays');
    }
};
