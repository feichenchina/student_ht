<?php

namespace App\Http\Controllers\Course;

use App\Constants\ErrorCode;
use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Course;
use App\Models\CourseStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    /**
     * @OA\get(
     *     tags={"课程管理"},
     *     path="/api/teacher/course_list",
     *     summary="课程列表",
     *     @OA\Parameter(name="page", in="query", description="页数"),
     *     @OA\Parameter(name="page_size", in="query", description="每页数量"),
     *     @OA\Response(response=200, description="Success", @OA\JsonContent(
     *         @OA\Property(property="code", type="integer", description="返回码"),
     *         @OA\Property(property="message", type="string", description="错误信息"),
     *         @OA\Property(property="data", type="object", description="返回数据",
     *             @OA\Property(property="meta", type="object", description="元信息",
     *                 @OA\Property(property="count", type="integer", description="当前页的项目数"),
     *                 @OA\Property(property="perPage", type="integer", description="每页显示的项目数"),
     *                 @OA\Property(property="currentPage", type="integer", description="当前页码"),
     *                 @OA\Property(property="lastPage", type="integer", description="最后一页的页码"),
     *                 @OA\Property(property="total", type="integer", description="总数")
     *             ),
     *             @OA\Property(property="list", type="array", description="数据列表", @OA\Items(type="object",
     *                 @OA\Property(property="id", type="integer", description="唯一标识"),
     *                 @OA\Property(property="date", type="string", description="日期"),
     *                 @OA\Property(property="course_name", type="string", description="课程名"),
     *                 @OA\Property(property="cost", type="string", description="费用"),
     *                 @OA\Property(property="bill_id", type="string", description="账单id"),
     *             ))
     *         ),
     *         @OA\Property(property="timestamp", type="integer", description="服务器响应的时间戳")
     *     ))
     * )
     *
     */
    public function getList(Request $request)
    {
        $page_size = $request->input("page_size");
        $current_page = $request->input("page");
        try {
            // 对查询出来的数据进行判断，若账单已发送，则只能添加学生，不能再次对学生做初始化操作
            // $query = Course::select("id", "course_name", "date", "cost");
            $query = Course::select("id", "course_name", "date", "cost");
            $courses = $query->paginate($page_size, ['*'], 'course', $current_page);
            return $this->resJson(ErrorCode::SUCCESS, $courses);
        } catch (\Throwable $e) {
            return $this->resJson(ErrorCode::ERROR, $e->getMessage());
        }
    }

    // 获取还未创建账单的课程列表
    public function getNotBillList(Request $request)
    {
        $page_size = $request->input("page_size");
        $current_page = $request->input("page");
        try {
            $query = Course::select("id", "course_name", "date", "cost")->whereNotIn("id", function ($query) {
                $query->select("course_id")->distinct()->from('bill');
            });
            $courses = $query->paginate($page_size, ['*'], 'course', $current_page);
            return $this->resJson(ErrorCode::SUCCESS, $courses);
        } catch (\Throwable $e) {
            return $this->resJson(ErrorCode::ERROR, $e->getMessage());
        }
    }

    /**
     * @OA\post(
     *     tags={"课程管理"},
     *     path="/api/teacher/course_add",
     *     summary="新增课程",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="course_name", type="string", description="课程名"),
     *                 @OA\Property(property="cost", type="string", description="费用"),
     *                 @OA\Property(property="date", type="string", description="日期"),
     *                 required={"course_name","cost","date"}
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Success", @OA\JsonContent(
     *         @OA\Property(property="code", type="integer", description="返回码"),
     *         @OA\Property(property="message", type="string", description="错误信息"),
     *         @OA\Property(property="data", type="object", description="返回数据",
     *         ),
     *         @OA\Property(property="timestamp", type="integer", description="服务器响应的时间戳")
     *     ))
     * )
     *
     */
    public function addCourse(Request $request)
    {

        $params = $request->only("course_name", "cost", "date");
        try {
            $params["date"] = substr((string) $params["date"], 0, 7);
            // 判断课程-日期是否冲突
            if (Course::where('course_name', $params['course_name'])->where('date', $params['date'])->first()) {
                return $this->resJson(ErrorCode::REPEAT_OPERATION, '课程重复');
            }
            $data = [
                "course_name" => $params['course_name'],
                "date" => $params['date'],
                "cost" => $params['cost'],
            ];
            // 写入课程表
            Course::create($data);
            return $this->resJson(ErrorCode::SUCCESS);
        } catch (\Throwable $e) {
            return $this->resJson(ErrorCode::ERROR, $e->getMessage());
        }
    }

    // 初始化选修课程的学生
    public function initCourseStudets(Request $request)
    {
        $course_id = $request->input("course_id");
        $student_ids = $request->input("student_ids");
        $student_id_array = explode(",", $student_ids);
        $data = [];
        // 先判断该课程是否已经创建账单，若已创建则不允许初始化
        $bill = Bill::where("course_id", $course_id)->first();
        if (!empty($bill)) {
            return $this->resJson(ErrorCode::BILL_EXIST);
        }
        DB::beginTransaction();
        try {

            foreach ($student_id_array as $key => $value) {
                $data[] = [
                    "course_id" => $course_id,
                    "student_id" => $value,
                ];
            }
            CourseStudent::where('course_id', $course_id)->delete();
            // 写入课程学生表
            CourseStudent::insert($data);
            DB::commit();
            return $this->resJson(ErrorCode::SUCCESS);
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->resJson(ErrorCode::ERROR, $e->getMessage());
        }
    }

    // 新增选修课程的学生
    public function addCourseStudets(Request $request)
    {
        $course_id = $request->input("course_id");
        $student_ids = $request->input("student_ids");
        $student_id_array = explode(",", $student_ids);
        $data = [];
        DB::beginTransaction();
        try {
            foreach ($student_id_array as $key => $value) {
                $data[] = [
                    "course_id" => $course_id,
                    "student_id" => $value,
                ];
            }
            // 写入课程学生表
            CourseStudent::insert($data);
            DB::commit();
            return $this->resJson(ErrorCode::SUCCESS);
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->resJson(ErrorCode::ERROR, $e->getMessage());
        }
    }
}
