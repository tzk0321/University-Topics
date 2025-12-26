<?php
// --------------------------------------------------
// r&s.php - 主頁面與資料處理
// --------------------------------------------------

// 1. 開啟 Session 並檢查登入
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 檢查是否登入
if (!isset($_SESSION['is_login']) || $_SESSION['is_login'] !== TRUE) {
    echo "<script>alert('尚未登入'); window.location.href='login.php';</script>";
    exit();
}

$my_id = $_SESSION['user_id'];

// 2. 連線資料庫
// 請確認 db.php 存在，或直接使用下方的連線程式碼
// require_once 'db.php'; 
$host = 'localhost';
$db_name = 'mydatabase'; 
$username = 'root';
$password = '';

try {
    $dsn = "mysql:host=$host;dbname=$db_name;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // =============================================
    // (A) 查詢「預約紀錄」 (我向別人預約的)
    // =============================================
    $sql_my_res = "SELECT r.*, f.food_name, f.image_filename, u.name AS publisher_name 
                   FROM reservations r
                   JOIN food_items f ON r.food_id = f.food_id
                   JOIN users u ON r.publisher_id = u.id
                   WHERE r.requester_id = ?
                   ORDER BY r.created_at DESC";
    $stmt1 = $pdo->prepare($sql_my_res);
    $stmt1->execute([$my_id]);
    $my_reservations = $stmt1->fetchAll(PDO::FETCH_ASSOC);

    // =============================================
    // (B) 查詢「分享紀錄」 (別人向我預約的 - 需審核)
    // =============================================
    $sql_my_share = "SELECT r.*, f.food_name, f.image_filename, u.name AS requester_name 
                     FROM reservations r
                     JOIN food_items f ON r.food_id = f.food_id
                     JOIN users u ON r.requester_id = u.id
                     WHERE r.publisher_id = ?
                     ORDER BY FIELD(r.status, 'pending', 'confirmed', 'rejected'), r.created_at DESC";
    $stmt2 = $pdo->prepare($sql_my_share);
    $stmt2->execute([$my_id]);
    $incoming_requests = $stmt2->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "資料庫連線失敗: " . $e->getMessage();
    exit;
}

// 狀態對應表 (給 PHP 輸出 HTML 用)
$status_map = ['pending' => '待確認', 'confirmed' => '預約成功', 'rejected' => '已婉拒'];
$color_map = ['pending' => '#ff9800', 'confirmed' => '#4CAF50', 'rejected' => '#F44336'];
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>我的預約/分享 | food sharing</title>
    <link rel="shortcut icon" href="img/favorites.png" type="image/x-icon">
    
    <link rel="stylesheet" href="../2.0/css/r&s.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <article>
        <div class="reserve-record">
            <h1>預約紀錄</h1>
            <div id="product-container-1" class="container">
                <?php if (count($my_reservations) > 0): ?>
                    <?php foreach ($my_reservations as $r): ?>
                        <div class="record-item">
                            <div class="product_title">
                                <span><?php echo htmlspecialchars($r['food_name']); ?></span>
                                <span class="status-badge" style="background-color: <?php echo $color_map[$r['status']]; ?>;">
                                    <?php echo $status_map[$r['status']]; ?>
                                </span>
                            </div>
                            <div class="product_details">
                                <div class="detail-row">
                                    <img src="uploads/<?php echo htmlspecialchars($r['image_filename']); ?>" class="food-img">
                                    <div class="info-text">
                                        <p><strong>發布者：</strong><?php echo htmlspecialchars($r['publisher_name']); ?></p>
                                        <p><strong>預約時間：</strong><?php echo $r['created_at']; ?></p>
                                        <?php if ($r['status'] == 'confirmed'): ?>
                                            <p style="color: #4CAF50; font-weight: bold;">★ 對方已同意！請準時前往面交。</p>
                                        <?php elseif ($r['status'] == 'rejected'): ?>
                                            <p style="color: #F44336;">很遺憾，對方婉拒了您的預約。</p>
                                        <?php else: ?>
                                            <p style="color: #ff9800;">等待對方確認中...</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="padding: 20px; text-align: center; color: #777; background: #fff;">目前沒有預約紀錄</div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="share-record">
            <h1>分享紀錄</h1>
            <div id="product-container-2" class="container">
                <?php if (count($incoming_requests) > 0): ?>
                    <?php foreach ($incoming_requests as $req): ?>
                        <div class="record-item" id="req-row-<?php echo $req['id']; ?>">
                            <div class="product_title">
                                <span><?php echo htmlspecialchars($req['food_name']); ?> - 來自 <?php echo htmlspecialchars($req['requester_name']); ?></span>
                                <span class="status-badge" id="status-badge-<?php echo $req['id']; ?>" style="background-color: <?php echo $color_map[$req['status']]; ?>;">
                                    <?php echo $status_map[$req['status']]; ?>
                                </span>
                            </div>
                            <div class="product_details">
                                <div class="detail-row">
                                    <img src="uploads/<?php echo htmlspecialchars($req['image_filename']); ?>" class="food-img">
                                    <div class="info-text" style="flex: 1;">
                                        <p><strong>預約者：</strong><?php echo htmlspecialchars($req['requester_name']); ?></p>
                                        <p><strong>申請時間：</strong><?php echo $req['created_at']; ?></p>
                                        
                                        <div id="action-area-<?php echo $req['id']; ?>" style="margin-top: 15px;">
                                            <?php if ($req['status'] == 'pending'): ?>
                                                <button class="btn btn-accept" onclick="respond(<?php echo $req['id']; ?>, 'confirmed')">同意預約</button>
                                                <button class="btn btn-reject" onclick="respond(<?php echo $req['id']; ?>, 'rejected')">婉拒</button>
                                            <?php else: ?>
                                                <p style="color: #777;">已處理 (<?php echo $status_map[$req['status']]; ?>)</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="padding: 20px; text-align: center; color: #777; background: #fff;">目前沒有分享紀錄</div>
                <?php endif; ?>
            </div>
        </div>
    </article>

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
                <div class="social-icons" style="display: flex; gap: 8px; flex-wrap: nowrap; justify-content: center;">
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

    <script src="r&s.js"></script>
</body>
</html>