<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
//token设置
$token_Correct="ab321818";
$token_Correct=hash('sha256',$token_Correct);

// Mysql 设置
$servername = "127.0.0.1";
$username = "imp";
$password = "ab321818";
$dbname = "imp";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//连飞服务器设置
$url_whazzup = "http://180.188.35.7:6067/whazzup.json"; // 将URL定义在这里



