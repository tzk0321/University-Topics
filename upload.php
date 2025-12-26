<?php require_once 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>上傳料理 | Food Sharing</title>
    <link rel="shortcut icon" href="img/favorites.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../2.0/css/upload.css">
    <link rel="stylesheet" href="../2.0/css/r&S.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="list-group-scroll-container">
        <!--列表群組 (list-group)-->
        <div class="list-group w-25 mt-5" id="list-group">
            <a href="#step1" class="list-group-item list-group-item-action my-custom-color" aria-current="true"><b> &nbsp; step 1. &nbsp; 餐點基本資料 </b></a>
            <a href="#step2" class="list-group-item list-group-item-action my-custom-color"><b> &nbsp; step 2. &nbsp; 餐點詳細資料 </b></a>
            <a href="#step3" class="list-group-item list-group-item-action my-custom-color"><b> &nbsp; step 3. &nbsp; 預覽餐點 &nbsp; </b></a>
            <a href="#step4" class="list-group-item list-group-item-action my-custom-color"><b> &nbsp; step 4. &nbsp; 確認上傳餐點 </b></a>
        </div>

        <form id="uploadForm" class="uploadForm" action="uploadif.php" method="post" enctype="multipart/form-data">
            <div data-bs-spy="scroll" data-bs-target="#list-group" data-bs-offset="0" class="scrollspy-example" tabindex="0">
                <div id="step1">
                    <h3><b>開始上傳餐點</b></h3>
                    <h4 style="margin-top: 30px; margin-bottom: 30px;">step 1. &nbsp; 餐點基本資料</h4>
                    <input type="file" id="imageUpload" name="imageFile" accept="image/*" style="display: block; margin-bottom: 15px;" onchange="previewImage(this)">
                    <p>
                        餐點名稱 &nbsp;
                        <input type="text" class="custom-input1" id="name" name="food-name" oninput="updatePreview()" required>
                    </p>
                    <p>
                        份數 &nbsp;
                        <input type="number" class="custom-input2" id="number" name="food-number" placeholder="數量" oninput="updatePreview()" required>&nbsp;
                        <input type="text" class="custom-input2" id="unit" name="food-unit" placeholder="單位" oninput="updatePreview()" required>
                    </p>
                    <p>
                        類別 <br> &nbsp;
                        <label style="margin-top: 10px;"><input type="radio" name="food-class" value="熟食"> 熟食 </label> &nbsp;
                        <label><input type="radio" name="food-class" value="乾貨/速食"> 乾貨/速食 </label> &nbsp;
                        <label><input type="radio" name="food-class" value="生鮮食品"> 生鮮食品 </label> &nbsp;
                        <label><input type="radio" name="food-class" value="蔬果"> 蔬果 </label> &nbsp;
                        <label><input type="radio" name="food-class" value="飲料"> 飲料 </label> &nbsp;
                        <label><input type="radio" name="food-class" value="零食"> 零食 </label> &nbsp;
                        <label><input type="radio" name="food-class" value="甜點"> 甜點 </label> &nbsp;
                        <label><input type="radio" name="food-class" value="其他"> 其他 </label>
                    </p>
                    <p>
                        標籤 (選填) <br> &nbsp;
                        <label class="tag"><input type="checkbox" name="food-label[]" value="便當"><span class="tag-text"> 🍱便當 </span></label> &nbsp;
                        <label class="tag"><input type="checkbox" name="food-label[]" value="麵食"><span class="tag-text"> 🍜麵食 </span></label> &nbsp;
                        <label class="tag"><input type="checkbox" name="food-label[]" value="飯食"><span class="tag-text"> 🍚飯食 </span></label> &nbsp;
                        <label class="tag"><input type="checkbox" name="food-label[]" value="飲料"><span class="tag-text"> 🥤飲料 </span></label> &nbsp;
                        <label class="tag"><input type="checkbox" name="food-label[]" value="麵包"><span class="tag-text"> 🥐麵包 </span></label> &nbsp;
                        <label class="tag"><input type="checkbox" name="food-label[]" value="蛋糕"><span class="tag-text"> 🍰蛋糕 </span></label> &nbsp; <br> &nbsp;
                        <label class="tag"><input type="checkbox" name="food-label[]" value="素食" id="vegetarianCheckbox"><span class="tag-text"> 🥗素食 </span></label> &nbsp;
                        <label class="tag"><input type="checkbox" name="food-label[]" value="罐頭"><span class="tag-text"> 🥫罐頭 </span></label> &nbsp;
                        <label class="tag"><input type="checkbox" name="food-label[]" value="有機" id="organicCheckbox"><span class="tag-text"> 🌾有機 </span></label> &nbsp;
                        <label class="tag"><input type="checkbox" name="food-label[]" value="即期食品" id="expiringCheckbox"><span class="tag-text"> ⏰即期食品 </span></label> &nbsp;
                    </p>
                    </p>
                    <p style="margin-top: 30px;">
                        有效日期 &nbsp;
                        <input type="datetime-local" id="event-datetime1" name="event-datetime1" class="datetime-input-style" oninput="updatePreview()" required>
                    </p>
                </div>

                <div id="step2" style="margin-top: 70px;">
                    <h4 style="margin-top: 30px; margin-bottom: 30px;">step 2. &nbsp; 餐點詳細資料</h4>
                    <p>
                        取貨地點 (詳細地址不會直接公布在餐點介紹上) &nbsp; <br>
                        &nbsp;<input type="text" class="custom-input3" id="city" name="place1" placeholder=" 請輸入縣市/鄉鎮市區" oninput="updatePreview()" style="margin-top: 15px;" required><br>
                        &nbsp;<input type="text" class="custom-input3" name="place2" placeholder=" 請輸入街巷弄/號" style="margin-top: 15px;" required><br>
                        &nbsp;<input type="text" class="custom-input1" id="landmark" name="place3" placeholder=" 明顯建築物 (選填)" oninput="updatePreview()" style="margin-top: 15px;" >
                    </p>
                    <p>
                        食材來源 &nbsp;
                        <input type="text" class="custom-input1" name="origin" placeholder=" 店家/自製" required>
                    </p>
                    <p>
                        保存方式 &nbsp;
                        <select class="selected" name="selected" id="fruit-select" style="border-radius: 5px;" required>
                            <option class="selected-item" value="常溫">常溫</option>
                            <option class="selected-item" value="冷藏">冷藏</option>
                            <option class="selected-item" value="冷凍">冷凍</option>
                        </select>
                    </p>
                    <p>
                        狀態 &nbsp;
                        <label><input type="radio" name="state" value="未開封"> 未開封 </label> &nbsp;
                        <label><input type="radio" name="state" value="已開封"> 已開封 </label> &nbsp;
                    </p>
                    <p>
                        備註 <br>
                        <TEXTAREA NAME="remark" id="remark" ROWS="8" COLS="60" placeholder=" 說明分享原因 / 蛋奶素還是純素 / 可能的過敏成分 / 其他" class="BG-Copy" style="border:1px #2f3944 solid; margin-top: 8px;"></TEXTAREA>
                    </p>
                </div>

                <div id="step3" style="margin-top: 70px;">
                    <h4 style="margin-top: 30px; margin-bottom: 30px;">step 3. &nbsp; 預覽餐點</h4>
                    <div class="preview-section" style=" display: flex; align-items: start;">
                        <h3 style="color:#555; margin-bottom:15px;">預覽效果</h3>
                        
                        <div class="food-item">
                            <div class="img-card">
                                <img id="p-img" src="" style="display: none;">
                                <div id="p-placeholder" style="color: #999; font-size: 14px;">上傳圖片後顯示</div>
                            </div>
                            
                            <div class="layer"></div>
                            
                            <div class="info">
                                <h4 class="name"><strong id="p-name">食物名稱</strong></h4> <br>
            
                                <p style="font-size: 16px; margin: 0; margin-bottom: 5px;">
                                    份數：<span id="p-qty">  </span> <span id="p-unit"> </span> <br>
                                    取貨地點：<br>
                                    <span id="p-city"></span> &nbsp; <span id="p-landmark"></span> <br>
                                    有效期限: <br><span id="p-time">--/-- --:--</span>
                                </p>
                                
                                <button class="btn-view" type="button"><B> 查看更多 </B></button>
                            </div>
                        </div>
                    
                        <p style="color: #999; font-size: 13px; margin-top: 15px;">(即時預覽上架樣式)</p>
                    </div>
                </div>

                <div id="step4" style="margin-top: 70px; margin-bottom: 50px;">
                    <h4 style="margin-top: 30px; margin-bottom: 30px;">step 4. &nbsp; 確認上傳餐點</h4>
                    <label style="margin-bottom: 30px;">
                        <input type="radio" required> 已詳細閱讀
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal" style="border: none; text-decoration: underline; background: rgb(249,245,239); color: rgb(117, 144, 139); padding: 0; margin: 0;"><b>《食物安全聲明》</b></button>
                        並能遵守以上條例
                    </label><br>
                    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel"><b>📃食物安全聲明</b></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p> 
                                        所有在本平台發佈的食物，必須嚴格遵守食品安全法規，確保：<br>
                                        1. 來源可追溯性： 食材與產品來源公開、透明，並具備完整的文件紀錄。 <br>
                                        2. 品質與安全： 產品必須安全、衛生、未變質，完全適用於人類食用。 <br>
                                        任何不符合上述標準的食品內容，將被立即移除並追究相關責任。
                                    </p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">閱讀完畢</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="uploadbtn"> 分享 </button>
                </div>
            </div> 
        </form>
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
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="upload.js"></script>

    <script>
    // 1. 圖片預覽功能 (這裡不需要改 ID，因為是透過 this 傳入 input)
    function previewImage(input) {
        // 抓取右邊預覽區的圖片標籤 ID (p-img)
        const img = document.getElementById('p-img');
        // 抓取右邊預覽區的提示文字 ID (p-placeholder)
        const placeholder = document.getElementById('p-placeholder');

        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function(e) {
                // 讀取成功，顯示圖片
                img.src = e.target.result;
                img.style.display = 'block';
                placeholder.style.display = 'none';
            }
            
            reader.readAsDataURL(input.files[0]);
        } else {
            // 取消選擇，恢復原狀
            img.src = "";
            img.style.display = 'none';
            placeholder.style.display = 'flex'; // 或 block，看你的排版
        }
    }

    // 2. 文字與日期即時更新功能
    function updatePreview() {
        const nameInput = document.getElementById('name');
        const qtyInput = document.getElementById('number');
        const unitInput = document.getElementById('unit');
        const cityInput = document.getElementById('city');
        const landmarkInput = document.getElementById('landmark');
        const expiryInput = document.getElementById('event-datetime1');

        // 為了防止找不到元素報錯，加個保險，如果沒抓到元素就給空字串
        const name = nameInput ? nameInput.value : '';
        const qty = qtyInput ? qtyInput.value : '';
        const unit = unitInput ? unitInput.value : '';
        const city = cityInput ? cityInput.value : '';
        const landmark = landmarkInput ? landmarkInput.value : '';
        const expiryVal = expiryInput ? expiryInput.value : '';

        // 更新預覽區文字 (若無輸入則顯示預設值)
        // 這裡的 p-name, p-qty... 是右邊預覽卡片裡的 id
        document.getElementById('p-name').innerText = name || '食物名稱';
        document.getElementById('p-qty').innerText = qty;
        document.getElementById('p-unit').innerText = unit;
        document.getElementById('p-city').innerText = city;
        document.getElementById('p-landmark').innerText = landmark;

        // 日期格式化處理 (YYYY/MM/DD HH:MM)
        const timeSpan = document.getElementById('p-time');
        
        if (expiryVal) {
            const now = new Date();
            const expDate = new Date(expiryVal);

            const year = expDate.getFullYear();
            const month = (expDate.getMonth() + 1).toString().padStart(2, '0');
            const date = expDate.getDate().toString().padStart(2, '0');
            const hour = expDate.getHours().toString().padStart(2, '0');
            const min = expDate.getMinutes().toString().padStart(2, '0');

            timeSpan.innerText = `${year}/${month}/${date} ${hour}:${min}`;

            // 過期變紅字提醒
            if (expDate < now) {
                timeSpan.style.color = "#ff4d4d";
            } else {
                timeSpan.style.color = "white";
            }
        } else {
            timeSpan.innerText = "--/-- --:--";
            timeSpan.style.color = "white";
        }
    }
</script>
</body>
</html>