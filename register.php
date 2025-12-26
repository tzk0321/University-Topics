<?php
    // 連接到資料庫
    require_once 'db.php';

    $name = mysqli_real_escape_string($conn, $_POST['username']);
    $account = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $sql = "INSERT INTO `users`(`name`, `account`, `password`) VALUES('$name', '$account', '$password')";

    if (mysqli_query($conn, $sql)) {
        echo "資料新增成功！";
    } else {
        echo "新增資料失敗: " . mysqli_error($conn);
    }

    mysqli_close($conn);

    header('location: home.php');
