<?php
$host = 'localhost';
$db   = 'mydatabase'; // 資料庫名稱
$user = 'root';               // 帳號
$pass = '';                   // 密碼
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("資料庫連線失敗: " . $e->getMessage());
}

// 2. 接收搜尋關鍵字
$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';

$results = []; // 預設結果為空陣列

// 3. 如果有關鍵字，才去查詢資料庫
if ($search_query !== '') {
    // 同時搜尋 food_name, category, pickup_address_city
    $sql = "SELECT * FROM food_items WHERE (food_name LIKE ? OR category LIKE ? OR pickup_address_city LIKE ?)
             AND expiry_datetime > NOW()
             ORDER BY expiry_datetime ASC";
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute(["%$search_query%", "%$search_query%", "%$search_query%"]);
    $results = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>搜尋結果 | food sharing</title>
    <link rel="shortcut icon" href="img/favorites.png" type="image/x-icon">
    <link rel="stylesheet" href="../2.0/css/r&S.css">
    
    <style>
        /* 1. 外層容器設定固定寬度 */
        .main-container {
            margin-top: 120px;
            margin-bottom: 50px;
            /* 4個卡片(220*4) + 3個間隙(45*3) = 1015px */
            width: 1125px; 
            margin-left: auto;
            margin-right: auto;
            max-width: 95%; 
        }

        /* 2. 標題區塊 */
        .search-header {
            margin-bottom: 30px;
            padding-left: 5px;
        }
        
        .search-header h1 {
            font-size: 24px;
            color: #333;
            margin-bottom: 10px;
        }

        .search-header p {
            color: #666;
            font-size: 16px;
        }

        /* 3. 列表區塊 (Flex 靠左) */
        .result-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start; /* 靠左排列 */
            gap: 75px; /* 卡片間距 45px */
            padding-bottom: 80px;
            width: 100%;
        }

        /* 4. 卡片本體 */
        .food-item {
            height: 305px;
            width: 220px;
            mix-blend-mode: normal; 
            border-radius: 16px;
            overflow: hidden;
            position: relative;
            transition: transform 0.5s, box-shadow 0.3s;
            cursor: pointer;
            background: #fff;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            flex-shrink: 0;
        }
        
        .food-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(0,0,0,0.15);
        }

        /* 5. 連結與圖片 */
        .food-item a {
            text-decoration: none;
            display: block;
            width: 100%;
            height: 100%;
        }

        .img-card {
            width: 100%;
            height: 100%;
        }

        .food-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            pointer-events: none;
            transition: transform 0.7s; 
        }

        .food-item:hover img {
            transform: scale(1.1);
        }

        /* 6. 漸層遮罩 */
        .food-item .layer {
            background: linear-gradient(to top, rgba(0, 0, 0, 0.9) 0%, rgba(0, 0, 0, 0.4) 50%, rgba(0, 0, 0, 0) 100%);
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 0.4s;
        }
        
        .food-item:hover .layer {
            opacity: 1;
        }

        /* 7. 資訊文字區 */
        .info {
            position: absolute;
            bottom: -60%; 
            left: 0;
            width: 100%;
            padding: 15px; 
            box-sizing: border-box; 
            opacity: 0;
            color: white;
            z-index: 2;
            transition: bottom 0.5s cubic-bezier(0.19, 1, 0.22, 1), opacity 0.5s;
            text-align: left;
        }

        .food-item:hover .info {
            bottom: 0;
            opacity: 1;
        }

        .info h4 {
            margin: 0 0 8px 0;
            font-size: 18px;
            letter-spacing: 1px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.5);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .info p {
            font-size: 14px; 
            margin: 0; 
            line-height: 1.5;
            text-shadow: 0 1px 2px rgba(0,0,0,0.5);
        }

        .btn {
            background: rgb(141,168,163);
            color: rgb(233,230,225);
            border: none;
            padding: 6px 15px;
            font-weight: bold;
            border-radius: 8px;
            margin-top: 10px;
            cursor: pointer;
            display: inline-block;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            font-size: 13px;
        }
        
        .btn:hover {
            background: rgb(108, 155, 155);
        }

        /* 無結果樣式 */
        .no-result {
            width: 100%;
            text-align: center;
            padding: 50px 0;
            color: #888;
            font-size: 18px;
        }

        /* RWD 響應式 */
        @media (max-width: 1050px) { .main-container { width: 750px; } }
        @media (max-width: 780px) { .main-container { width: 485px; } }
        @media (max-width: 520px) {
            .main-container { width: 100%; display: flex; flex-direction: column; align-items: center; }
            .result-list { justify-content: center; }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="main-container">
        
        <div class="search-header">
            <?php if ($search_query !== ''): ?>
                <h1>搜尋 "<?php echo htmlspecialchars($search_query); ?>" 的結果</h1>
                <p>共找到 <?php echo count($results); ?> 筆資料</p>
            <?php else: ?>
                <h1>搜尋結果</h1>
                <p>請輸入關鍵字進行搜尋</p>
            <?php endif; ?>
        </div>

        <div class="result-list">
            <?php if (count($results) > 0): ?>
                <?php foreach ($results as $row): ?>
                    <div class="food-item">
                        <a href="detail.php?id=<?php echo $row['food_id']; ?>">
                            <div class="img-card">
                                <?php if (!empty($row['image_filename'])): ?>
                                    <img src="uploads/<?php echo htmlspecialchars($row['image_filename']); ?>" 
                                         alt="<?php echo htmlspecialchars($row['food_name']); ?>">
                                <?php else: ?>
                                    <div style="width:100%; height:100%; background:#eee; display:flex; align-items:center; justify-content:center; color:#999;">無圖片</div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="layer"></div>
                            
                            <div class="info">
                                <h4><b><?php echo htmlspecialchars($row['food_name']); ?></b></h4>
                                <p>
                                    地點：<?php echo htmlspecialchars($row['pickup_address_city']); ?><br>
                                    有效日期：<?php echo date('m/d H:i', strtotime($row['expiry_datetime'])); ?><br>
                                    份數：<?php echo htmlspecialchars($row['quantity']); ?> 份
                                </p>
                                <span class="btn">查看更多</span>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>

            <?php else: ?>
                <div class="no-result">
                    <div style="font-size: 40px; margin-bottom: 10px;">📦</div>
                    <p>抱歉，找不到與 "<?php echo htmlspecialchars($search_query); ?>" 相關的結果。</p>
                    <p>建議您嘗試其他關鍵字。</p>
                </div>
            <?php endif; ?>
        </div>
        
    </main>

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
</body>
</html>