<?php
// header.php

// 1. 確保 Session 啟動 (因為要讀取 $_SESSION['user_id'])
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. 確保資料庫連線 (檢查是否已經連線過，若無則連線)
if (!isset($pdo)) {
    $host = 'localhost';
    $db_name = 'mydatabase'; 
    $username = 'root';
    $password = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        // 如果 header 連線失敗，通常不顯示錯誤以免破壞版面，或者只 log 錯誤
        // echo "連線失敗"; 
    }
}

// 3. 通知邏輯：查詢未讀數量與訊息
$current_user_id = $_SESSION['user_id'] ?? 0;
$unread_count = 0;
$notifs = [];

if ($current_user_id > 0 && isset($pdo)) {
    // (A) 查未讀數量
    $stmt_count = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
    $stmt_count->execute([$current_user_id]);
    $unread_count = $stmt_count->fetchColumn();

    // (B) 查最近 5 筆通知
    $stmt_msg = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
    $stmt_msg->execute([$current_user_id]);
    $notifs = $stmt_msg->fetchAll(PDO::FETCH_ASSOC);
}
?>

<header>
    <div class="top-start">
        <ul class="dropdown">
            <li class="dropbtn"><a href="home.php"><img src="../2.0/img/logo.png" style="width: 90px;"></a></li>
            <li class="dropbtn"><a href="food.php">美味佳餚</a></li>
            <li class="dropbtn"><a href="upload.php">上傳料理</a></li>
            <li class="dropbtn"><a href="r&s.php">我的預約/分享</a></li>
            <li class="dropbtn"><a href="aboutus.php">關於我們</a></li>
        </ul>
    </div>
    
    <div class="top-end">
        <form class="search" action="search_result.php" method="GET">
            <input class="search-box" name="q" type="text" placeholder="search">
            <button class="search-icon"><img src="../2.0/img/search.png"></button>
        </form>
        
        <div class="notifications" style="position: relative; margin: 0 15px;">
            <a href="#" class="notif-btn">
                <img src="../2.0/img/notifications.png" style="width: 30px; height: 30px;">
                <?php if ($unread_count > 0): ?>
                    <span class="badge"><?php echo $unread_count; ?></span>
                <?php endif; ?>
            </a>
            
            <div class="notif-dropdown">
                <div class="notif-header">通知中心</div>
                <?php if (count($notifs) > 0): ?>
                    <?php foreach ($notifs as $n): ?>
                        <div class="notif-item <?php echo ($n['is_read'] == 0) ? 'unread' : ''; ?>">
                            <p><?php echo htmlspecialchars($n['message']); ?></p>
                            <small><?php echo $n['created_at']; ?></small>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="notif-item" style="text-align:center; color:#999;">目前沒有新通知</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="account">
            <a href="member.php"><img src="../2.0/img/account.png"></a>
        </div>
    </div>
</header>