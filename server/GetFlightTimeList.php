<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

require './setting.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $sql = "SELECT cid, SUM(online_time) AS online_time 
            FROM (
                SELECT cid, online_time FROM history
                UNION ALL
                SELECT cid, online_time FROM history_atc
            ) AS combined_data
            GROUP BY cid
            ORDER BY online_time DESC
            LIMIT 15";

    $result = $conn->query($sql);

    $output_data = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $onlineTimeSeconds = $row['online_time']; // Get total online_time in seconds
            $hours = floor($onlineTimeSeconds / 3600); // Calculate hours
            $minutes = floor(($onlineTimeSeconds % 3600) / 60); // Calculate minutes
            $seconds = $onlineTimeSeconds % 60; // Calculate seconds

            $output_data[] = array(
                'CID' => $row['cid'],
                'Online_Time_Hours' => sprintf("%02d小时%02d分钟%02d秒", $hours, $minutes, $seconds) // Format the time
            );
        }

        http_response_code(200);
        echo json_encode($output_data);
    } else {
        http_response_code(200); // Assuming 0 results is still a valid response
        echo json_encode(array('message' => '0 results'));
    }

    $conn->close();

} else {
    http_response_code(405);
    echo json_encode(array('status' => '405', 'error' => 'Method Not Allowed'));
}



