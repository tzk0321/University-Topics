<?php
// api_respond.php
// 用途：處理發布者按下「同意」或「婉拒」後的邏輯

header('Content-Type: application/json');
session_start();

// 1. 檢查登入與參數
if (!isset($_SESSION['user_id']) || !isset($_POST['id']) || !isset($_POST['action'])) {
    echo json_encode(['success' => false, 'message' => '無效的請求或參數不足']);
    exit;
}

$my_id = $_SESSION['user_id'];
$res_id = $_POST['id'];        // 預約單的 ID (reservations table id)
$action = $_POST['action'];    // 'confirmed' 或 'rejected'

// 2. 連線資料庫 (使用你的設定)
$host = 'localhost';
$db_name = 'mydatabase'; 
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 3. 安全性檢查：確認這筆預約真的是這個發布者 (我) 的
    // 避免有人亂改 ID 偷改別人的預約狀態
    $stmt = $pdo->prepare("SELECT * FROM reservations WHERE id = ? AND publisher_id = ?");
    $stmt->execute([$res_id, $my_id]);
    $reservation = $stmt->fetch();

    if (!$reservation) {
        echo json_encode(['success' => false, 'message' => '找不到這筆預約，或您無權限操作']);
        exit;
    }

    // 4. 更新預約狀態 (pending -> confirmed 或 rejected)
    $stmt_update = $pdo->prepare("UPDATE reservations SET status = ? WHERE id = ?");
    $stmt_update->execute([$action, $res_id]);

    // 5. 根據動作做後續處理
    if ($action === 'confirmed') {
        // (A) 如果同意：把食物狀態改成「已預訂」，避免其他人再預約
        $pdo->prepare("UPDATE food_items SET item_state = '已預訂' WHERE food_id = ?")
            ->execute([$reservation['food_id']]);

        // (B) 通知預約者 (Requester)
        $msg = "恭喜！發布者同意了您的預約 (訂單 #$res_id)，請準時前往領取。";
        $pdo->prepare("INSERT INTO notifications (user_id, message, created_at, is_read) VALUES (?, ?, NOW(), 0)")
            ->execute([$reservation['requester_id'], $msg]);

        // (C) 選項：如果有其他人也預約同一個食物，可以考慮自動拒絕他們 (這裡先不做，保留彈性)
    } 
    else if ($action === 'rejected') {
        // 如果拒絕：通知預約者被拒絕
        $msg = "很遺憾，發布者婉拒了您的預約 (訂單 #$res_id)。";
        $pdo->prepare("INSERT INTO notifications (user_id, message, created_at, is_read) VALUES (?, ?, NOW(), 0)")
            ->execute([$reservation['requester_id'], $msg]);
    }

    // 6. 回傳成功
    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => '資料庫錯誤：' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => '系統錯誤：' . $e->getMessage()]);
}
?>