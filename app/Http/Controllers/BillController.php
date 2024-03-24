<?php

namespace App\Http\Controllers;

use App\Constants\ErrorCode;
use App\Models\Bill;
use App\Models\BillStudent;
use App\Models\Course;
use App\Models\CourseStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillController extends Controller
{
    /**
     * @OA\post(
     *     tags={"账单管理"},
     *     path="/api/teacher/add_bill",
     *     summary="新增账单",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="course_id", type="integer", description="课程id"),
     *                 required={"course_id"}
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
    public function addBill(Request $request)
    {
        // 接受参数
        $params = $request->only("course_id");
        try {
            // 先判断数据是否存在
            $bill = Bill::where("course_id", $params['course_id'])->first();
            if (!empty($bill)) {
                return $this->resJson(ErrorCode::DATA_EXIST);
            }
            // 判断该课程是否设置学生
            $student_ids = CourseStudent::where("course_id", $params['course_id'])->distinct()->pluck("student_id");

            if ($student_ids->isEmpty()) {
                return $this->resJson(ErrorCode::STUDENT_NO_EXIST);
            }
            $data = [
                "course_id" => $params['course_id'],
            ];
            // 写入课程表
            Bill::create($data);
            return $this->resJson(ErrorCode::SUCCESS);
        } catch (\Throwable $e) {
            return $this->resJson(ErrorCode::ERROR, $e->getMessage());
        }
    }

    /**
     * @OA\post(
     *     tags={"账单管理"},
     *     path="/api/teacher/list_bill",
     *     summary="账单列表",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="page_size", type="integer", description="每页显示几条信息"),
     *                 @OA\Property(property="page", type="integer", description="当前显示第几页"),
     *                 required={"page_size","page"}
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Success", @OA\JsonContent(
     *         @OA\Property(property="code", type="integer", description="返回码"),
     *         @OA\Property(property="message", type="string", description="错误信息"),
     *         @OA\Property(property="data", type="object", description="返回数据",
     *            @OA\Property(property="meta", type="object", description="元信息",
     *                @OA\Property(property="count", type="integer", description="当前页的项目数"),
     *                @OA\Property(property="perPage", type="integer", description="每页显示的项目数"),
     *                @OA\Property(property="currentPage", type="integer", description="当前页码"),
     *                @OA\Property(property="lastPage", type="integer", description="最后一页的页码"),
     *                @OA\Property(property="total", type="integer", description="总数")
     *           ),
     *           @OA\Property(property="list", type="array", description="数据列表", @OA\Items(type="object",
     *               @OA\Property(property="id", type="integer", description="唯一标识"),
     *               @OA\Property(property="course_id", type="integer", description="课程id"),
     *               @OA\Property(property="created_at", type="string", description="创建时间"),
     *               @OA\Property(property="status", type="integer", description="账单状态"),
     *               @OA\Property(property="status_description", type="string", description="账单状态-中文描述"),
     *               @OA\Property(property="course", type="object", description="课程详情",
     *                  @OA\Property(property="id", type="integer", description="唯一标识"),
     *                  @OA\Property(property="date", type="string", description="日期"),
     *                  @OA\Property(property="course_name", type="string", description="课程名称"),
     *                  @OA\Property(property="cost", type="string", description="费用"),
     *                  )
     *             ))
     *         ),
     *         @OA\Property(property="timestamp", type="integer", description="服务器响应的时间戳")
     *     ))
     * )
     *
     */
    public function listBill(Request $request)
    {
        // 接受参数
        $page_size = $request->input("page_size");
        $current_page = $request->input("page");
        try {
            // $query = Bill::select("id", "status", "course_id")->with('course')->orderByDesc('created_at');
            $query = Bill::select("id", "status", "course_id", "created_at")->with(['course' => function ($query) {
                $query->select('id', 'course_name', 'cost', 'date');
            }])->orderByDesc('created_at');
            $data = $query->paginate($page_size, ['*'], 'course', $current_page);
            return $this->resJson(ErrorCode::SUCCESS, $data);
        } catch (\Throwable $e) {
            return $this->resJson(ErrorCode::ERROR, $e->getMessage());
        }
    }

    /**
     * @OA\get(
     *     tags={"账单管理"},
     *     path="/api/teacher/send_bill",
     *     summary="发送账单",
     *     @OA\Parameter(name="bill_id", in="query", description="账单id"),
     *     @OA\Response(response=200, description="Success", @OA\JsonContent(
     *         @OA\Property(property="code", type="integer", description="返回码"),
     *         @OA\Property(property="message", type="string", description="错误信息"),
     *         @OA\Property(property="data", type="object", description="返回数据"),
     *         @OA\Property(property="timestamp", type="integer", description="服务器响应的时间戳")
     *     ))
     * )
     *
     */
    public function sendBill(Request $request)
    {
        // 接受参数
        $bill_id = $request->input("bill_id");
        DB::beginTransaction();
        try {
            // 根据课程id查找当前选修该课程的学生
            $student = CourseStudent::where("course_id", function ($query) use ($bill_id) {
                $query->select("course_id")->where("id", $bill_id)->distinct()->from('bill');
            })->distinct()->pluck("student_id");
            if ($student->isEmpty()) {
                return $this->resJson(ErrorCode::STUDENT_NO_EXIST);
            }
            // 再找到已经发送过账单提醒的人员
            $payed_student_ids = BillStudent::where("bill_id", $bill_id)->pluck("student_id");
            $difference_student = array_diff($student->toArray(), $payed_student_ids->toArray());
            $data = [];
            foreach ($difference_student as $key => $value) {
                $data[] = [
                    "bill_id" => $bill_id,
                    "student_id" => $value,
                ];
            }
            if (empty($data)) {
                DB::rollBack();
                return $this->resJson(ErrorCode::SUCCESS);
            }
            BillStudent::insert($data);
            Bill::where('id', $bill_id)->update(['status' => Bill::SENDED]);
            DB::commit();
            return $this->resJson(ErrorCode::SUCCESS);
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->resJson(ErrorCode::ERROR, $e->getMessage());
        }
    }

    /**
     * @OA\get(
     *     tags={"我的账单"},
     *     path="/api/student/get_student_bill",
     *     summary="获取当前用户账单列表",
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
     *                 @OA\Property(property="bill_id", type="integer", description="账单唯一标识"),
     *                 @OA\Property(property="status", type="integer", description="支付状态"),
     *                 @OA\Property(property="status_description", type="string", description="支付状态中文描述"),
     *                 @OA\Property(property="bill", type="object", description="账单详情",
     *                     @OA\Property(property="id", type="integer", description="唯一标识"),
     *                     @OA\Property(property="course_id", type="integer", description="课程id"),
     *                     @OA\Property(property="created_at", type="string", description="创建时间"),
     *                     @OA\Property(property="status", type="string", description="账单是否已发送"),
     *                     @OA\Property(property="status_description", type="string", description="账单是否已发送中文描述"),
     *                     @OA\Property(property="course", type="object", description="课程详情",
     *                         @OA\Property(property="id", type="integer", description="唯一标识"),
     *                         @OA\Property(property="created_at", type="string", description="创建时间"),
     *                         @OA\Property(property="cost", type="string", description="费用"),
     *                         @OA\Property(property="course_name", type="string", description="课程名"),
     *                         @OA\Property(property="date", type="string", description="日期"),
     *                     )
     *                  )
     *             ))
     *         ),
     *         @OA\Property(property="timestamp", type="integer", description="服务器响应的时间戳")
     *     ))
     * )
     *
     */
    public function getStudentBillList(Request $request)
    {
        // 接受参数
        $page_size = $request->input("page_size");
        $current_page = $request->input("page");
        try {
            $user_id = \Auth::guard("student")->user()->id;
            // 根据课程id查找当前选修该课程的学生
            $query = BillStudent::select("id", "status", "bill_id")->with('bill.course')->where("student_id", $user_id);
            $data = $query->paginate($page_size, ['*'], 'bill_student', $current_page);
            return $this->resJson(ErrorCode::SUCCESS, $data);
        } catch (\Throwable $e) {
            return $this->resJson(ErrorCode::ERROR, $e->getMessage());
        }
    }
}
