<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

require './setting.php';

$dir = "./download";

// Ensure the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $files = scandir($dir);
    $result = array();
    $dlurl = 'https://server.skydreamclub.cn/download/';

    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..' && !is_dir($dir . '/' . $file)) {
            $filename = $file; // Set the filename
            $update_sql = $conn->prepare("SELECT * FROM download WHERE filename = ?");
            $update_sql->bind_param("s", $filename);
            $update_sql->execute();
            $res = $update_sql->get_result();

            if ($res->num_rows > 0) {
                $row = $res->fetch_assoc();
                switch ($row["type"]) {
                    case "1":
                        $result["Official"][] = array(
                            "name" => ($row["name"]) ? $row["name"] : "",
                            "introduce" => ($row["introduce"]) ? $row["introduce"] : "",
                            "version" => ($row["version"]) ? $row["version"] : "",
                            "size" => ($row["size"]) ? $row["size"] : "",
                            "manufacturers" => ($row["manufacturers"]) ? $row["manufacturers"] : "",
                            "link" => "{$dlurl}{$file}"
                        );
                        break;
                    case "2":
                        $result["PublicBeta"][] =  array(
                            "name" => ($row["name"]) ? $row["name"] : "",
                            "introduce" => ($row["introduce"]) ? $row["introduce"] : "",
                            "version" => ($row["version"]) ? $row["version"] : "",
                            "size" => ($row["size"]) ? $row["size"] : "",
                            "manufacturers" => ($row["manufacturers"]) ? $row["manufacturers"] : "",
                            "link" => "{$dlurl}{$file}"
                        );
                        break;
                }
            }
        }
    }
    echo json_encode($result);
} else {
    http_response_code(405); // Method not allowed
    echo json_encode(['status' => '405', 'error' => 'Method Not Allowed']);
}
?>