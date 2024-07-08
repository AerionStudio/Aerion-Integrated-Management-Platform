<?php
header("Content-Type: application/json");

// 检查请求的来源
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

// 允许特定域名跨域访问
$allowed_origins = ['http://localhost:5173', 'https://v4.ariven.cn','https://imp.skydreamclub.cn/','https://admin.imp.skydreamclub.cn/'];

if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: {$origin}");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Access-Control-Allow-Credentials: true");
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Handle preflight request
    http_response_code(200);
    exit();
}




if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $jsonData = file_get_contents('./coord.json');
    $dataArray = json_decode($jsonData, true);
    $searchedValue = $_GET['atc_id']; // Value to search for
    $foundItems = [];

    foreach ($dataArray as $item) {
        if ($item['name'] === $searchedValue) {
            $foundItems[] = $item['coord'];
        }
    }

    if (!empty($foundItems)) {
        http_response_code(200);
        echo json_encode(array('status' => '200', 'data' => $foundItems));
    } else {
        http_response_code(200); // Not Found
        echo json_encode(array('status' => '404', 'error' => 'Not Found'));
    }

} else {
    http_response_code(405);
    echo json_encode(array('status' => '405', 'error' => 'Method Not Allowed'));
}

