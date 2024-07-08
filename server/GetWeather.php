<?php
header("Content-Type: application/json");

// 检查请求的来源
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

// 允许特定域名跨域访问
$allowed_origins = ['http://localhost:5173', 'https://v4.ariven.cn','https://imp.skydreamclub.cn/','https://admin.imp.skydreamclub.cn/'];

if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: {$origin}");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Access-Control-Allow-Credentials: true");
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Handle preflight request
    http_response_code(200);
    exit();
}

include "setting.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postData = file_get_contents("php://input");
    $data = json_decode($postData, true);
    $token = $data['token'];
    $token = hash('sha256', $token);
    if ($token == $token_Correct) {
        $icao = $data['icao'];
        $secret = 'TUlJQlZBSUJBREFOQmdrcWhraUc5dzBCQVFFRkFBU0NBVDR3Z2dFNkFnRUFBa0VBeGRjaHFEdGkrSTNZZjNNcQpuY2prNTlVUERmV28wMU04V2tlMjlYeXRqRGYvWW5hZEFuNHp5dFVIbHBYVDFrL3UvVStjKzlFRG5QU3NzVUZCCk96RjVCd0lEQVFBQkFrQWk2d0IrdjlTTkNBUVJJcE4vKzhnaS91REVWdnB3S2YyNTlYUmVTWjRiNUNwd2hLUEMKbHd3WDdkS0NMUzJKWUU4dVdUOUdnUUJ2N2hvRE1RZ0IxL21oQWlFQStNejY0MHN5YU40ekl6aVJZalltQ00xZwpYWHBYZ3h0MDJuT2tHa1pna3BFQ0lRRExrS2dqV0xzSTVPNnVIemJCamN3dUpVMUFMTnV1TmJVcEZIS1E0ZXB1CkZ3SWhBT3B3YkRCbEdSa0wxMi9teThlWmNubDAzTXI0anlHeGE0aTAwdnNYT2NTaEFpQjRpNFFWMG1DSHB0SDAKaUlWclh1WFBXY1dDUUU0aXZxazExMmIwaHVQRkp3SWdET2pjK1ZjbDNUNHRuV1pUT0xsVVFLWVJuUjdFL21MMgozWHJiRlA2bWI1UT0=';
        $microtime = microtime();
        $secretJson = json_encode(array("secret" => hash("sha256", $secret . $microtime), "time" => $microtime));
        $encryData = base64_encode(openssl_encrypt($secretJson, "DES-CBC", "fI8~eR7!", 0, "oW2{eU8`"));

        $token_get_body = [
            'secret' => $encryData,
            'platform' => "cfcsim"
        ];

        $platform = "cfcsim";

        $url_token = "http://api.lvtenghui.com:8081/v1/auth/?secret=" . urlencode($token_get_body['secret']) . "&platform=" . urlencode($token_get_body['platform']);

        $token = file_get_contents($url_token);

        $token = json_decode($token, true);

        $token = $token['msg']['token'];

        $weather_get_body = [
            'token' => $token,
            'icao' => $icao
        ];
        $url_weather = "http://api.lvtenghui.com:8081/v1/weather/?icao=" . urlencode($weather_get_body['icao']) . "&token=" . urlencode($weather_get_body['token']);
        $weather_json = file_get_contents($url_weather);

        $weather_arr = json_decode($weather_json, true);

        $airport_get_body = [
            'token' => $token,
            'icao' => $icao
        ];
        $url_airport = "http://api.lvtenghui.com:8081/v1/airport/?icao=" . urlencode($weather_get_body['icao']) . "&token=" . urlencode($weather_get_body['token']);
        $airport_json = file_get_contents($url_airport);

        $airport_arr = json_decode($airport_json, true);
        http_response_code(200);
        $output_array = [
            'status'=>200,
            'airport' => $airport_arr['msg'],
            'weather' => $weather_arr['msg']
        ];

        echo json_encode($output_array, JSON_PRETTY_PRINT);
    } else {
        http_response_code(505);
        echo json_encode(array('status' => '505', 'code' => '错误的token'));
        exit;
    }
} else {
    http_response_code(500);
    echo json_encode(array('status' => '500', 'code' => '错误提交方法'));
    exit;
}

