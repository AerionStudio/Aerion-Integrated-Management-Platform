<?php
// 确保请求的来源
$origin = $_SERVER['HTTP_ORIGIN'];

// 检查请求的来源是否在允许的列表中
$allowed_origins = ['http://localhost:5173', 'https://v4.ariven.cn','https://imp.skydreamclub.cn/','https://admin.imp.skydreamclub.cn/'];
if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
}

header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // 添加 'Authorization'

// 确保请求是 OPTIONS 方法
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require './setting.php';

// 确保请求是 POST 方法
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postData = file_get_contents("php://input");
    $data = json_decode($postData, true);
    $token = $data['token'];
    $token = hash("sha256", $token);
    if ($token == $token_Correct) {
        $user_num=$data['user_num'];
        if (isset($user_num)) {
            $int_user_num=intval($user_num);
            $url = 'http://180.188.35.7:6067/users/' . $int_user_num;

            // 设置 HTTP 头
            $headers = [
                'Authorization: Bearer 157fd42534a1a058a8ee3de1104dfbbfee6d37b1a3930157b25b24b8da4d680a'
            ];

            // 创建 CURL 请求
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // 发送请求
            $response = curl_exec($ch);

            // 获取响应状态码
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            // 关闭请求
            curl_close($ch);

            // 根据响应状态码进行相应处理
            if ($httpCode == 503) {
                // 服务器还未准备好，稍后再试
                echo json_encode(['status' => '503', 'message' => '服务器还没准备好，请稍后再试']);
                exit;
            } elseif ($httpCode == 404) {
                // 呼号不存在
                echo json_encode(['status' => '404', 'message' => '呼号不存在']);
                exit;
            } elseif ($httpCode == 204) {
                // 删除成功
                $sql = "DELETE FROM `user` WHERE user_num='$user_num'";
                $result = mysqli_query($conn, $sql);
                if ($result) {
                    echo json_encode(['status' => '200', 'message' => "呼号：{$user_num}，删除成功"]);
                    exit;
                } else {
                    echo json_encode(['status' => '201', 'message' => "呼号：{$user_num}，删除失败"]);
                    exit;
                }
            } else {
                echo json_encode(['status' => '500', 'message' => "错误码：{$httpCode}，请发送给 3208629021"]);
                exit;
            }
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(['status' => '400', 'error' => '呼号参数缺失']);
            exit;
        }
    } else {
        http_response_code(505);
        echo json_encode(array('status' => '505', 'code' => '错误的token'));
        exit;
    }


} else {
    http_response_code(405); // 请求方法不允许
    echo json_encode(['status' => '405', 'error' => 'Method Not Allowed']);
    exit;
}
?>
