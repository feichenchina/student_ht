<?php

// 设置您的 Omise API 密钥
$apiKey = 'skey_test_5z1rz7sgwl33bbh17hs';

// 要支付的金额和币种
$amount = 12345; // 例如 10.00 泰铢
$currency = 'THB';

// 获取前端传递过来的 Omise Token
$token = "tokn_test_5z6hhczeq8zsstyvbud"; // 假设前端将 Token 通过 POST 请求发送到后端

// 设置支付参数
$data = array(
    'amount' => $amount,
    'currency' => $currency,
    'card' => $token,
);

// 发起支付请求
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.omise.co/charges');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/x-www-form-urlencoded',
    'Authorization: Basic ' . base64_encode($apiKey . ':'),
));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

if ($httpCode == 200) {
    // 支付成功
    echo "Payment successful.";
} else {
    // 支付失败，输出错误信息
    echo 'Payment failed. Response: ' . $response;
}
