

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>關於我們 | Food Sharing</title>
    <link rel="shortcut icon" href="img/favorites.png" type="image/x-icon">
    <link rel="stylesheet" href="../2.0/css/aboutus.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* --- 通知與帳號區塊容器 --- */
.notifications, .account {
    display: flex;
    align-items: center;
    height: 100%;
    position: relative; /* 為了下拉選單定位 */
}

/* 鈴鐺按鈕容器 */
.notif-btn {
    position: relative; /* ★ 關鍵：讓紅點以此為基準定位 */
    display: flex;
    align-items: center;
    text-decoration: none;
    cursor: pointer;
}

/* Icon 圖片大小統一 */
.notifications img, .account img {
    width: 28px;
    height: 28px;
    display: block;
}

/* 紅點樣式 */
.badge {
    position: absolute;
    /* 微調位置，讓它騎在鈴鐺右上角 */
    top: -5px;   
    right: -8px; 
    
    background-color: #ff3b30;
    color: white;
    border-radius: 50%;
    padding: 1px 5px;
    font-size: 11px;
    font-weight: bold;
    border: 2px solid rgb(233,230,225); /* 邊框同背景色，製造鏤空感 */
    min-width: 15px;
    text-align: center;
    line-height: 1.2;
}

/* 通知下拉選單 */
.notif-dropdown {
    display: none; /* 預設隱藏 */
    position: absolute;
    top: 50px; /* 在 Header 下方 */
    right: -10px;
    width: 300px;
    background-color: white;
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    border-radius: 8px;
    z-index: 1000;
    overflow: hidden;
    border: 1px solid #eee;
}

/* 滑鼠移過顯示下拉選單 */
.notifications:hover .notif-dropdown {
    display: block;
}

.notif-header {
    background-color: #f8f9fa;
    padding: 10px 15px;
    font-weight: bold;
    border-bottom: 1px solid #eee;
    color: #333;
    font-size: 14px;
}

.notif-item {
    padding: 10px 15px;
    border-bottom: 1px solid #f0f0f0;
    transition: background 0.2s;
    font-size: 14px;
    color: #444;
    cursor: pointer;
}

.notif-item:hover {
    background-color: #fafafa;
}

.notif-item.unread {
    background-color: #fff8e1;
    border-left: 3px solid #ff9800;
}

.notif-item:last-child {
    border-bottom: none;
}

.notif-item small {
    color: #999;
    font-size: 12px;
    display: block;
    margin-top: 4px;
}
    </style>
</head>
<body>
    <?php include 'header.php'; ?>


    <div class="article">
        <h1>Food-sharing 想做的是「讓每份食物，都找到它的歸屬。」</h1>
        <h4>在全球，三分之一的食物被白白浪費，而同時，卻仍有無數人在為下一餐而煩惱。</h4>

        <div class="skills-section">
            <div class="skills-container">
                <div class="skill-item">
                    <canvas id="webDesignChart" class="chart-canvas"></canvas>
                    <div class="skill-title">
                        碳排放量 <br>
                        <p class="skill-subtitle">
                            相當於減少了全台年平均碳排放（約2.6億公噸）的此百分比
                        </p>
                    </div>
                </div>
                <div class="skill-item">
                    <canvas id="graphicDesignChart" class="chart-canvas"></canvas>
                    <div class="skill-title">
                        餐點份數 <br>
                        <p class="skill-subtitle">
                            佔本平台年度惜食目標<br>（10萬份）之達成率
                        </p>
                    </div>
                </div>
                <div class="skill-item">
                    <canvas id="htmlCssChart" class="chart-canvas"></canvas>
                    <div class="skill-title">
                        媒合成功率
                        <p class="skill-subtitle">
                            佔全平台上架餐點總數<br>
                            之成功領取比例
                        </p>
                    </div>
                </div>
                <div class="skill-item">
                    <canvas id="uiUxChart" class="chart-canvas"></canvas>
                    <div class="skill-title">
                        參與人數 <br>
                        <p class="skill-subtitle">
                            佔全台總人口數<br>
                            （約2300萬人）之響應比例
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="say">
            <h3>🎯 我們相信，多餘不等於浪費。</h3>
            <p class="text">
                Food-sharing 的創立，源自於一個簡單而堅定的信念：廚餘也可以轉化為溫暖。讓多餘的食物找到真正需要它們的人。
                我們目睹了全球有大量仍可食用的物資被丟棄，同時卻有無數人正在為下一餐煩憂。
                我們深信，透過科技與每個人的力量，我們可以建立一個更有效率、更具愛心的循環，
                讓每一份食物都能圓滿地找到它的歸宿。
                我們的目標，就是讓「剩食」一詞，從此退出歷史舞臺。
            </p>
            <h3>🔥 剩食危機：不僅是浪費，更是地球的巨大負擔。</h3>
            <p class="text">
                食物浪費帶來的衝擊遠超乎想像。
                當食物變成廚餘，它不僅是資源（水、能源、勞力）的巨大耗損，
                更在掩埋過程中產生大量的溫室氣體，直接加速氣候變遷。
                這份環境負擔，加上社會資源分配不均的矛盾，是我們共同面對的嚴峻挑戰。
                我們不能再允許這種無謂的犧牲持續發生。
            </p>

            <h3>🤝 立即加入：參與最簡單、最直接的環保行動。</h3>
            <div class="benefit-card">
                <div class="card">
                    <h4>簡單分享</h4>
                    <p class="text">輕鬆上傳剩食資訊，幾分鐘內完成愛心傳遞。</p>
                </div><hr>
                <div class="card">
                    <h4>安全可靠</h4>
                    <p class="text">嚴格遵守食物安全規範，保障分享者與領取者權益。</p>
                </div><hr>
                <div class="card">
                    <h4>高效環保</h4>
                    <p  class="text">即時媒合，減少食物閒置時間，最大化環保效益。</p>
                </div>
            </div>
            <p  class="text">
                我們不只是一個平台，我們更是一場全民的綠色行動。
                在此，每位使用者都不是旁觀者，而是「食物零浪費行動者」（Zero-Waste Food Activists）。
                這個稱謂代表著您已經決定，用最實際、最有效的方式，對抗食物浪費，守護我們的地球與社會。
            </p>
            <p  class="text">
                無論您是手邊有多餘食材的家庭、想為地球盡一份心的店家，還是正尋找一頓溫飽的夥伴，
                Food-sharing 都為您搭建了一個友善、安全、高效的分享橋樑。
                加入我們，不僅是分享一份食物，更是分享一份愛心，一份對地球的責任。 
                讓我們一起，讓每一口食物都充滿意義。
            </p>

            <h3>✨ 平台價值：我們是連結環保與人道關懷的數位橋樑。</h3>
            <p class="text">
                Food-sharing 的價值，不僅僅在於分享食物本身。
                我們的價值在於，我們成功地將個人的善意，轉化為可計算的環保成果和可衡量的社會影響力。
                並向世界證明：透過科技與團結，我們確實能創造一個零浪費的美好未來。
            </p>
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

    <script src="aboutus.js"></script>
</body>
</html>