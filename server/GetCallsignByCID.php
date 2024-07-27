<?php
header("Content-Type: application/json");

// List of allowed origins for CORS
$allowed_origins = [
    'http://localhost:5173',
    'https://v4.ariven.cn',
    'https://imp.skydreamclub.cn/',
    'https://admin.imp.skydreamclub.cn/'
];

// Check if the request's origin is allowed
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Access-Control-Allow-Credentials: true");

    // Handle preflight requests
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }
}

// Include database settings
include "setting.php";

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postData = file_get_contents("php://input");
    $data = json_decode($postData, true);

    // Verify the token
    $token = hash('sha256', $data['token']);
    if ($token === $token_Correct) {
        // Extract required data
        $cid = $data['cid'];

        // Check if required data is present
        if (isset($time, $atc)) {
            $url = 'https://server.skydreamclub.cn/whazzup.php';

            // Initialize cURL session
            $ch = curl_init();

            // Set cURL options
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // Execute the request
            $response = curl_exec($ch);

            // Check if the request was successful
            if (curl_getinfo($ch, CURLINFO_HTTP_CODE) === 200) {
                // Decode the JSON response
                $data = json_decode($response, true);
                if (isset($data['pilot'])) {
                    // Find the flight with the specified callsign
                    $flights = $data['pilot'];
                    foreach ($flights as $flight) {
                        if ($flight['cid'] === $cid) {
                            // Return the flight information
                            return [
                                'callsign' => $flight['callsign'],
                                'departure' => ['icao' => $flight['flight_plan']['departure'], 'name' => ''],
                                'arrival' => ['icao' => $flight['flight_plan']['arrival'], 'name' => ''],
                                'groundspeed' => $flight['groundspeed'],
                                'altitude' => $flight['altitude'],
                                'heading' => $flight['heading'],
                                'transponder' => $flight['transponder'],
                                'aircraft' => ['name' => $flight['flight_plan']['aircraft'], 'registration' => ''],
                                'pilot' => ['username' => $flight['name']],
                            ];
                        }
                    }
                }
            }
        } else {
            http_response_code(400);
            echo json_encode(['status' => '400', 'code' => '缺少必要的值']);
        }
    } else {
        http_response_code(403);
        echo json_encode(['status' => '403', 'code' => '错误的token']);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => '405', 'code' => '错误提交方法']);
}
?>