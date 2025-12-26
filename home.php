<?php
// 引入 Header (它裡面已經包含 session_start 和 資料庫連線 $pdo 了)
require_once 'header.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>共享廚房 | food sharing</title>
    <link rel="shortcut icon" href="/2.0/img/favorites.png?v=2" type="image/png">
    <link rel="stylesheet" href="../2.0/css/home.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
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


    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <!--Bootstrap 輪播 (Carousel)-->
    <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>

        <div class="carousel-inner">
            <div class="carousel-item active">
                <a href="https://greenmedia.today/map_search.php" target="_blank">
                    <img src="../2.0/img/carousel-1.png" class="d-block w-100" alt="綠色生活地圖">
                </a>       
            </div>
            <div class="carousel-item">
                <a href="https://tools.heho.com.tw/bmr/" target="_blank">
                    <img src="../2.0/img/carousel-2.png" class="d-block w-100" alt="基礎代謝率 (BMR) 計算機">
                </a>        
            </div>
            <div class="carousel-item">
                <a href="https://edh.tw/lohas/article/30766" target="_blank">
                    <img src="../2.0/img/carousel-3.png" class="d-block w-100" alt="超商即期食品優惠時段整理">
                </a>
            </div>
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <!-- 即期食品 -->
    <div class="food-list" style=" margin-top: 500px; width: 100%; display: flex;justify-content: center; flex-wrap: wrap;">
        <h1 style="width: 100%; display: flex;justify-content: center; margin: 0; margin-top: 35px; color: rgb(207, 86, 35);"><b> 即 &nbsp; 期 &nbsp; 食 &nbsp; 品 </b></h1>
        <h4 style="width: 60%; display: flex; justify-content: space-evenly; padding: 0; margin: 0; margin-bottom: 50px; color: rgb(153,141,136);"><b> N E A R - E X P I R Y &nbsp; F O O D </b></h4>
        <div id="food-container" style="display: flex; justify-content: start; width: 90%; margin-left: 30px;">
            <p>載入中...</p>
        </div>
    </div>

    <!-- 垂直堆疊 (Vertical Stack) -->
    <div style="width: 100%; display: flex; align-items: center; justify-content: center; margin-top: 150px;">
        <div class="overcook" style="width: 90%; height: 575px; display: flex; align-items: center; justify-content: space-evenly; background: papayawhip; border-radius: 20px;">
            <div class="aricle" style="width: 35%; height: 450px; margin-top: 10px; display: flex; flex-wrap: wrap; justify-content: start; align-items: center;">
                <h1 style="display: flex; align-items: center;"><b> 本週推薦食譜</b><img src="../2.0/img/cookbook2-removebg-preview.png" style="height: 60px;"></h1>
                <p style="margin-top: 30px;">
                    <h4><b>忙碌生活中的小確幸： <br> 善用剩食，創造專屬於你的儀式感！</b></h4> <br>
                    <p style="font-size: 18px;">
                        您是否也覺得生活步調匆忙，連好好吃頓飯都成了奢望？ <br>
                        請善用食物分享平台上的每一份剩食！它們不僅是惜食的表現，更是您為自己增添生活儀式感的寶藏。搭配右側提供的食譜，將原本可能被忽略的食材，轉化為一頓充滿心意的美味餐點。無需花費大量時間與金錢，只需一點巧思，就能在忙碌中，享受那份專屬於您的，溫暖又美好的「自煮時光」。
                    </p>
                    <button style="width: 75px; width: 100px; border: none; border-radius: 8px; box-shadow: none; font-size: 18px; padding: 5px 10px; background: rgb(222, 162, 102);">
                        <b><a href="https://icook.tw/" target="_blank" style="text-decoration: none; text-align: center; color: #fff;"> 更多食譜 </a></b>
                    </button>
                </p>
            </div>
            
            <div class="cookbook-container">    
                <div class="cookbook-scroll" id="scrollArea">
                    <!-- 1 -->
                    <div class="cb-card" style="display: flex; flex-wrap: wrap; width: 100%;">
                        <div style="width: 50%;">
                            <div class="cb-source">Day 1.</div>
                            <h3 style="margin-top: 5px;">
                                <b>
                                    <a href="https://icook.tw/recipes/99443" target="_blank" style="text-decoration: none; color: black;">可樂雞翅 </a>
                                </b>
                            </h3>
                            <p style="font-size: 18px;">
                                可樂雞翅是忙碌生活中的儀式感救星。一鍋輕鬆完成，醬汁濃郁甜鹹，雞翅軟嫩多汁。極簡美味，今晚就試！
                            </p>
                        </div>
                        <div style="width: 50%;  display: flex; justify-content: center; align-items: end; margin-top: 55px;">
                            <img src="../2.0/img/cook1-1.jpg" style="width: 160px; height: 170px;">
                        </div>
                    </div>

                    <!-- 2 -->
                    <div class="cb-card" style="display: flex; flex-wrap: wrap; width: 100%;">
                        <div style="width: 50%;">
                            <div class="cb-source">Day 2.</div>
                            <h3 style="margin-top: 5px;">
                                <b>
                                    <a href="https://icook.tw/recipes/358448" target="_blank" style="text-decoration: none; color: black;">鮮菇絲瓜燜煮</a>
                                </b>
                            </h3>
                            <p style="font-size: 18px;">
                                厭倦了外食的重鹹重辣嗎？鮮菇絲瓜燜煮清甜無油，讓你的身體輕鬆無負擔。為味蕾帶來一次溫柔的假期！
                            </p>
                        </div>
                        <div style="width: 50%;  display: flex; justify-content: center; align-items: end; margin-top: 55px;">
                            <img src="../2.0/img/cook2-1.jpg" style="width: 160px; height: 170px;">
                        </div>
                    </div>

                    <!-- 3 -->
                    <div class="cb-card" style="display: flex; flex-wrap: wrap; width: 100%;">
                        <div style="width: 50%;">
                            <div class="cb-source">Day 3.</div>
                            <h3 style="margin-top: 5px;">
                                <b>
                                    <a href="https://icook.tw/recipes/385346" target="_blank" style="text-decoration: none; color: black;">韓式炸雞</a>
                                </b>
                            </h3>
                            <p style="font-size: 18px;">
                                星期三，小周末，就讓酥脆的韓式炸雞配啤酒，享受解放後的微醺與暢聊吧！
                            </p>
                        </div>
                        <div style="width: 50%;  display: flex; justify-content: center; align-items: end; margin-top: 55px;">
                            <img src="../2.0/img/cook4-1.jpg" style="width: 160px; height: 170px;">
                        </div>
                    </div>

                    <!-- 4 -->
                    <div class="cb-card" style="display: flex; flex-wrap: wrap; width: 100%;">
                        <div style="width: 50%;">
                            <div class="cb-source">Day 4.</div>
                            <h3 style="margin-top: 5px;">
                                <b>
                                    <a href="https://icook.tw/recipes/135246" target="_blank" style="text-decoration: none; color: black;">麻婆豆腐</a>
                                </b>
                            </h3>
                            <p style="font-size: 18px;">
                                這道麻婆豆腐香麻開胃，豆腐滑嫩。只需簡單快炒，就能快速變出一道超級下飯的美味
                            </p>
                        </div>
                        <div style="width: 50%;  display: flex; justify-content: center; align-items: end; margin-top: 55px;">
                            <img src="../2.0/img/cook3-1.jpg" style="width: 160px; height: 170px;">
                        </div>
                    </div>

                    <!-- 5 -->
                    <div class="cb-card" style="display: flex; flex-wrap: wrap; width: 100%;">
                        <div style="width: 50%;">
                            <div class="cb-source">Day 5.</div>
                            <h3 style="margin-top: 5px;">
                                <b>
                                    <a href="https://icook.tw/recipes/423596" target="_blank" style="text-decoration: none; color: black;">韓式部隊鍋</a>
                                </b>
                            </h3>
                            <p style="font-size: 18px;">
                                最近追劇是不是被裡面的美食誘惑到流口水？熱騰騰的部隊鍋，豐富配料搭配濃郁湯頭，讓你瞬間走進韓劇場景！
                            </p>
                        </div>
                        <div style="width: 50%;  display: flex; justify-content: center; align-items: end; margin-top: 55px;">
                            <img src="../2.0/img/cook5-1.jpg" style="width: 160px; height: 170px;">
                        </div>
                    </div>

                </div>

                <div class="cb-controls">
                    <button class="cb-btn" id="cbUp">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                    </button>
                    <button class="cb-btn" id="cbDown">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                </div>

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

    <script src="home.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script> 
</body>
</html>