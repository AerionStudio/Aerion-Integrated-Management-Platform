<?php
error_reporting(0);

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

include "setting.php"; // 包

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    // 定义目录路径
    $directory = './sidstar';

    // 获取查询参数
    $searchFilename = strtolower($_GET['icao']);
    $tr = $_GET['tr'];
    $ils = $_GET['ils'];
    // 搜索并输出JSON文件
    $sidstardata = searchAndOutputJsonFile($directory, $searchFilename);
    $output = array();
    $k = 0;
    for ($i = 0; $i < count($sidstardata['Airport']['Approach']); $i++) {
        if ($sidstardata['Airport']['Approach'][$i]['Name'] == $ils) {
            for ($j = 0; $j < count($sidstardata['Airport']['Approach'][$i]['App_Waypoint']); $j++) {
                if ($sidstardata['Airport']['Approach'][$i]['App_Waypoint'][$j]['Latitude']['text'] != '0.000000' && $sidstardata['Airport']['Approach'][$i]['App_Waypoint'][$j]['Longitude'] != '0.000000') {
                    $output['star'][$k++] = [$sidstardata['Airport']['Approach'][$i]['App_Waypoint'][$j]['Name']['text'], $sidstardata['Airport']['Approach'][$i]['App_Waypoint'][$j]['Latitude']['text'], $sidstardata['Airport']['Approach'][$i]['App_Waypoint'][$j]['Longitude']['text']];
                }
            }
            for ($k = 0; $k < count($sidstardata['Airport']['Approach'][$i]['App_Transition']); $k++) {
                if ($sidstardata['Airport']['Approach'][$i]['App_Transition'][$k]['Name'] == $tr) {
                    for ($l = 0; $l < count($sidstardata['Airport']['Approach'][$i]['App_Transition'][$k]['AppTr_Waypoint']); $l++) {
                        $output['tr'][$l] = [$sidstardata['Airport']['Approach'][$i]['App_Transition'][$k]['AppTr_Waypoint'][$l]['Name']['text'], $sidstardata['Airport']['Approach'][$i]['App_Transition'][$k]['AppTr_Waypoint'][$l]['Latitude']['text'], $sidstardata['Airport']['Approach'][$i]['App_Transition'][$k]['AppTr_Waypoint'][$l]['Longitude']['text']];
                    }
                }
            }
        }
    }
    for ($m = 0; $m < count($output['tr']+ $output['star']); $m++) {

    }
    http_response_code(200);
    echo json_encode(array('status' => '200', 'code' => $output));
    exit;
} else {
    http_response_code(500);
    echo json_encode(array('status' => '500', 'code' => '错误提交方法'));
    exit;
}

function searchAndOutputJsonFile($dir, $filename)
{
    // 确保目录路径以斜杠结尾
    $dir = rtrim($dir, '/') . '/';

    // 打开目录
    if (is_dir($dir)) {
        if ($dh = opendir($dir)) {
            // 遍历目录中的文件和文件夹
            while (($file = readdir($dh)) !== false) {
                // 跳过 . 和 .. 目录
                if ($file == '.' || $file == '..') {
                    continue;
                }

                // 检查文件扩展名是否为 .json 且文件名中包含传入的搜索字符串
                if (is_file($dir . $file) && pathinfo($file, PATHINFO_EXTENSION) === 'json' && strpos($file, $filename) !== false) {
                    // 读取并输出文件内容
                    $content = file_get_contents($dir . $file);
                    return json_decode($content, true); // 返回解析后的JSON数据
                }
            }

            // 关闭目录句柄
            closedir($dh);
        } else {
            echo "Cannot open directory: $dir";
        }
    } else {
        echo "Not a valid directory: $dir";
    }
    return null;
}


