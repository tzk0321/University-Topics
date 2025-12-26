<?php
// api_reserve.php
header('Content-Type: application/json');
session_start();

// 1. 檢查是否登入
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => '請先登入會員']);
    exit;
}

$my_id = $_SESSION['user_id'];
$food_id = $_POST['food_id'] ?? null;
$publisher_id = $_POST['publisher_id'] ?? null;

if (!$food_id || !$publisher_id) {
    echo json_encode(['success' => false, 'message' => '參數錯誤']);
    exit;
}

// 2. 連線資料庫 (設定已改成你的)
$host = 'localhost';
$db_name = 'mydatabase'; 
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ==========================================
    // (A) ★★★ 關鍵修正：檢查是否重複預約 ★★★
    // ==========================================
    $check = $pdo->prepare("SELECT id FROM reservations WHERE food_id = ? AND requester_id = ? AND status IN ('pending', 'confirmed')");
    $check->execute([$food_id, $my_id]);
    
    if ($check->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => '您已經預約過此商品，請等待發布者確認。']);
        exit;
    }

    // ==========================================
    // (B) ★★★ 關鍵修正：寫入預約紀錄 (reservations) ★★★
    // 這段是你原本漏掉的！沒有這段，r&s.php 就抓不到資料
    // ==========================================
    $sql = "INSERT INTO reservations (food_id, requester_id, publisher_id, status, created_at) VALUES (?, ?, ?, 'pending', NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$food_id, $my_id, $publisher_id]);

    // ==========================================
    // (C) 發送通知給發布者 (Notifications)
    // ==========================================
    $msg = "有人預約了您的食物 (ID: $food_id)，請前往「我的預約/分享」進行審核。";
    $stmt_notif = $pdo->prepare("INSERT INTO notifications (user_id, message, created_at, is_read) VALUES (?, ?, NOW(), 0)");
    $stmt_notif->execute([$publisher_id, $msg]);

    // 5. 回傳成功
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => '系統錯誤：' . $e->getMessage()]);
}
?>