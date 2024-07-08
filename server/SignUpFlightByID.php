<?php
header("Content-Type: application/json");

// 检查请求的来源
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

// 允许特定域名跨域访问

$allowed_origins = ['http://localhost:5173', 'https://v4.ariven.cn','https://imp.skydreamclub.cn/','https://admin.imp.skydreamclub.cn/'];

if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowed_origins)) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Access-Control-Allow-Credentials: true");

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        // Handle preflight request
        header('HTTP/1.1 200 OK');
        exit();
    }
}

header("Access-Control-Allow-Headers: Content-Type");

// 如果是OPTIONS请求（预检请求），直接返回200 OK
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}


include "../setting.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postData = file_get_contents("php://input");
    $data = json_decode($postData, true);
    $token = $data['token'];
    // $token = $_REQUEST['token'];
    $token = hash('sha256', $token);
    if ($token == $token_Correct) {
        /** 获取数据 **/
        $id = $data['id'];
        $userid = $data['userid'];
        // $id =$_REQUEST['id'];
        // $userid = $_REQUEST['userid'];
        if (isset($id, $userid)) {
            $time = date("Y-m-d H:i:s");
            $aircraft = $data['aircraft'];
                // $aircraft = $_REQUEST['aircraft'];
            $route = "AFCS-V4.0";
            $moment = $data['moment'];
            $manner = $data['manner'];
            $park = $data['park'];
             $callsign=$data['callsign'];
            // $moment = $_REQUEST['moment'];
            // $manner =$_REQUEST['manner'];
            // $park = $_REQUEST['park'];
            // $callsign=$_REQUEST['callsign'];
            $insert_sql = $conn->prepare("INSERT INTO activity_user (user, route, aircraft, time, activity_time,park) VALUES (?, ?, ?, ?, ?,?);");
            $insert_sql->bind_param("ssssss", $userid, $route, $aircraft, $time, $id, $park);
            if ($insert_sql->execute()) {
                $insert_sql = $conn->prepare("INSERT INTO moment (cid,moment,time,manner,callsign) VALUES (?, ?, ?,?,?);");
                $insert_sql->bind_param("sssss", $userid, $moment, $id, $manner,$callsign);
                $insert_sql->execute();
                http_response_code(200);
                $data = array(
                    'id' => $id,
                    'user' => $userid
                );
                echo json_encode(array('status' => '200', 'data' => $data));
                exit;
            } else {
                http_response_code(404);
                echo json_encode(array('status' => '404', 'data' => '报名失败'));
                exit;
            }
        } else {
            http_response_code(300);
            echo json_encode(array('status' => '300', 'code' => '缺少必要的值'));
            exit;
        }
    } else {
        http_response_code(505);
        echo json_encode(array('status' => '505', 'code' => '错误的token'));
        exit;
    }
} else {
    http_response_code(500);
    echo json_encode(array('status' => '500', 'code' => '错误提交方法'));
    exit;
}

