<?php
header("Content-Type: application/json");

// 检查请求的来源
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

// 允许特定域名跨域访问
$allowed_origins = ['http://localhost:5173', 'https://v4.ariven.cn', 'https://imp.skydreamclub.cn', 'http://imp.skydreamclub.cn'];

if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Access-Control-Allow-Credentials: true");

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        // Handle preflight request
        header('HTTP/1.1 200 OK');
        exit();
    }
} else {
    // If origin is not allowed, return a 403 Forbidden response
    http_response_code(403);
    echo json_encode(array('status' => '403', 'code' => '不允许的来源'));
    exit();
}

// 如果是OPTIONS请求（预检请求），直接返回200 OK
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include 'setting.php';
$num = $_GET['callsign'];
$time = $_GET['time'];
if (isset($num)) {

    $servername = "127.0.0.1";
    $username = "imp";
    $password = "ab321818";
    $dbname = "imp";
    $conn = new mysqli($servername, $username, $password, $dbname);

    $sql = $conn->prepare("SELECT * FROM user WHERE user_num = ?");
    $sql->bind_param("s", $num);
    $sql->execute();
    $result = $sql->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $integral = $row['integral'];
        $Hourlywages=get($time);
        $userData = array(
            "status" => "200",
            "integral" => $integral,
            "Hourlywages"=>$Hourlywages,
            "time"=>$time,
        );
        // 返回响应数据
        echo json_encode($userData);
    }else{
        $userData = array(
            "status" => "404",
        );
        // 返回响应数据
        echo json_encode($userData);
        exit; // 结束脚本执行
    }

}

function get($time)
{
    $time = intval($time);

    if ($time >= 360000 && $time < 720000) {
        return '60积分/小时';
    } elseif ($time >= 360000 && $time < 720000) {
        return '80积分/小时';
    } elseif ($time >= 720000 && $time < 1080000) {
        return '100积分/小时';
    } elseif ($time >= 1080000 && $time < 1440000) {
        return '120积分/小时';
    } elseif ($time >= 1440000 && $time < 1800000) {
        return '140积分/小时';
    } elseif ($time >= 1800000) {
        return '160积分/小时';
    } else {
        return '40积分/小时';
    }

}