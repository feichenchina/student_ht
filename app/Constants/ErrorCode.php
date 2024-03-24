<?php

declare (strict_types = 1);

namespace App\Constants;

class ErrorCode
{
    /**
     * @Message("操作失败")
     */
    public const ERROR = 101;

    /**
     * @Message("操作成功")
     */
    public const SUCCESS = 200;

    /**
     * @Message("参数不完整")
     */
    public const PARAM_ERROR = 201;

    /**
     * @Message("账号或密码错误")
     */
    public const LOGIN_ERROR = 301;

    /**
     * @Message("邮箱已注册")
     */
    public const EMAIL_EXIST = 302;

    /**
     * @Message("未登录")
     */
    public const NO_LOGIN = 401;

    /**
     * @Message("账号尚未激活！")
     */
    public const USER_FREEZE = 402;

    /**
     * @Message("没有权限")
     */
    public const NO_AUTH = 403;

    /**
     * @Message("数据不存在")
     */
    public const DATA_NO_EXIST = 1001;

    /**
     * @Message("操作重复")
     */
    public const REPEAT_OPERATION = 1002;

    /**
     * @Message("未有学生选修")
     */
    public const STUDENT_NO_EXIST = 1003;

    /**
     * @Message("数据已存在")
     */
    public const DATA_EXIST = 1004;

    /**
     * @Message("账单已创建，不允许初始化")
     */
    public const BILL_EXIST = 1005;

    /**
     * @Message("账单已支付，请勿重复支付")
     */
    public const PAYED_EXIST = 1006;
}
