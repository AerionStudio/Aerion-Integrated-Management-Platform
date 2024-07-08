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


    $dep = $data['dep'];
    $app = $data['app'];
    $dep_icao = $data['dep_icao'];
    $app_icao = $data['app_icao'];
    $route = $data['route'];
    $takeoff_time = '0000';
    $state = '1'; // 设置默认状态为1
    $publisher = '6067';
    $time = $data['time'];
    $time= strstr($time, 'T', true);
    $direction = $data['dir'];
    $atc = $data['atc'];
    $atc_fq = $data['atc_fq'];
    $atc = implode(',', array_slice($atc, 0, -1));
    $atc_fq = implode(',', array_slice($atc_fq, 0, -1));
    $starttime=$data['starttime'];
    $starttime_re=$data['starttime_re'];
    $endtime_re=$data['endtime_re'];
    $nav=$data['nav'];
    $more=$data['more'];


    // 查询是否已经存在相同的数据
    $sql = "SELECT * FROM event WHERE dep='$dep' AND app='$app' AND time='$time'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        // 如果已经存在，则更新数据
        $sql = "UPDATE event SET dep='$dep', app='$app', time='$time', route='$route', Publisher='$publisher' WHERE dep='$dep' AND app='$app' AND time='$time' AND app_icao='$app_icao' AND dep_icao = '$dep_icao' AND takeoff_time='$takeoff_time' AND direction='$direction' AND atc='$atc' AND atc_fq='$atc_fq' AND starttime='$starttime' AND starttime_re='$starttime_re' AND endtime_re='$endtime_re' AND nav='$nav' AND more='$more' ";
        if (mysqli_query($conn, $sql)) {
            http_response_code(200);
            echo json_encode(array('status' => '200', 'code' => '更新成功'));
            exit;
        } else {
            http_response_code(500);
            echo json_encode(array('status' => '500', 'error' => '更新失败: ' . mysqli_error($conn)));
            exit;
        }
    } else {
        // 如果不存在，则插入数据
        $sql = "INSERT INTO event (dep, app, route, time, Publisher, state,dep_icao,app_icao,takeoff_time,direction,atc,atc_fq,starttime,starttime_re,endtime_re,nav,more) VALUES ('$dep', '$app', '$route', '$time', '$publisher', '$state','$dep_icao','$app_icao','$takeoff_time','$direction','$atc','$atc_fq','$starttime','$starttime_re','$endtime_re','$nav','$more')";
        if (mysqli_query($conn, $sql)) {
            http_response_code(200);
            echo json_encode(array('status' => '200', 'code' => '插入成功', 'link' => "https://imp.xfex.cc/activity/detailed.html?id={$time}"));
            exit;
        } else {
            http_response_code(500);
            echo json_encode(array('status' => '500', 'error' => '插入失败: ' . mysqli_error($conn)));
            exit;
        }
    }

    // 关闭连接
    mysqli_close($conn);
} else {
    http_response_code(405); // 请求方法不允许
    echo json_encode(array('status' => '405', 'error' => 'Method Not Allowed'));
}
?>
