<?php

use App\Http\Controllers\BillController;
use App\Http\Controllers\Course\CourseController;
use App\Http\Controllers\Login\LoginController;
use App\Http\Controllers\Student\StudentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
 */
Route::post('/login', [LoginController::class, 'loginIn']); // 登录

/**
 * 教师接口
 */
Route::middleware(['auth:teacher', 'scope:teacher'])->prefix('teacher')->group(function () {
    Route::post('login-out', [LoginController::class, 'loginOut']); // 登出
    Route::get('course_list', [CourseController::class, 'getList']); // 获取课程列表
    Route::get('not_bill_course_list', [CourseController::class, 'getNotBillList']); // 获取没有创建账单的课程列表
    Route::post('course_add', [CourseController::class, 'addCourse']); // 新增课程
    Route::post('init_course_student', [CourseController::class, 'initCourseStudets']); // 初始化课程与学生关系
    Route::post('add_course_student', [CourseController::class, 'addCourseStudets']); // 新增课程与学生关系
    Route::get('student_list', [StudentController::class, 'getList']); // 获取所有学生列表
    Route::get('get_course_student_list', [StudentController::class, 'getCoursestudentList']); // 获取已选修指定课程的学生列表
    Route::get('get_not_course_student_list', [StudentController::class, 'getNotCoursestudentList']); // 获取还未选修指定课程的学生列表

    Route::post('add_bill', [BillController::class, 'addBill']); // 新增账单
    Route::get('list_bill', [BillController::class, 'listBill']); // 账单列表
    Route::get('send_bill', [BillController::class, 'sendBill']); // 发送账单

    Route::get('get_student_bill_status_list', [BillController::class, 'getStudentBillStatusList']); // 获取还未选修指定课程的学生列表
});

/**
 * 学生接口
 */
Route::middleware(['auth:student', 'scope:student'])->prefix('student')->group(function () {
    Route::post('login-out', [LoginController::class, 'loginOut']); // 登出

    Route::get('course_list', [StudentController::class, 'getCourseList']); // 获取该用户选修课程的列表
    Route::post('pay_ment', [StudentController::class, 'processPayment']); // 测试对接omise支付

    Route::get('get_student_bill', [BillController::class, 'getStudentBillList']); // 获取学生账单列表
});
