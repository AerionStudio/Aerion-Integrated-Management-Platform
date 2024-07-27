<?php

include "setting.php";

$jsonUrl = 'http://180.188.35.7:6067/whazzup.json';
$jsonData = file_get_contents($jsonUrl);
$data = json_decode($jsonData, true);

foreach ($data['pilot'] as $pilot) {
    $cid = $pilot['cid'];
    $callsign = $pilot['callsign'];
    $last_updated = $pilot['last_updated'];
    $departure = $pilot['flight_plan']['departure'];
    $arrival = $pilot['flight_plan']['arrival'];
    $aircraft = $pilot['flight_plan']['aircraft'];
    $route = $pilot['flight_plan']['route'];
    $heading = $pilot['heading'];
    $groundspeed = $pilot['groundspeed'];
    $transponder = $pilot['transponder'];
    $altitude = $pilot['altitude'];
    $longitude = $pilot['longitude'];
    $latitude = $pilot['latitude'];
    $time = $pilot['logon_time'];


    $sql = "SELECT * FROM history WHERE cid = '$cid' AND time = '$time'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {

        $onlineTime = strtotime($time); 
        $offlineTime = time() ; 
        $onlineDuration = $offlineTime - $onlineTime;
        $sqlSelect = "SELECT * FROM history WHERE cid = '$cid' AND time = '$time'";
        $resultSelect = $conn->query($sqlSelect);
        if ($resultSelect->num_rows > 0) {
            $row = $resultSelect->fetch_assoc();
            $time_1 = $row['online_time'];
            echo $cid."时长".$time_1.'</br>';
            $time_2=$onlineDuration- $time_1;
            $point=$time_2*getuserflighttime($cid);
            
            echo $cid."时长".$time_2.'</br>';
            echo $cid."积分增长比例".getuserflighttime($cid).'</br>';
            echo $cid."新增积分".$point.'</br>';
        }
        $sqlSelect = "SELECT * FROM user WHERE user_num = '$cid'";
        $resultSelect = $conn->query($sqlSelect);
        if ($resultSelect->num_rows > 0) {
            $row = $resultSelect->fetch_assoc();
            $point=$row['integral']+$point;
        }
        $sql = 'UPDATE user SET integral = '.$point.' WHERE user_num = "'.$cid.'"';
        if ($conn->query($sql) === TRUE) {
            echo $cid."成功更新积分".$point.'</br>';
        }else{
            echo $cid."失败更新积分".$point.'</br>';
        }
        $sql = "UPDATE history SET online_time = '$onlineDuration' WHERE cid = '$cid' AND time = '$time'";
        if ($conn->query($sql) === TRUE) {
            echo "记录更新成功";
        } else {
            echo "Error: " . $sql . "</br>" . $conn->error;
        }


        $sqlSelect = "SELECT * FROM online WHERE cid = '$cid' AND time = '$time'";
        $resultSelect = $conn->query($sqlSelect);

        if ($resultSelect->num_rows > 0) {
            $row = $resultSelect->fetch_assoc();
            $existingLat = $row['lat'];
            $existingLot = $row['lon'];
            $existingAltitude = $row['altitude'];
            $existingGroundspeed = $row['groundspeed'];
            $existingFlightTimeOnline = $row['flighttimeonline'];
            // Concatenate existing and new data with commas
            $newLat = $existingLat . ',' . $latitude;
            $newLot = $existingLot . ',' . $longitude;
            $newAltitude = $existingAltitude . ',' . $altitude;
            $newGroundspeed = $existingGroundspeed . ',' . $groundspeed;
            $newFlightTimeOnline = $existingFlightTimeOnline . ',' . $last_updated;
            // Update the record with the concatenated data
            $sqlUpdate = "UPDATE online SET lat = '$newLat', lon = '$newLot', altitude = '$newAltitude', groundspeed = '$newGroundspeed',flighttimeonline='$newFlightTimeOnline' WHERE cid = '$cid' AND time = '$time'";
            if ($conn->query($sqlUpdate) === TRUE) {
                echo "123";
            } else {
                echo "Error: " . $sqlUpdate . "</br>" . $conn->error;
            }
        } else {
            $sql = "INSERT INTO online (cid, lat, lon, time, groundspeed, altitude,flighttimeonline) VALUES ('$cid', '0', '0', '$time', '0', '0', '0')";
            if ($conn->query($sql) === TRUE) {
                echo $cid."新记录插入成功".$time."</br>";
            } else {
                echo "Error: " . $sql . "</br>" . $conn->error;
            }
        }
    } else {
        // 表示用户刚刚连接，插入新记录到history表
        if (isset($departure)) {
            $onlineTime = strtotime($time); // 将时间转换为时间戳
            $offlineTime = time() + 28800; // 获取当前时间戳
            $onlineDuration = $offlineTime - $onlineTime;
            $sql = "INSERT INTO history (client_name, depairport, destairport, aircraft, cid, time, online_time) VALUES ('$callsign', '$departure', '$arrival', '$aircraft', '$cid', '$time', '$onlineDuration')";
            if ($conn->query($sql) === TRUE) {
                echo $cid."新记录插入成功".$time."</br>";
            } else {
                echo "Error: " . $sql . "</br>" . $conn->error;
            }

            // Insert new record into the online table
            $sql = "INSERT INTO online (cid, lat, lon, time, groundspeed, altitude,flighttimeonline) VALUES ('$cid', '0', '0', '$time', '0', '0', '0')";
            if ($conn->query($sql) === TRUE) {
                echo $cid."新记录插入成功".$time."</br>";
            } else {
                echo "Error: " . $sql . "</br>" . $conn->error;
            }
        }
    }
}

