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
        Schema::create('payroll_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('ss_min_salary', 10, 2)->default(0)->comment('ฐานเงินเดือนขั้นต่ำที่ถูกหักประกันสังคม');
            $table->decimal('ss_percent', 5, 2)->default(5.00)->comment('เปอร์เซ็นต์การหักประกันสังคม');
            $table->decimal('ss_max_deduction', 10, 2)->default(750)->comment('เพดานการหักสูงสุด');
            
            $table->decimal('tax_min_salary', 10, 2)->default(26000)->comment('ฐานเงินเดือนขั้นต่ำที่เสียภาษี');
            $table->decimal('tax_percent', 5, 2)->default(3.00)->comment('เปอร์เซ็นต์การหักภาษี');
            $table->timestamps();
        });

        // Insert default values
        DB::table('payroll_settings')->insert([
            'ss_min_salary' => 1, // Any salary > 0 gets deducted
            'ss_percent' => 5.00,
            'ss_max_deduction' => 750.00,
            'tax_min_salary' => 26000.00,
            'tax_percent' => 3.00,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_settings');
    }
};
