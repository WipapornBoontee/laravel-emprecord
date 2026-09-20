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
        if (!Schema::hasTable('attendance_adjustments')) {
            Schema::create('attendance_adjustments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('attendance_id')->nullable()->constrained('attendances')->onDelete('set null');
                $table->date('target_date')->comment('วันที่ที่ต้องการขอปรับปรุงเวลา');
                $table->time('requested_check_in')->nullable()->comment('เวลาเข้างานที่ขอปรับ');
                $table->time('requested_check_out')->nullable()->comment('เวลาเลิกงานที่ขอปรับ');
                $table->text('reason')->comment('เหตุผลความจำเป็น');
                $table->string('attachment_url', 255)->nullable()->comment('หลักฐานแนบ');
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->foreignId('approver_id')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamp('approved_at')->nullable();
                $table->text('reject_reason')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_adjustments');
    }
};
