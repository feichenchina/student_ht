<?php

if (!function_exists('resJson')) {
    /**
     * http 响应 json 数据格式
     *
     * @Author sunyanan
     *
     * @param int $code 自定义 code 码
     * @param string|array $msg 错误信息，如果不为''则认为是data字段数据
     * @param mixed $data 正常的数据
     * @param int $statusCode http code
     * @param array $headers 响应头设置
     * @return Response
     */
    function resJson(int $code, $msg = '', $data = null, int $statusCode = 200, array $headers = [])
    {
        /**
         * @var \App\Constants\ErrorCode
         */
        if ($msg === '') {
            // 默认填充标准错误信息
            $msg = getConstMessage($code);
        } elseif (!is_string($msg)) {
            // 将msg改为data用，msg为充标准错误信息
            $data = $msg;
            $msg = getConstMessage($code);
        }
        if ($data instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            // 整理分页数据
            $data = [
                'meta' => [
                    'count' => $data->count(), // 当前页的项目数
                    'perPage' => $data->perPage(), // 每页显示的项目数
                    'currentPage' => $data->currentPage(), // 当前页码
                    'lastPage' => $data->lastPage(), // 最后一页的页码
                    'total' => $data->total(), // 总数
                ],
                'list' => $data->items(), // 数据
            ];
        }

        return response()->json([
            // "status" => $status, // 描述 HTTP 响应结果：HTTP 状态响应码在 500-599 之间为”fail”，在 400-499 之间为”error”，其它均为”success”
            "code" => (int) $code, // 自定义业务 code 码
            "message" => $msg,
            "data" => $data,
            "timestamp" => millisecond(),
        ], $statusCode, $headers, JSON_UNESCAPED_UNICODE);
    }
}

if (!function_exists('millisecond')) {
    /**
     * 当前时间的毫秒级别时间戳
     * @return int
     */
    function millisecond()
    {
        return ceil(microtime(true) * 1000);
    }
}

if (!function_exists('get_time')) {
    /**
     * 转化时间为毫秒时间戳
     * @param int|string $time 时间戳|日期
     * @return int
     */
    function get_time($time)
    {
        if (is_numeric($time)) {
            return (int) str_pad($time, 13, "0", STR_PAD_RIGHT);
        }
        return strtotime($time) * 1000;
    }
}

if (!function_exists('time_format')) {
    /**
     * 时间戳转化为日期
     * @param int|string $time 时间戳|日期
     * @param string $format 格式化方式
     * @return string
     */
    function time_format($time, $format = 'Y-m-d')
    {
        if (is_numeric($time)) {
            return date($format, substr($time, 0, 10));
        }
        return $time;
    }
}

if (!function_exists('prepare_tree')) {
    /**
     * 数组转树结构
     * @Author sunyanan
     *
     * @param array $data
     * @param string $key
     * @param string $child_name
     * @return array
     */
    function prepare_tree($data, $key = 'id', $child_name = 'children')
    {
        $data = collect($data)->keyBy($key)->toArray();
        $tree = [];
        foreach ($data as $v) {
            if (isset($data[$v['pid']])) {
                $data[$v['pid']][$child_name][] = &$data[$v[$key]];
            } else {
                $tree[] = &$data[$v[$key]];
            }
        }
        return $tree;
    }
}

if (!function_exists('is_effective')) {
    /**
     * 判断查询条件是否有效，主要为了区分空字符串和0字符串
     * @param [type] $param 接收数组
     * @param [type] $key 验证字段
     * @return boolean
     */
    function is_effective($param, $key)
    {
        return isset($param[$key]) && strlen($param[$key]) > 0;
    }
}
