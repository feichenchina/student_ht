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
        Schema::create('course', function (Blueprint $table) {
            $table->id();
            $table->string('course_name')->comment('课程名');
            $table->string('date')->comment('开课日期');
            $table->text('cost')->comment('费用');
            $table->dateTime('created_at')->nullable()->comment('创建时间');
            $table->dateTime('updated_at')->nullable()->comment('更新时间');
            $table->dateTime('deleted_at')->nullable()->index()->comment('删除时间');
        });

        Schema::create('course_student', function (Blueprint $table) {
            $table->id();
            $table->integer('course_id')->comment('课程id');
            $table->integer('student_id')->comment('学生id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course');
        Schema::dropIfExists('course_student');
    }
};
