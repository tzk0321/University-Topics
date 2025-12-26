<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id = $_GET['id'] ?? null; 

if (!$id) {
    echo "錯誤：未指定商品 ID";
    exit;
}


require_once 'db.php';
    $host = 'localhost';
    $db_name = 'mydatabase'; 
    $username = 'root';
    $password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "資料庫連線失敗: " . $e->getMessage();
    exit;
}

$sql = "SELECT food_items.*, users.name AS publisher_name 
        FROM food_items 
        LEFT JOIN users ON food_items.user_id = users.id 
        WHERE food_items.food_id = ?";

$stmt = $pdo->prepare($sql); 
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item) {
    echo "找不到這筆資料";
    exit;
}

// (B) 查詢此食物對應的標籤
$sql_tags = "SELECT labels.label_name 
             FROM labels 
             JOIN food_items_label ON labels.label_id = food_items_label.label_id 
             WHERE food_items_label.food_id = ?";

$stmt_tags = $pdo->prepare($sql_tags);
$stmt_tags->execute([$id]);
$tags = $stmt_tags->fetchAll(PDO::FETCH_ASSOC);

// (C) 定義標籤對應的圖案 (Emoji)
$tag_icons = [
    '便當' => '🍱',
    '麵食' => '🍜',
    '飯食' => '🍚',
    '飲料' => '🥤',
    '麵包' => '🥐',
    '蛋糕' => '🍰',
    '素食' => '🥗',
    '罐頭' => '🥫',
    '有機' => '🌾',
    '即期食品' => '⏰'
];

// ==========================================
// 3. 處理「新增留言」與「通知發布者」
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    $my_id = $_SESSION['user_id'] ?? null; 
    $content = trim($_POST['comment_content']);

    if (!$my_id) {
        echo "<script>alert('請先登入才能留言！');</script>";
    } elseif (empty($content)) {
        echo "<script>alert('留言內容不能為空！');</script>";
    } else {
        // (1) 寫入留言
        $sql_insert = "INSERT INTO comments (food_id, user_id, content) VALUES (?, ?, ?)";
        $stmt_insert = $pdo->prepare($sql_insert);
        $stmt_insert->execute([$id, $my_id, $content]);
        
        // (2) 發送通知給發布者
        $publisher_id = $item['user_id'];
        if ($my_id != $publisher_id) {
            $food_name = $item['food_name'];
            $short_content = mb_substr($content, 0, 10, "utf-8") . (mb_strlen($content) > 10 ? "..." : "");
            $msg = "有人在您的料理「{$food_name}」留言：{$short_content}";
            
            $sql_notif = "INSERT INTO notifications (user_id, message, created_at, is_read) VALUES (?, ?, NOW(), 0)";
            $stmt_notif = $pdo->prepare($sql_notif);
            $stmt_notif->execute([$publisher_id, $msg]);
        }
        
        // 重新整理頁面
        header("Location: detail.php?id=$id");
        exit;
    }
}

// ==========================================
// 4. 抓取這筆食物的所有留言
// ==========================================
$sql_comments = "SELECT comments.*, users.name
                 FROM comments 
                 LEFT JOIN users ON comments.user_id = users.id 
                 WHERE comments.food_id = ? 
                 ORDER BY comments.created_at DESC";

$stmt_comments = $pdo->prepare($sql_comments);
$stmt_comments->execute([$id]);
$comment_list = $stmt_comments->fetchAll(PDO::FETCH_ASSOC);

// ==========================================
// 5. Header 通知鈴鐺資料 (給登入者看)
// ==========================================
$current_user_id = $_SESSION['user_id'] ?? 0;
$unread_count = 0;
$notifs = [];

if ($current_user_id > 0) {
    // 查未讀數量
    $stmt_count = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
    $stmt_count->execute([$current_user_id]);
    $unread_count = $stmt_count->fetchColumn();

    // 查最近通知
    $stmt_msg = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
    $stmt_msg->execute([$current_user_id]);
    $notifs = $stmt_msg->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>詳細資訊 | food sharing</title>
    <link rel="shortcut icon" href="img/favorites.png" type="image/x-icon">
    <link rel="stylesheet" href="../2.0/css/detail.css">
    <style>
        .badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background-color: #ff3b30; /* 紅色 */
    color: white;
    border-radius: 50%;
    padding: 2px 6px;
    font-size: 12px;
    font-weight: bold;
    border: 2px solid white; /* 讓紅點跟圖案有點間隔 */
}

