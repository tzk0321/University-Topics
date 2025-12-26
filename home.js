function formatTimeLeft(expiryStr) {
    const formattedExpiry = expiryStr.replace(" ", "T"); 
    const now = new Date();
    const expiry = new Date(formattedExpiry);
    const diffMs = expiry - now;

    if (diffMs <= 0) return "已過期";

    const diffHrs = Math.floor((diffMs % 86400000) / 3600000);
    const diffMins = Math.round(((diffMs % 86400000) % 3600000) / 60000);

    return `${diffHrs} 小時 ${diffMins} 分鐘`;
}

function loadFoodData() {
    console.log("開始抓取資料..."); 

    fetch('home1.php')
        .then(response => response.json())
        .then(data => {
            console.log("成功抓到資料:", data); 

            const container = document.getElementById('food-container');
            
            if (!container) {
                console.error("找不到 id='food-container' 的元素！請檢查 HTML。");
                return;
            }

            let html = '';

            if (data.length === 0) {
                container.innerHTML = '<p>目前沒有即期品。</p>';
                return;
            }

            data.forEach(item => {
                const timeLeft = formatTimeLeft(item.expiry_datetime);
                const imgUrl = item.image_filename ? `uploads/${item.image_filename}` : 'uploads/default.jpg';
                
                html += `
                    <div class="food-item">
                        <div class="img-card" style="margin: 0;">
                            <img src="${imgUrl}" alt="${item.name}" >
                        </div>
                        <div class="layer"></div>
                        <div class="info">
                            <h4 class="name"><strong>${item.food_name}</strong></h4> <br>
                            <p style="font-size: 16px; margin: 0; margin-bottom: 5px;">
                                份數：${item.quantity} ${item.unit} <br>
                                取貨地點：<br>
                                ${item.pickup_address_city} &nbsp; ${item.pickup_landmark} <br>
                                ⏰剩餘: ${timeLeft}  <br>
                            </p>
                            <button class="btn" onclick="location.href='detail.php?id=${item.food_id}'"><B> 查看更多 </B></button>
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;
        })
        .catch(error => {
            console.error('JS 執行錯誤:', error);
        });
}

loadFoodData();
setInterval(loadFoodData, 600000);




function validateSearch() {
            var input = document.getElementById('search-input').value;
            
            if (input.trim() === "") {
                alert("請輸入搜尋內容！");
                return false;
            }
            return true; 
        }






/* 垂直堆疊 (Vertical Stack) */
document.addEventListener('DOMContentLoaded', () => {
            const scrollArea = document.getElementById('scrollArea');
            const cards = scrollArea.querySelectorAll('.cb-card');
            const btnUp = document.getElementById('cbUp');
            const btnDown = document.getElementById('cbDown');

            // 1. Sticky 錯位設定
            const baseTop = 20; 
            const offset = 18;  
            
            cards.forEach((card, index) => {
                card.style.top = `${baseTop + (index * offset)}px`;
            });

            // 2. 滾動動畫邏輯
            scrollArea.addEventListener('scroll', () => {
                cards.forEach((card, index) => {
                    const nextCard = cards[index + 1];
                    if (nextCard) {
                        const cardRect = card.getBoundingClientRect();
                        const nextCardRect = nextCard.getBoundingClientRect();
                        
                        const diff = nextCardRect.top - cardRect.top;
                        const animationRange = 350; // 動畫區間維持卡片高度

                        if (diff < animationRange && diff > 0) {
                            const progress = 1 - (diff / animationRange);
                            const scale = 1 - (progress * 0.06); 
                            const brightness = 1 - (progress * 0.05);

                            card.style.transform = `scale(${scale})`;
                            card.style.filter = `brightness(${brightness})`;
                        } else if (diff <= 0) {
                            card.style.transform = `scale(0.94)`;
                            card.style.filter = `brightness(0.95)`;
                        } else {
                            card.style.transform = `scale(1)`;
                            card.style.filter = `brightness(1)`;
                        }
                    }
                });
            });

            // 3. 按鈕邏輯
            const scrollDistance = 350 + 30; // 卡片高度 + margin
            btnDown.addEventListener('click', () => {
                scrollArea.scrollBy({ top: scrollDistance, behavior: 'smooth' });
            });

            btnUp.addEventListener('click', () => {
                scrollArea.scrollBy({ top: -scrollDistance, behavior: 'smooth' });
            });
});