foreach ($data['controllers'] as $atc) {
    $cid = $atc['cid'];
    $callsign = $atc['callsign'];
    $frequency = $atc['frequency'];
    $time = $atc['logon_time'];

    // 更新history表
    $sql = "SELECT * FROM history_atc WHERE cid = '$cid' AND logon_time = '$time'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // 表示用户已经连接，更新history表
        $onlineTime = strtotime($time); // 将时间转换为时间戳
        $offlineTime = time() ; // 获取当前时间戳
        $onlineDuration = $offlineTime - $onlineTime;

        // Update the history_atc table
        $sql = "UPDATE history_atc SET online_time = '$onlineDuration', frequency='$frequency' WHERE cid = '$cid' AND logon_time = '$time'";
        if ($conn->query($sql) === TRUE) {
            echo $cid."新记录更新成功".$time."</br>";
        } else {
            echo "Error: " . $sql . "</br>" . $conn->error;
        }

        // Update the online table with comma-separated frequency
        $sqlSelect = "SELECT * FROM online WHERE cid = '$cid' AND time = '$time'";
        $resultSelect = $conn->query($sqlSelect);

        if ($resultSelect->num_rows > 0) {
            $row = $resultSelect->fetch_assoc();
            $existingFrequency = $row['frequency'];

            // Concatenate existing and new frequency with commas
            $newFrequency = $existingFrequency . ',' . $frequency;

            // Update the record with the concatenated frequency
            $sqlUpdate = "UPDATE online SET frequency = '$newFrequency' WHERE cid = '$cid' AND time = '$time'";
            if ($conn->query($sqlUpdate) === TRUE) {
                echo $cid."新记录更新成功".$time."</br>";
            } else {
                echo "Error: " . $sqlUpdate . "</br>" . $conn->error;
            }
        }
    } else {
        // 表示用户刚刚连接，插入新记录到history_atc表
        if (isset($frequency)) {
            $onlineTime = strtotime($time); // 将时间转换为时间戳
            $offlineTime = time() ; // 获取当前时间戳
            $onlineDuration = $offlineTime - $onlineTime;

            // Insert new record into history_atc table
            $sql = "INSERT INTO history_atc (cid, callsign, frequency, logon_time, online_time) VALUES ('$cid', '$callsign', '$frequency', '$time', '$onlineDuration')";
            if ($conn->query($sql) === TRUE) {
                echo $cid."新记录插入成功".$time."</br>";
            } else {
                echo "Error: " . $sql . "</br>" . $conn->error;
            }

            // Insert new record into online table
            $sql = "INSERT INTO online (cid, frequency, time) VALUES ('$cid', '$frequency', '$time')";
            if ($conn->query($sql) === TRUE) {
                echo $cid."新记录插入成功".$time."</br>";
            } else {
                echo "Error: " . $sql . "</br>" . $conn->error;
            }
        }
    }
}

$conn->close();


function getuserflighttime($cid) {
    global $conn; // Ensure $conn is accessible within the function
    $sql = $conn->prepare("SELECT * FROM history WHERE cid=?");
    $sql->bind_param("s", $cid);
    $sql->execute();
    $result = $sql->get_result();
    $alltime_flight = 0;
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $alltime_flight += $row['online_time'];
        }
        return getuserHourlywages($alltime_flight);
    } else {
        echo "0 results";
    }
}


function getuserHourlywages($time) {
    if ($time >= 360000 && $time < 720000) {
        return 0.014;
    } elseif ($time >= 720000 && $time < 1080000) {
        return 0.017;
    } elseif ($time >= 1080000 && $time < 1440000) {
        return 0.019;
    } elseif ($time >= 1440000 && $time < 1800000) {
        return 0.022;
    } elseif ($time >= 1800000 && $time < 2160000) {
        return 0.025;
    } elseif ($time >= 2160000) {
        return 0.028;
    } else {
        return 0.01;
    }
}
