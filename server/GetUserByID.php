<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

require './setting.php';

// 确保请求是 GET 方法
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    // 创建预处理语句
    $stmt = $conn->prepare("SELECT * FROM user WHERE user_num=?");

    // 检查预处理语句是否创建成功
    if ($stmt) {

        // 绑定参数
        $user_num = $_GET['user_num'];
        $stmt->bind_param("s", $user_num);

        // 执行查询
        if ($stmt->execute()) {

            // 获取结果
            $result = $stmt->get_result();

            // 处理查询结果
            if ($result) {
                $data = $result->fetch_assoc(); // 获取单行数据

                if (!empty($data)) {
                    http_response_code(200);
                    echo json_encode($data);
                } else {
                    http_response_code(200); // 假设0个结果也是有效响应
                    echo json_encode(array('message' => '0 results'));
                }
            } else {
                http_response_code(500); // 服务器内部错误
                echo json_encode(array('error' => $stmt->error));
            }
        } else {
            http_response_code(500); // 服务器内部错误
            echo json_encode(array('error' => $stmt->error));
        }

        // 关闭预处理语句
        $stmt->close();
    } else {
        http_response_code(500); // 服务器内部错误
        echo json_encode(array('error' => $conn->error));
    }

    // 关闭数据库连接
    $conn->close();

} else {
    http_response_code(405); // 请求方法不允许
    echo json_encode(array('status' => '405', 'error' => 'Method Not Allowed'));
}
?>
