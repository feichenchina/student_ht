<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\SecurityScheme(
 *  type="http",
 *  securityScheme="Authorization-Bearer",
 *  name="Authorization",
 *  in="header",
 *  scheme="Bearer",
 *  bearerFormat="JWT"
 * ),
 * @OA\Info(
 *      version="1.0.0",
 *      title="教务系统",
 *      description="教务系统接口文档"
 * ),
 * @OA\Server(
 *  url=APP_URL
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    public function resJson(int $code, $msg = '', $data = null, int $statusCode = 200, array $headers = [])
    {
        return resJson($code, $msg, $data, $statusCode, $headers);
    }
}
