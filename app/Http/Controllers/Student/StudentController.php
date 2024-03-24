<?php

namespace App\Http\Controllers\Student;

use App\Constants\ErrorCode;
use App\Http\Controllers\Controller;
use App\Models\BillStudent;
use App\Models\CourseStudent;
use App\Models\Student;
use App\Services\OmisePaymentService;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * @OA\get(
     *     tags={"课程管理"},
     *     path="/api/teacher/student_list",
     *     summary="学生列表",
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
     *                 @OA\Property(property="name", type="string", description="姓名"),
     *                 @OA\Property(property="email", type="string", description="邮箱"),
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
            $query = Student::select("id", "email", "name");
            $student = $query->paginate($page_size, ['*'], 'student', $current_page);
            return $this->resJson(ErrorCode::SUCCESS, $student);
        } catch (\Throwable $e) {
            return $this->resJson(ErrorCode::ERROR, $e->getMessage());
        }
    }

    /**
     * @OA\get(
     *     tags={"课程管理"},
     *     path="/api/teacher/get_not_course_student_list",
     *     summary="获取未选修指定科目的学生列表",
     *     @OA\Parameter(name="course_id", in="query", description="课程id"),
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
     *                 @OA\Property(property="name", type="string", description="姓名"),
     *                 @OA\Property(property="email", type="string", description="邮箱"),
     *             ))
     *         ),
     *         @OA\Property(property="timestamp", type="integer", description="服务器响应的时间戳")
     *     ))
     * )
     *
     */
    public function getNotCoursestudentList(Request $request)
    {
        $page_size = $request->input("page_size");
        $current_page = $request->input("page");
        $course_id = $request->input("course_id");
        try {
            $query = Student::select("id", "email", "name")->whereNotIn('id', function ($query) use ($course_id) {
                $query->select('student_id')->where("course_id", $course_id)
                    ->from('course_student');
            });
            $student = $query->paginate($page_size, ['*'], 'student', $current_page);
            return $this->resJson(ErrorCode::SUCCESS, $student);
        } catch (\Throwable $e) {
            return $this->resJson(ErrorCode::ERROR, $e->getMessage());
        }
    }

    /**
     * @OA\get(
     *     tags={"课程管理"},
     *     path="/api/teacher/get_course_student_list",
     *     summary="获取选修指定科目的学生列表",
     *     @OA\Parameter(name="course_id", in="query", description="课程id"),
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
     *                 @OA\Property(property="name", type="string", description="姓名"),
     *                 @OA\Property(property="email", type="string", description="邮箱"),
     *             ))
     *         ),
     *         @OA\Property(property="timestamp", type="integer", description="服务器响应的时间戳")
     *     ))
     * )
     *
     */
    public function getCoursestudentList(Request $request)
    {
        $page_size = $request->input("page_size");
        $current_page = $request->input("page");
        $course_id = $request->input("course_id");
        try {
            $query = Student::select("id", "name")->whereIn("id", function ($query) use ($course_id) {
                $query->select("student_id")->where("course_id", $course_id)->from('course_student');
            }
            );
            $result = $query->paginate($page_size, ['*'], 'student', $current_page);
            return $this->resJson(ErrorCode::SUCCESS, $result);
        } catch (\Throwable $e) {
            return $this->resJson(ErrorCode::ERROR, $e->getMessage());
        }
    }

    /**
     * @OA\get(
     *     tags={"我的课程"},
     *     path="/api/student/course_list",
     *     summary="获取当前用户所选修的课程",
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
     *                 @OA\Property(property="course_id", type="integer", description="课程id"),
     *                 @OA\Property(property="course", type="object", description="课程详情",
     *                     @OA\Property(property="id", type="integer", description="唯一标识"),
     *                     @OA\Property(property="date", type="string", description="日期"),
     *                     @OA\Property(property="course_name", type="string", description="课程名称"),
     *                     @OA\Property(property="cost", type="string", description="费用"),
     *                  )
     *             ))
     *         ),
     *         @OA\Property(property="timestamp", type="integer", description="服务器响应的时间戳")
     *     ))
     * )
     *
     */
    public function getCourseList(Request $request)
    {
        $page_size = $request->input("page_size");
        $current_page = $request->input("page");
        try {
            $user_id = \Auth::guard("student")->user()->id;
            $query = CourseStudent::select("course_id")->where("student_id", $user_id)->distinct()->with(['course' => function ($query) {
                $query->select('id', 'course_name', 'cost', 'date');
            }]);
            $result = $query->paginate($page_size, ['*'], 'course_student', $current_page);
            return $this->resJson(ErrorCode::SUCCESS, $result);
        } catch (\Throwable $e) {
            return $this->resJson(ErrorCode::ERROR, $e->getMessage());
        }
    }

    // 测试支付
    public function processPayment(Request $request)
    {
        $paymentService = new OmisePaymentService();
        try {
            $amount = $request->input('amount'); // 金额
            $currency = $request->input('currency'); // 货币代码
            $cardToken = $request->input('omiseToken');
            $bill_student_id = $request->input("id");
            // 判断是否已完成支付
            $bill_student = BillStudent::where("id", $bill_student_id)->where("status", BillStudent::PAYED)->get();
            if (!$bill_student->isEmpty()) {
                return $this->resJson(ErrorCode::PAYED_EXIST);
            }
            $charge = $paymentService->processPayment($amount, $currency, $cardToken);
            // 处理支付成功逻辑
            if ($charge["status"] == "successful") {
                BillStudent::where("id", $bill_student_id)->update(["status" => BillStudent::PAYED]);
                return $this->resJson(ErrorCode::SUCCESS);
            } else {
                return $this->resJson(ErrorCode::ERROR, $charge["failure_message"]);
            }

        } catch (\Throwable $e) {
            // 处理支付失败逻辑
            return $this->resJson(ErrorCode::ERROR, $e->getMessage());
        }
    }

}
