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

include "setting.php";
require './mail.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') { //检测提交方式是否为 POST
    $postData = file_get_contents("php://input");
    $data = json_decode($postData, true);
    $token = $data['token'];
//    $token = $_REQUEST['token'];
    $token = hash('sha256', $token);
    if ($token == $token_Correct) {//鉴权
        /********获取数据*********/
        $cid = $data['cid'];
        $atcname = $data['atcname'];
        $en = $data['en'];
        $experience = $data['experience'];
        $online_time = $data['time'];
        $connect = $data['connect'];
        $reason = $data['reason'];

//        $cid = $_REQUEST['cid'];
//        $atcname =$_REQUEST['atcname'];
//        $en = $_REQUEST['en'];
//        $experience =$_REQUEST['experience'];
//        $online_time = $_REQUEST['time'];
//        $connect = $_REQUEST['connect'];
//        $reason =$_REQUEST['reason'];
        if (isset($cid,$atcname,$en,$experience,$online_time,$connect,$reason) ) {
            sendatc($cid,$atcname,$en,$experience,$online_time,$connect,$reason);
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode(array('status' => '200', 'code' => '成功'));
            exit;
        } else {
            http_response_code(300);
            header('Content-Type: application/json');
            echo json_encode(array('status' => '300', 'code' => '缺少必要的值'));
            exit;
        }
    } else {
        http_response_code(505);
        header('Content-Type: application/json');
        echo json_encode(array('status' => '505', 'code' => '错误的token'));
        exit;
    }
} else {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(array('status' => '500', 'code' => '错误提交方法'));
    exit;
}




