<?php
// 确保请求的来源
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';


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
    // $token = $_REQUEST['token'];
    // $token = hash("sha256", $token);

    if ($token == $token_Correct) {
        $id = $data['id'];
        $val = $data['grade'];
//  $id = $_REQUEST['id'];
//         $val = $_REQUEST['grade'];
        $callsign = $id;
        $val = intval($val);
        $newRating = $val;

        if ($newRating > 1) {
           $sql = "INSERT INTO `atcdisplay`(`callsign`, `qq`, `gradelist`, `remark`, `teacher`) VALUES ('$callsign', ' ', ' ', ' ', ' ')";
            $result = $conn->query($sql);
        }

        if ($newRating < 2) {
            $sql = "DELETE FROM `atcdisplay` WHERE `callsign` = $callsign";
            $result = $conn->query($sql);
        }

        $sql = "SELECT user_pwd FROM user WHERE user_num = '$callsign'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $hashedPassword = $row["user_pwd"];
        }

        $body = json_encode([
            'password' => $hashedPassword,
            'rating' => $newRating
        ]);

        $apiEndpoint = 'http://180.188.35.7:6067/users/' . $callsign;
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer 157fd42534a1a058a8ee3de1104dfbbfee6d37b1a3930157b25b24b8da4d680a'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiEndpoint);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        $sql = "UPDATE user SET user_grade = '$val' WHERE user_num LIKE '%$id%'";
        $result = mysqli_query($conn, $sql);

        if ($httpCode === 204) {
            http_response_code(200);
            echo json_encode(['status' => '200', 'code' => '成功']);
            exit;
        } elseif ($httpCode === 404) {
            http_response_code(200);
            echo json_encode(['status' => '201', 'code' => '呼号不存在']);
            exit;
        } else {
            http_response_code(200);
            echo json_encode(['status' => '201', 'code' => '错误']);
            exit;
        }

        $conn->close();
    } else {
        http_response_code(505);
        echo json_encode(['status' => '505', 'code' => '错误的token']);
        exit;
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => '405', 'error' => 'Method Not Allowed']);
}
?>
