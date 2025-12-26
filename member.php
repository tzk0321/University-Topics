<?php require_once 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>會員專區 | Food Sharing</title>
    <link rel="shortcut icon" href="img/favorites.png" type="image/x-icon">
    <link rel="stylesheet" href="../2.0/css/member.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
</head>
<body>
    
    <div class="all">
        <div class="homebtn">
            <a href="home.php">
                <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#77a692"><path d="M226.67-186.67h140v-246.66h226.66v246.66h140v-380L480-756.67l-253.33 190v380ZM160-120v-480l320-240 320 240v480H526.67v-246.67h-93.34V-120H160Zm320-352Z"/></svg>
            </a>
        </div>
        <div class="container">
            <div class="form-box login">
                <form action="login.php" method="post">
                    <h1>登入</h1>
                    <div class="input-box">
                        <input type="text" name="email" placeholder="電子郵件" required>
                        <span class="email-icon material-symbols-outlined">mail</span>
                    </div>
                    <div class="input-box">
                        <input type="password" name="password" placeholder="密碼" required>
                        <span class="password-icon material-symbols-outlined">lock</span>
                    </div>
                    <button type="submit" class="btn">登入</button>
                </form>
            </div>

            <div class="form-box register">
                <form action="register.php" method="post">
                    <h1>註冊</h1>
                    <div class="input-box">
                        <input type="text" name="username" placeholder="使用者名稱" required>
                        <span class="material-symbols-outlined">person</span>
                    </div>
                    <div class="input-box">
                        <input type="text" name="email" placeholder="電子郵件" required>
                        <span class="email-icon material-symbols-outlined">mail</span>
                    </div>
                    <div class="input-box">
                        <input type="password" name="password" placeholder="密碼" required>
                        <span class="password-icon material-symbols-outlined">lock</span>
                    </div>
                    <button type="submit" class="btn">註冊</button>
                </form>
            </div>

            <div class="toggle-box">
                <div class="toggle-panel toggle-left">
                    <h1>哈囉！歡迎加入</h1>
                    <p style="margin-top: 20px;">尚未擁有帳號嗎？</p>
                    <button class="btn register-btn" style="margin-top: 10px;">註冊</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <h1>歡迎回來</h1>
                    <p style="margin-top: 20px;">已經擁有帳號嗎？</p>
                    <button class="btn login-btn" style="margin-top: 10px;"> 登入</button>
                </div>
            </div>
        </div>
    </div>

    <script src="member.js"></script>

</body>
</html>