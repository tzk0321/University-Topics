<?php

    header('Content-Type: application/json'); // 設定回傳格式為 JSON

    // 連接到資料庫
    require_once 'db.php';
    $host = 'localhost';
    $db_name = 'mydatabase'; 
    $username = 'root';
    $password = '';

    // 即期食品
    $conn = new mysqli($host, $username, $password, $db_name);
    if ($conn->connect_error) {
        die(json_encode(["error" => "Connection failed"]));
    }

    // 設定時區
    date_default_timezone_set('Asia/Taipei');
    $conn->query("SET time_zone = '+08:00'");

    $sql = "SELECT * FROM food_items 
        WHERE expiry_datetime > DATE_ADD(NOW(), INTERVAL 10 MINUTE) 
        AND expiry_datetime <= DATE_ADD(NOW(), INTERVAL 24 HOUR)
        ORDER BY expiry_datetime ASC";

    $result = $conn->query($sql);

    $foods = [];
    while($row = $result->fetch_assoc()) {
        // 為了前端方便，我們可以在這裡先算好「剩餘時間」的字串，或者直接回傳日期給前端算
        // 這裡示範直接回傳原始資料
        $foods[] = $row;
    }

    // 3. 輸出 JSON
    echo json_encode($foods);

    $conn->close();


