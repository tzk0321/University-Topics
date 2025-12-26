<?php
session_start();
// 1. 連線資料庫
$host = 'localhost';
$db_name = 'mydatabase'; 
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. 設定按鈕
    $categories = [
        '全部' => '🍽️',
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

    // 3. 接收參數 (預設 '全部')
    $current_category = $_GET['category'] ?? '全部';

    // 4. SQL 查詢
    if ($current_category === '全部') {
        $sql = "SELECT * FROM food_items 
                WHERE expiry_datetime > NOW() 
                ORDER BY expiry_datetime ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
    } else {
        $sql = "SELECT food_items.* FROM food_items 
                JOIN food_items_label ON food_items.food_id = food_items_label.food_id
                JOIN labels ON food_items_label.label_id = labels.label_id
                WHERE labels.label_name = ? 
                AND food_items.expiry_datetime > NOW() 
                ORDER BY food_items.expiry_datetime ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$current_category]);
    }

    $foods = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "資料庫連線失敗: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>美味佳餚 | food sharing</title>
    <link rel="shortcut icon" href="img/favorites.png" type="image/x-icon">
    <link rel="stylesheet" href="css/r&s.css"> 
    
    <style>
        /* --- 頁面佈局 (關鍵修改) --- */
        .main-content {
            margin-top: 150px;
            margin-bottom: 50px;
            
            /* ★ 關鍵設定：強制寬度 = 4個卡片(240*4) + 3個間距(75*3) = 1015px */
            width: 1125px; 
            
            /* 讓這個固定寬度的區塊在畫面中置中 */
            margin-left: auto;
            margin-right: auto;
            
            /* 確保內容不會溢出 */
            max-width: 95%; 
        }

        /* --- 1. 類別按鈕列 --- */
        .category-bar {
            display: flex;
            justify-content: flex-start; /* 靠左排列 */
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 30px;
            width: 100%; /* 撐滿 main-content 的寬度 (也就是1015px) */
        }

        .cat-btn {
            text-decoration: none;
            color: #555;
            background-color: #fff;
            padding: 10px 22px;
            border-radius: 50px;
            border: 1px solid #ddd;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
            font-weight: 600;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .cat-btn:hover, .cat-btn.active {
            background-color: rgb(141,168,163);
            color: white;
            border-color: rgb(141,168,163);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(141,168,163, 0.3);
        }

        hr {
            border: 0;
            border-top: 3px solid #eee;
            margin: 30px 0;
            width: 100%; /* 線條也會跟按鈕、圖片一樣寬 */
        }

        /* --- 2. 食物卡片區塊 --- */
        .result-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start; /* 靠左排列 */
            
            /* ★ 這裡的間距 45px 必須跟上面的寬度計算吻合 */
            gap: 75px; 
            
            padding-bottom: 80px;
            width: 100%;
        }

        .food-item {
            /* 固定尺寸 */
            height: 315px;
            width: 225px;
            
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

        /* 漸層遮罩 */
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

        /* 資訊文字區 */
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

        .no-data {
            text-align: left;
            padding: 50px 0;
            color: #888;
            font-size: 18px;
            width: 100%;
        }

        /* RWD 響應式：當螢幕變小時，自動縮減寬度以維持置中 */
        
        /* 變為 3 欄: (220*3) + (45*2) = 750px */
        @media (max-width: 1050px) {
            .main-content { width: 750px; }
        }

        /* 變為 2 欄: (220*2) + (45*1) = 485px */
        @media (max-width: 780px) {
            .main-content { width: 485px; }
        }

        /* 手機版：滿版置中 */
        @media (max-width: 520px) {
            .main-content { width: 100%; display: flex; flex-direction: column; align-items: center; }
            .result-list { justify-content: center; }
            .category-bar { justify-content: center; }
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

    <div class="main-content">
        
        <div class="category-bar" style="margin-bottom: 30px;">
            <?php foreach ($categories as $name => $icon): ?>
                <a href="?category=<?php echo urlencode($name); ?>" 
                   class="cat-btn <?php echo ($current_category == $name) ? 'active' : ''; ?>">
                    <span><?php echo $icon; ?></span>
                    <span><?php echo $name; ?></span>
                </a>
            <?php endforeach; ?>
        </div>

        <hr>

        <div class="result-list" style="margin-top: 30px;">
            <?php if (count($foods) > 0): ?>
                <?php foreach ($foods as $row): ?>
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
                                    份數：<?php echo htmlspecialchars($row['quantity']); ?> <?php echo htmlspecialchars($row['unit']); ?><br>
                                    地點：<?php echo htmlspecialchars($row['pickup_address_city']); ?><br>
                                    有效日期：<?php echo date('m/d H:i', strtotime($row['expiry_datetime'])); ?>
                                    
                                </p>
                                <span class="btn">查看更多</span>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-data">
                    <div style="font-size: 40px; margin-bottom: 10px;">📦</div>
                    目前「<?php echo htmlspecialchars($current_category); ?>」類別沒有可領取的食物喔！
                </div>
            <?php endif; ?>
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
</body>
</html>