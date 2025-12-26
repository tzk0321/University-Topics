<?php
    // 啟用交談期
    session_start();

    // 連接到資料庫
    require_once 'db.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // 從 POST 資料中取得資料
        $account = mysqli_real_escape_string($conn, $_POST['email']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);

        $sql = "SELECT * FROM users WHERE account ='$account' and password = '$password'";

        // 查詢使用者
        $result = mysqli_query($conn, $sql);
        $rows = mysqli_num_rows($result);  

        $user = mysqli_fetch_assoc($result);

        if($rows){
            //登入成功
            $_SESSION['is_login'] = TRUE;
            $_SESSION['user_id'] = $user['id'];
            header('Location: home.php');
        }else{
            //登入失敗
            $_SESSION['is_login'] = FALSE;
            $_SESSION['msg'] = '登入失敗，請確認帳號密碼!!';
            header('Location: member.php');
        }
    }