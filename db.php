<?php
    $conn = new mysqli("localhost", "root", "", "mydatabase");
    if (!$conn) {
            die("資料庫連線失敗: " . mysqli_connect_error());
        }
    mysqli_set_charset($conn, "utf8");

