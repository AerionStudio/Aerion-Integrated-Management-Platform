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
        $rating = $row['user_grade'];
        $t=0;
        if ($rating > '1') {
            $sql = $conn->prepare("SELECT * FROM history_atc WHERE cid=?");
            $sql->bind_param("s", $num);
            $sql->execute();
            $result = $sql->get_result();
            $alltime_atc=0;
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $alltime_atc += ($row['online_time']);
                }
                $sql = $conn->prepare("SELECT * FROM history_atc WHERE cid=? ORDER BY logon_time DESC LIMIT 1;");
                $sql->bind_param("s", $num);
                $sql->execute();
                $result = $sql->get_result();
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $t=1;
                    $callsign=$row['callsign'];
                    $frequency=$row['frequency'];
                    $logon_time=$row['logon_time'];
                }
            }
        }
        $sql = $conn->prepare("SELECT * FROM history WHERE cid=?");
        $sql->bind_param("s", $num);
        $sql->execute();
        $result = $sql->get_result();
        $alltime_flight=0;
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            while ($row = $result->fetch_assoc()) {
                $alltime_flight+=$row['online_time'];
            }

            $sql = $conn->prepare("SELECT * FROM history WHERE cid=? ORDER BY time DESC LIMIT 1;");
            $sql->bind_param("s", $num);
            $sql->execute();
            $result = $sql->get_result();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $client_name=$row['client_name'];
                $dep=$row['depairport'];
                $app=$row['destairport'];
                $aircraft=$row['aircraft'];
                $time=$row['time'];
            }
        }

        if ($rating>'1'&&$t){
            if (isset($alltime_flight)){
                $userData = array(
                    "status" => "201",
                    "rating"=>$rating,
                    "last_flight"=>array(
                        "dep"=>$dep,
                        "app"=>$app,
                        "aircraft"=>$aircraft,
                        "time"=>$time
                    ),
                    "last_atc"=>array(
                        "atc_name"=>$callsign,
                        "frequency"=>$frequency,
                        "time"=>$logon_time
                    ),
                    "online_time"=>array(
                        "flight"=>$alltime_flight,
                        "atc"=>$alltime_atc
                    )
                );
            }else{
                $userData = array(
                    "status" => "201",
                    "rating"=>$rating,
                    "last_flight"=>array(
                        "dep"=>$dep,
                        "app"=>$app,
                        "aircraft"=>$aircraft,
                        "time"=>$time
                    ),
                    "last_atc"=>array(
                        "atc_name"=>$callsign,
                        "frequency"=>$frequency,
                        "time"=>$logon_time
                    ),
                    "online_time"=>array(
                        "flight"=>0,
                        "atc"=>$alltime_atc
                    )
                );
            }

            // 返回响应数据
            echo json_encode($userData);
            exit; // 结束脚本执行
        }else{
          if (isset($alltime_flight)){
              $userData = array(
                  "status" => "200",
                  "rating"=>$rating,
                  "last_flight"=>array(
                      "dep"=>$dep,
                      "app"=>$app,
                      "aircraft"=>$aircraft,
                      "time"=>$time
                  ),
                  "online_time"=>array(
                      "flight"=>$alltime_flight,
                      "atc"=>0,
                  )
              );
          }else{
              $userData = array(
                  "status" => "200",
                  "rating"=>$rating,
                  "last_flight"=>array(
                      "dep"=>$dep,
                      "app"=>$app,
                      "aircraft"=>$aircraft,
                      "time"=>$time
                  ),
                  "online_time"=>array(
                      "flight"=>0,
                      "atc"=>0,
                  )
              );
          }
            // 返回响应数据
            echo json_encode($userData);
            exit; // 结束脚本执行
        }

    }else{
        $userData = array(
            "status" => "404",
        );
        // 返回响应数据
        echo json_encode($userData);
        exit; // 结束脚本执行
    }

}
?>