/* 下拉選單容器 (預設隱藏) */
.notif-dropdown {
    display: none; /* 隱藏 */
    position: absolute;
    top: 40px; /* 在鈴鐺下方 */
    right: 0;
    width: 300px;
    background-color: white;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    border-radius: 8px;
    z-index: 1000;
    overflow: hidden;
    border: 1px solid #eee;
}

/* 當滑鼠移到 .notifications 上時，顯示下拉選單 */
/* 也可以改成用點擊觸發 JS，這裡先用 CSS hover 比較簡單 */
.notifications:hover .notif-dropdown {
    display: block;
}

/* 通知標題 */
.notif-header {
    background-color: #f8f9fa;
    padding: 10px 15px;
    font-weight: bold;
    border-bottom: 1px solid #eee;
    color: #333;
}

/* 單條通知 */
.notif-item {
    padding: 10px 15px;
    border-bottom: 1px solid #f0f0f0;
    transition: background 0.2s;
}

.notif-item:hover {
    background-color: #fafafa;
}

/* 未讀通知的樣式 (稍微深一點的背景或粗體) */
.notif-item.unread {
    background-color: #fff8e1; /* 淺橘黃色背景 */
    border-left: 3px solid #ff9800;
}

.notif-item:last-child {
    border-bottom: none;
}


        /* footer */
        footer{
            width: 100%;
            padding-bottom: 30px;
            height: 275px;
            position: relative;
            margin-top: 75px;
            background-color: rgb(134,157,157);
            display: flex;
            justify-content: start;
        }

        .all{
            width: 80%;
            height: 255px;
            display: flex;
            justify-content: space-around;
            align-items: center;
        }

        .information{
            color: rgb(216,216,205);
            height: 200px;

        }

        .links, .smthing{
            color: rgb(216,216,205);
            height: 200px;
            width: 300px;
        }

        .social{
            color: rgb(216,216,205);
            height: 200px;
            width: 175px;
        }

        .links a{
            color: rgb(216,216,205);
            font-size: 16px;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="article" style="width: 100%; display: flex; justify-content: center; margin-top: 150px; padding-bottom: 50px;">
        
        <div style="width: 85%; max-width: 1200px;">
            
            <div style="display: flex; align-items: center; margin-bottom: 30px; padding-left: 125px;">
                <img src="../2.0/img/user_img.PNG" style="width: 50px; height: 50px; border-radius: 50%; margin-right: 15px; object-fit: cover;">
                <h2 style="margin: 0; color: #444;">
                    <?php echo htmlspecialchars($item['publisher_name'] ?? '未知使用者'); ?>
                </h2>
            </div>

            <div style="display: flex; flex-wrap: wrap; gap: 5px;">
                
                <div style="flex: 1; min-width: 300px; display: flex; justify-content: center; align-items: start;">
                    <?php if (!empty($item['image_filename'])): ?>
                        <img src="uploads/<?php echo htmlspecialchars($item['image_filename']); ?>" 
                             alt="商品圖片"
                             style="width: 100%; max-width: 275px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                    <?php else: ?>
                        <div style="width: 100%; height: 300px; background: #eee; display: flex; align-items: center; justify-content: center; border-radius: 10px;">
                            尚無圖片
                        </div>
                    <?php endif; ?>
                </div>

                <div style="flex: 1; min-width: 300px;">
                    <h1 style="margin: 0 0 25px 0; font-size: 36px; color: #333;">
                        <?php echo htmlspecialchars($item['food_name']); ?>
                    </h1>

                    <div class="info-row">
                        <span class="info-label">份數：</span>
                        <span class="info-content">
                            <?php echo htmlspecialchars($item['quantity'] . ' ' . ($item['unit'] ?? '')); ?>
                        </span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">類別：</span>
                        <span class="info-content"><?php echo htmlspecialchars($item['category']); ?></span>
                    </div>

                    <div class="info-row vertical">
                        <div class="info-label">標籤：</div>
                        <div class="info-content tag-container">
                            <?php if (isset($tags) && count($tags) > 0): ?>
                                <?php foreach ($tags as $tag): ?>
                                    <?php 
                                        $name = $tag['label_name']; 
                                        $icon = $tag_icons[$name] ?? '🏷️'; 
                                    ?>
                                    <span class="food-tag">
                                        <?php echo $icon . ' ' . htmlspecialchars($name); ?>
                                    </span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span style="color: #999; font-size: 14px;">(未設定)</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="info-row">
                        <span class="info-label">有效日期：</span>
                        <span class="info-content">
                            <?php echo htmlspecialchars($item['expiry_datetime']); ?>
                        </span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">面交地點：</span>
                        <span class="info-content">
                            <?php echo htmlspecialchars($item['pickup_address_city'] . ' ' . $item['pickup_landmark']); ?>
                        </span>
                    </div>
                    
                    <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">

                    <div class="info-row">
                        <span class="info-label">食材來源：</span>
                        <span class="info-content"><?php echo htmlspecialchars($item['origin']); ?></span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">保存方式：</span>
                        <span class="info-content"><?php echo htmlspecialchars($item['storage_method']); ?></span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">狀態：</span>
                        <span class="info-content"><?php echo htmlspecialchars($item['item_state']); ?></span>
                    </div>

                    <div class="info-row" style="align-items: flex-start;">
                        <span class="info-label">備註：</span>
                        <span class="info-content" style="white-space: pre-wrap;"><?php echo htmlspecialchars($item['remark']); ?></span>
                    </div>

                    <div style="margin-top: 30px; display: flex; gap: 15px;">
                        <button id="btn-open-modal" 
                                data-food-id="<?php echo $item['food_id']; ?>" 
                                data-publisher-id="<?php echo $item['user_id']; ?>"
                                style="border: none; cursor: pointer; color: white; background-color: rgb(141,168,163); padding: 10px 20px; border-radius: 5px; font-size: 16px;">
                            立即預約
                        </button>
                    </div>

                    <div id="reserveModal" class="modal-overlay">
                        <div class="modal-content">
                            <h3 style="margin-top: 0; color: #333;">確認預約</h3>
                            <p style="color: #666; font-size: 16px; line-height: 1.6;">
                                若預約成功，我承諾會準時赴約並領取食物。<br>
                                <span style="font-size: 14px; color: #999;">(按下確定後將通知發布者)</span>
                            </p>
                            <div class="modal-actions">
                                <button id="btn-cancel" class="modal-btn cancel">我再想想</button>
                                <button id="btn-confirm" class="modal-btn confirm">確定</button>
                            </div>
                        </div>
                    </div>
                </div> </div> </div>
    </div>

    <div style="width: 100%; display: flex; justify-content: center; margin-top: 50px; margin-bottom: 50px;">
        <div style="width: 85%; max-width: 800px; background: #fff; border-radius: 10px; padding: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #eee;">
            
            <h3 style="border-left: 5px solid rgb(141,168,163); padding-left: 10px; margin-bottom: 20px; color: #444;">
                留言板 (<?php echo count($comment_list ?? []); ?>)
            </h3>

            <div style="margin-bottom: 30px;">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <form method="POST" action="">
                        <div style="display: flex; gap: 15px;">
                            <img src="../2.0/img/user_img.PNG" style="width: 45px; height: 45px; border-radius: 50%;">
                            
                            <div style="flex: 1;">
                                <textarea name="comment_content" rows="3" placeholder="詢問一下食物狀況，或說聲謝謝..." 
                                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 15px; resize: vertical; font-family: inherit;"></textarea>
                                <div style="text-align: right; margin-top: 10px;">
                                    <button type="submit" name="submit_comment" 
                                        style="background-color: rgb(141,168,163); color: white; border: none; padding: 8px 20px; border-radius: 5px; cursor: pointer; font-size: 14px;">
                                        發送留言
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                <?php else: ?>
                    <div style="text-align: center; padding: 20px; background: #f9f9f9; border-radius: 5px; color: #666;">
                        請 <a href="login.php" style="color: #ff9800; text-decoration: none; font-weight: bold;">登入</a> 後參與討論
                    </div>
                <?php endif; ?>
            </div>

            <div class="comment-list">
                <?php if (count($comment_list) > 0): ?>
                    <?php foreach ($comment_list as $c): ?>
                        <div style="display: flex; gap: 15px; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 20px;">
                            
                            <img src="../2.0/img/user_img.PNG" 
                                style="width: 45px; height: 45px; border-radius: 50%; object-fit: cover;">
                            
                            <div style="flex: 1;">
                                <div style="margin-bottom: 5px;">
                                    <span style="font-weight: bold; color: #333; margin-right: 10px;">
                                        <?php echo htmlspecialchars($c['name']); ?>
                                    </span>
                                    <span style="font-size: 12px; color: #999;">
                                        <?php echo $c['created_at']; ?>
                                    </span>
                                </div>
                                
                                <div style="color: #555; line-height: 1.6; font-size: 15px; word-break: break-all;">
                                    <?php echo nl2br(htmlspecialchars($c['content'])); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="text-align: center; color: #999; margin-top: 30px;">目前還沒有人留言，快來搶頭香！</p>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <footer>
        <div class="space" style="width: 100px;"> &nbsp </div>
        <div class="all">
            <div class="smthing">
                <h5>
                    服務時段<br>
                    週一至週五 10:00 a.m. – 5 p.m.<br>
                    如有任何問題歡迎與我們聯繫
                </h5>
            </div>

            <div class="information">
                <h2><b>INFORMATION</b></h2>
                <p>
                    聯絡電話<br>
                    (08) 766-3800<br><br>
                    客服信箱<br>
                    ptdola@mail.nptu.edu.tw<br><br>
                    公司位置<br>
                    900391 屏東市林森路 1 號 (五育樓 B1 西側)
                </p>
            </div>
            <div class="links">
                <h2>LINK</h2><br>
                <p>
                    <a href="https://greenmedia.today/map_search.php" target="_blank"> 食物地圖 </a><br>
                    <a href="https://www.foodbank-taiwan.org.tw/" target="_blank"> 營養傳愛 </a><br>
                    <a href="https://icook.tw/" target="_blank"> 廚房魔法師：食材不浪費 </a><br>
                    <a href="https://www.twvns.org/info/recipe" target="_blank"> 健康蔬食新選擇 </a>
                </p>
            </div>
            <div class="social">
                <h2>SOCIAL</h2><br>
                <!-- Social Media Icon Set Made With NiftyButtons.com -->
                <div class="social-icons" style="display: flex; gap: 8px; flex-wrap: wrap; justify-content: center;">
                    <a href="#" target="_blank" rel="noopener" style="display: inline-flex; align-items: center; justify-content: center; width: 50px; height: 50px; padding: 9px; background: rgba(134, 157, 157, 1); border-radius: 15px; color: #d8d8cd; transition: all 0.3s ease; text-decoration: none;" onmouseover="this.style.transform='rotate(360deg)'; " onmouseout="this.style.transform=''; this.style.filter=''; this.style.animation='';">
                        <svg class="niftybutton-facebook" data-donate="true" data-tag="fac" data-name="Facebook" viewBox="0 0 512 512" preserveAspectRatio="xMidYMid meet" width="50px" height="50px" style="width: 50px; height: 50px; display: block; fill: #d8d8cd;"><title>Facebook social icon</title>
                            <path d="M211.9 197.4h-36.7v59.9h36.7V433.1h70.5V256.5h49.2l5.2-59.1h-54.4c0 0 0-22.1 0-33.7 0-13.9 2.8-19.5 16.3-19.5 10.9 0 38.2 0 38.2 0V82.9c0 0-40.2 0-48.8 0 -52.5 0-76.1 23.1-76.1 67.3C211.9 188.8 211.9 197.4 211.9 197.4z" fill="#d8d8cd"></path>
                        </svg>
                    </a>
                    <a href="#" target="_blank" rel="noopener" style="display: inline-flex; align-items: center; justify-content: center; width: 50px; height: 50px; padding: 9px; background: rgba(134, 157, 157, 1); border-radius: 15px; color: #d8d8cd; transition: all 0.3s ease; text-decoration: none;" onmouseover="this.style.transform='rotate(360deg)'; " onmouseout="this.style.transform=''; this.style.filter=''; this.style.animation='';">
                        <svg class="niftybutton-instagram" data-donate="true" data-tag="ins" data-name="Instagram" viewBox="0 0 512 512" preserveAspectRatio="xMidYMid meet" width="50px" height="50px" style="width: 50px; height: 50px; display: block; fill: #d8d8cd;"><title>Instagram social icon</title>
                            <path d="M256 109.3c47.8 0 53.4 0.2 72.3 1 17.4 0.8 26.9 3.7 33.2 6.2 8.4 3.2 14.3 7.1 20.6 13.4 6.3 6.3 10.1 12.2 13.4 20.6 2.5 6.3 5.4 15.8 6.2 33.2 0.9 18.9 1 24.5 1 72.3s-0.2 53.4-1 72.3c-0.8 17.4-3.7 26.9-6.2 33.2 -3.2 8.4-7.1 14.3-13.4 20.6 -6.3 6.3-12.2 10.1-20.6 13.4 -6.3 2.5-15.8 5.4-33.2 6.2 -18.9 0.9-24.5 1-72.3 1s-53.4-0.2-72.3-1c-17.4-0.8-26.9-3.7-33.2-6.2 -8.4-3.2-14.3-7.1-20.6-13.4 -6.3-6.3-10.1-12.2-13.4-20.6 -2.5-6.3-5.4-15.8-6.2-33.2 -0.9-18.9-1-24.5-1-72.3s0.2-53.4 1-72.3c0.8-17.4 3.7-26.9 6.2-33.2 3.2-8.4 7.1-14.3 13.4-20.6 6.3-6.3 12.2-10.1 20.6-13.4 6.3-2.5 15.8-5.4 33.2-6.2C202.6 109.5 208.2 109.3 256 109.3M256 77.1c-48.6 0-54.7 0.2-73.8 1.1 -19 0.9-32.1 3.9-43.4 8.3 -11.8 4.6-21.7 10.7-31.7 20.6 -9.9 9.9-16.1 19.9-20.6 31.7 -4.4 11.4-7.4 24.4-8.3 43.4 -0.9 19.1-1.1 25.2-1.1 73.8 0 48.6 0.2 54.7 1.1 73.8 0.9 19 3.9 32.1 8.3 43.4 4.6 11.8 10.7 21.7 20.6 31.7 9.9 9.9 19.9 16.1 31.7 20.6 11.4 4.4 24.4 7.4 43.4 8.3 19.1 0.9 25.2 1.1 73.8 1.1s54.7-0.2 73.8-1.1c19-0.9 32.1-3.9 43.4-8.3 11.8-4.6 21.7-10.7 31.7-20.6 9.9-9.9 16.1-19.9 20.6-31.7 4.4-11.4 7.4-24.4 8.3-43.4 0.9-19.1 1.1-25.2 1.1-73.8s-0.2-54.7-1.1-73.8c-0.9-19-3.9-32.1-8.3-43.4 -4.6-11.8-10.7-21.7-20.6-31.7 -9.9-9.9-19.9-16.1-31.7-20.6 -11.4-4.4-24.4-7.4-43.4-8.3C310.7 77.3 304.6 77.1 256 77.1L256 77.1z" fill="#d8d8cd"></path>
                            <path d="M256 164.1c-50.7 0-91.9 41.1-91.9 91.9s41.1 91.9 91.9 91.9 91.9-41.1 91.9-91.9S306.7 164.1 256 164.1zM256 315.6c-32.9 0-59.6-26.7-59.6-59.6s26.7-59.6 59.6-59.6 59.6 26.7 59.6 59.6S288.9 315.6 256 315.6z" fill="#d8d8cd"></path>
                            <circle cx="351.5" cy="160.5" r="21.5" fill="#d8d8cd"></circle>
                        </svg>
                    </a>
                    <a href="#" target="_blank" rel="noopener" style="display: inline-flex; align-items: center; justify-content: center; width: 50px; height: 50px; padding: 9px; background: rgba(134, 157, 157, 1); border-radius: 15px; color: #d8d8cd; transition: all 0.3s ease; text-decoration: none;" onmouseover="this.style.transform='rotate(360deg)'; " onmouseout="this.style.transform=''; this.style.filter=''; this.style.animation='';">
                        <svg class="niftybutton-lne" data-donate="true" data-tag="lne" data-name="Line" preserveAspectRatio="xMidYMid meet" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50" width="50px" height="50px" style="width: 50px; height: 50px; display: block; fill: #d8d8cd;"><title>Line icon</title>
                            <path d="M 9 4 C 6.24 4 4 6.24 4 9 L 4 41 C 4 43.76 6.24 46 9 46 L 41 46 C 43.76 46 46 43.76 46 41 L 46 9 C 46 6.24 43.76 4 41 4 L 9 4 z M 25 11 C 33.27 11 40 16.359219 40 22.949219 C 40 25.579219 38.959297 27.960781 36.779297 30.300781 C 35.209297 32.080781 32.660547 34.040156 30.310547 35.660156 C 27.960547 37.260156 25.8 38.519609 25 38.849609 C 24.68 38.979609 24.44 39.039062 24.25 39.039062 C 23.59 39.039062 23.649219 38.340781 23.699219 38.050781 C 23.739219 37.830781 23.919922 36.789063 23.919922 36.789062 C 23.969922 36.419063 24.019141 35.830937 23.869141 35.460938 C 23.699141 35.050938 23.029062 34.840234 22.539062 34.740234 C 15.339063 33.800234 10 28.849219 10 22.949219 C 10 16.359219 16.73 11 25 11 z M 23.992188 18.998047 C 23.488379 19.007393 23 19.391875 23 20 L 23 26 C 23 26.552 23.448 27 24 27 C 24.552 27 25 26.552 25 26 L 25 23.121094 L 27.185547 26.580078 C 27.751547 27.372078 29 26.973 29 26 L 29 20 C 29 19.448 28.552 19 28 19 C 27.448 19 27 19.448 27 20 L 27 23 L 24.814453 19.419922 C 24.602203 19.122922 24.294473 18.992439 23.992188 18.998047 z M 15 19 C 14.448 19 14 19.448 14 20 L 14 26 C 14 26.552 14.448 27 15 27 L 18 27 C 18.552 27 19 26.552 19 26 C 19 25.448 18.552 25 18 25 L 16 25 L 16 20 C 16 19.448 15.552 19 15 19 z M 21 19 C 20.448 19 20 19.448 20 20 L 20 26 C 20 26.552 20.448 27 21 27 C 21.552 27 22 26.552 22 26 L 22 20 C 22 19.448 21.552 19 21 19 z M 31 19 C 30.448 19 30 19.448 30 20 L 30 26 C 30 26.552 30.448 27 31 27 L 34 27 C 34.552 27 35 26.552 35 26 C 35 25.448 34.552 25 34 25 L 32 25 L 32 24 L 34 24 C 34.553 24 35 23.552 35 23 C 35 22.448 34.553 22 34 22 L 32 22 L 32 21 L 34 21 C 34.552 21 35 20.552 35 20 C 35 19.448 34.552 19 34 19 L 31 19 z" fill="#d8d8cd"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // 取得元素
        const modal = document.getElementById('reserveModal');
        const btnOpen = document.getElementById('btn-open-modal');
        const btnCancel = document.getElementById('btn-cancel');
        const btnConfirm = document.getElementById('btn-confirm');

        // 1. 打開 Modal
        btnOpen.addEventListener('click', function() {
            // 如果已經預約過，就不再打開
            if (this.innerText === '預約中...') return;
            modal.classList.add('show');
        });

        // 2. 關閉 Modal (我再想想)
        btnCancel.addEventListener('click', function() {
            modal.classList.remove('show');
        });

        // 3. 點擊背景也能關閉
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.classList.remove('show');
            }
        });

        // 4. 按下確定 (發送請求給後端)
        btnConfirm.addEventListener('click', function() {
            // 取得資料
            const foodId = btnOpen.dataset.foodId;
            const publisherId = btnOpen.dataset.publisherId;

            // 變更按鈕狀態避免重複點擊
            const originalText = btnConfirm.innerText;
            btnConfirm.innerText = "處理中...";
            btnConfirm.disabled = true;

            // 使用 Fetch API 發送請求
            fetch('api_reserve.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `food_id=${foodId}&publisher_id=${publisherId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // 成功：關閉視窗，改按鈕文字
                    modal.classList.remove('show');
                    btnOpen.innerText = "預約中";
                    btnOpen.style.backgroundColor = "#ccc"; // 變灰色
                    btnOpen.disabled = true;
                    alert("預約成功！已通知發布者。");
                } else {
                    // 失敗
                    alert(data.message || "預約失敗，請稍後再試。");
                    // 復原按鈕
                    btnConfirm.innerText = originalText;
                    btnConfirm.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert("發生錯誤，請檢查網路連線。");
                btnConfirm.innerText = originalText;
                btnConfirm.disabled = false;
            });
        });
    </script>
</body>
</html>