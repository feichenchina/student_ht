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
        Schema::create('bill_student', function (Blueprint $table) {
            $table->id();
            $table->integer('bill_id')->comment('账单id');
            $table->integer('student_id')->comment('学生id');
            $table->unique(['bill_id', 'student_id']);
            $table->integer('status')->default(1)->comment('状态[1:未支付,2:已支付]');
            $table->dateTime('created_at')->nullable()->comment('创建时间');
            $table->dateTime('updated_at')->nullable()->comment('更新时间');
            $table->dateTime('deleted_at')->nullable()->index()->comment('删除时间');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_student');
    }
};
