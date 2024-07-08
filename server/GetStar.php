<?php
error_reporting(0);
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

include "setting.php"; // 包含数据库连接设置

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    // 定义目录路径
    $directory = './sidstar';

    // 获取查询参数
    $searchFilename = strtolower($_GET['icao']);
    // 搜索并输出JSON文件
    $sidstardata = searchAndOutputJsonFile($directory, $searchFilename);
    $output = array();

    for ($i = 0; $i < count($sidstardata['Airport']['Approach']); $i++) {
        $output[$i] = ['value' => $sidstardata['Airport']['Approach'][$i]['Name'], 'label' => $sidstardata['Airport']['Approach'][$i]['Name']
        ];


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


