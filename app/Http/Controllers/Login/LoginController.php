<?php

namespace App\Http\Controllers\Login;

use App\Constants\ErrorCode;
use App\Http\Controllers\Controller;
use App\Http\Validates\LoginValidate;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Sorry510\Annotations\Validator;

class LoginController extends Controller
{
    /**
     * @OA\post(
     *     tags={"用户认证"},
     *     path="/api/login",
     *     summary="用户登录",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="username", type="string", description="用户名"),
     *                 @OA\Property(property="password", type="string", description="密码"),
     *                 @OA\Property(property="type", type="string", description="类型[teacher:教师,student:学生]"),
     *                 required={"username", "password", "type"}
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Success", @OA\JsonContent(
     *         @OA\Property(property="code", type="integer", description="返回码"),
     *         @OA\Property(property="message", type="string", description="错误信息"),
     *         @OA\Property(property="data", type="object", description="返回数据",
     *             @OA\Property(property="token", type="string", description="用户token"),
     *             @OA\Property(property="type", type="string", description="类型")
     *         ),
     *         @OA\Property(property="timestamp", type="integer", description="服务器响应的时间戳")
     *     ))
     * )
     *
     * @Validator(class=LoginValidate::class, scene="user")
     */
    public function loginIn(Request $request)
    {
        // 接受参数
        $params = $request->only("username", "password", "type");
        try {
            $errorCode = ErrorCode::SUCCESS;
            $data = [];
            if ($params['type'] === 'teacher') {
                [$errorCode, $data] = $this->loginTeacher($params);
            } elseif ($params['type'] === 'student') {
                [$errorCode, $data] = $this->loginStudent($params);
            } else {
                $errorCode = ErrorCode::ERROR;
            }
            return $this->resJson($errorCode, $data);
        } catch (\Throwable $e) {
            return $this->resJson(ErrorCode::ERROR, $e->getMessage());
        }
    }

    protected function loginTeacher($data)
    {
        // 获取用户信息
        $user = Teacher::where('email', $data['username'])->first();
        if (!$user) {
            return [ErrorCode::LOGIN_ERROR, ""];
        }
        $password = $user->password;
        unset($user->password);
        // 记录登录信息
        $user->last_login_time = date("Y-m-d H:i:s");
        $user->last_login_ip = request()->ip();
        if (!\Hash::check($data['password'], $password)) {
            // 失败次数加一
            $user->login_failure += 1;
            $user->save();
            return [ErrorCode::LOGIN_ERROR, ""];
        }
        // 失败次数清零
        $user->login_failure = 0;
        $user->save();
        $token = $user->createToken('teacher', ['teacher'])->accessToken;
        return [ErrorCode::SUCCESS, ['token' => $token, 'type' => 'teacher', 'info' => $user]];
    }

    protected function loginStudent($data)
    {
        // 获取用户信息
        $user = Student::where('email', $data['username'])->first();
        if (!$user) {
            return [ErrorCode::LOGIN_ERROR, ""];
        }
        $password = $user->password;
        unset($user->password);

        // 记录登录信息
        $user->last_login_time = date("Y-m-d H:i:s");
        $user->last_login_ip = request()->ip();

        // 验证密码
        if (!\Hash::check($data['password'], $password)) {
            // if ($data['password'] != $password) {
            // 失败次数加一
            $user->login_failure += 1;
            $user->save();
            return [ErrorCode::LOGIN_ERROR, ""];
        }

        // 失败次数清零
        $user->login_failure = 0;
        $user->save();
        $token = $user->createToken('student', ['student'])->accessToken;
        return [ErrorCode::SUCCESS, ['token' => $token, 'type' => 'student', 'info' => $user]];
    }

    /**
     * @OA\post(
     *     tags={"用户认证"},
     *     path="/api/{type}/login-out",
     *     summary="退出登录",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="type", type="string", description="类型[teacher:教师,student:学生]"),
     *                 required={"type"}
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Success", @OA\JsonContent(
     *         @OA\Property(property="code", type="integer", description="返回码"),
     *         @OA\Property(property="message", type="string", description="错误信息"),
     *         @OA\Property(property="data", type="object", description="返回数据"),
     *         @OA\Property(property="timestamp", type="integer", description="服务器响应的时间戳")
     *     ))
     * )
     *
     * @Validator(class=LoginValidate::class, scene="user")
     */
    public function loginOut(Request $request)
    {
        $type = $request->input("type");
        if (\Auth::guard($type)->check()) {
            \Auth::guard($type)->user()->token()->delete();
        }
        return $this->resJson(ErrorCode::SUCCESS);
    }
}
