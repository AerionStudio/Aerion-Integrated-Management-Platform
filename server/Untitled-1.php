<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

include 'setting.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    $postData = file_get_contents("php://input");
    $data = json_decode($postData, true);
    $token = $data['token'];
    $token = hash('sha256', $token);
    if ($token == $token_Correct) {
        $user_pwd = $data['user_pwd'];
        $user_num = $data['user_num'];
        if (isset($user_num) && isset($user_pwd)) {
            $user_pwd = hash("sha256", $user_pwd);
            $sql = $conn->prepare("SELECT * FROM user WHERE user_num=? AND user_pwd=? ");
            $sql->bind_param("ss", $user_num, $user_pwd);
            $sql->execute();
            $result = $sql->get_result();
            if ($result->num_rows > 0) {
                $data = $result->fetch_all(MYSQLI_ASSOC);
                $sql = $conn->prepare("SELECT * FROM whitelist WHERE cid=?");
                $sql->bind_param("s", $user_num);
                $sql->execute();
                $result = $sql->get_result();
                if ($result->num_rows <= 0) {
                    http_response_code(200);
                    echo json_encode(array('status' => '200', 'code' => '成功'));
                    exit;
                } else {
                    http_response_code(200);
                    echo json_encode(array('status' => '202', 'code' => '不在白名单'));
                    exit;
                }
            } else {
                http_response_code(201);
                header('Content-Type: application/json');
                echo json_encode(array('status' => '201', 'code' => '登录失败'));
                exit;
            }
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
?>