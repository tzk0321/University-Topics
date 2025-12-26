<?php
// uploadif.php - 處理表單上傳 (對應資料庫結構版)
session_start();

// 1. 資料庫連線
$host = 'localhost';
$db_name = 'mydatabase'; 
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("資料庫連線失敗: " . $e->getMessage());
}

// 2. 檢查登入
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('請先登入會員！'); window.location.href='member.php';</script>";
    exit;
}

// 3. 處理表單提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {
        // --- (A) 接收並整理資料 ---
        
        $user_id = $_SESSION['user_id']; // 對應 user_id
        
        $food_name = $_POST['food-name'];
        $quantity  = $_POST['food-number'];
        $unit      = $_POST['food-unit'];
        $category  = $_POST['food-class'] ?? '其他'; 
        $expiry    = $_POST['event-datetime1']; // 對應 expiry_datetime
        
        // 地點
        $city      = $_POST['place1']; // 對應 pickup_address_city
        $street    = $_POST['place2']; // 對應 pickup_address_street
        $landmark  = $_POST['place3']; // 對應 pickup_landmark
        
        // 詳細資訊
        $origin    = $_POST['origin'];    // 對應 origin
        $storage   = $_POST['selected'];  // 對應 storage_method
        
        // ★ 特別處理：因為資料庫沒有 food_condition 欄位
        // 我們把「狀態(未開封)」和「備註」合併存進 remark 欄位
        $condition_input = $_POST['state'] ?? '未開封';
        $remark_input    = $_POST['remark'];
        $final_remark    = "狀態：" . $condition_input . "。備註：" . $remark_input; // 對應 remark

        // --- (B) 處理圖片上傳 ---
        $image_filename = ""; 

        if (isset($_FILES['imageFile']) && $_FILES['imageFile']['error'] === 0) {
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_ext = pathinfo($_FILES['imageFile']['name'], PATHINFO_EXTENSION);
            $new_filename = time() . '_' . rand(1000, 9999) . '.' . $file_ext;
            $target_file = $upload_dir . $new_filename;

            if (move_uploaded_file($_FILES['imageFile']['tmp_name'], $target_file)) {
                $image_filename = $new_filename;
            } else {
                throw new Exception("圖片上傳失敗。");
            }
        } else {
            throw new Exception("請務必上傳一張餐點照片。");
        }

        // --- (C) 寫入資料庫 ---
        $pdo->beginTransaction();

        // 1. 寫入 food_items (完全依照你的資料庫欄位)
        $sql = "INSERT INTO food_items 
                (user_id, food_name, quantity, unit, category, 
                 pickup_address_city, pickup_address_street, pickup_landmark, 
                 origin, storage_method, remark, 
                 expiry_datetime, image_filename, item_state, created_at) 
                VALUES 
                (?, ?, ?, ?, ?, 
                 ?, ?, ?, 
                 ?, ?, ?, 
                 ?, ?, '上架中', NOW())";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $user_id, $food_name, $quantity, $unit, $category,
            $city, $street, $landmark,
            $origin, $storage, $final_remark,
            $expiry, $image_filename
        ]);
        
        $food_id = $pdo->lastInsertId();

        // 2. 處理標籤 (food_items_label)
        // 確保你的 HTML name 是 food-label[] (陣列)
        if (isset($_POST['food-label'])) {
            $labels = $_POST['food-label'];
            if (!is_array($labels)) $labels = [$labels]; 

            $stmt_find = $pdo->prepare("SELECT label_id FROM labels WHERE label_name = ?");
            $stmt_insert = $pdo->prepare("INSERT INTO food_items_label (food_id, label_id) VALUES (?, ?)");

            foreach ($labels as $label_name) {
                $stmt_find->execute([$label_name]);
                $row = $stmt_find->fetch();
                if ($row) {
                    $stmt_insert->execute([$food_id, $row['label_id']]);
                }
            }
        }

        $pdo->commit();
        echo "<script>alert('餐點上架成功！'); window.location.href='food.php';</script>";

    } catch (Exception $e) {
        $pdo->rollBack();
        echo "<script>alert('上架失敗：" . addslashes($e->getMessage()) . "'); history.back();</script>";
    }

} else {
    echo "請透過表單提交。";
}
?>