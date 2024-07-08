<?php
// 确保请求的来源
$origin = $_SERVER['HTTP_ORIGIN'];

// 检查请求的来源是否在允许的列表中
$allowed_origins = ['http://localhost:5173', 'https://v4.ariven.cn','https://imp.skydreamclub.cn/','https://admin.imp.skydreamclub.cn/'];
if (in_array($origin, $allowedOrigins)) {
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
    $token=hash("sha256",$token);
    if ($token == $token_Correct) {
        $time = $data['time'];
        // 创建预处理语句
        $sql = "DELETE FROM event WHERE time = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $time);
        $stmt->execute();


        if ($stmt->affected_rows > 0) {
            http_response_code(200);
            echo json_encode(array('status' => '200', 'code' => '成功'));
            exit;
        } else {
            http_response_code(200);
            echo json_encode(array('status' => '201', 'code' => '没找到数据'));
            exit;
        }

        // 关闭数据库连接
        $stmt->close();
        $conn->close();
    } else {
        http_response_code(505);
        echo json_encode(array('status' => '505', 'code' => '错误的token'));
        exit;
    }
} else {
    http_response_code(405); // 请求方法不允许
    echo json_encode(array('status' => '405', 'error' => 'Method Not Allowed'));
}
?>
