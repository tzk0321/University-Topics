// r&s.js - 新版 (只負責互動，不負責抓資料)

// 1. 手風琴效果 (Accordion)
document.addEventListener('click', function(e) {
    // 檢查點擊的元素是否是 product_title
    const title = e.target.closest('.product_title');
    
    if (title) {
        // 切換 active class (讓箭頭旋轉)
        title.classList.toggle('active');
        
        // 找到下一個兄弟元素 (product_details)
        const details = title.nextElementSibling;
        
        if (details && details.classList.contains('product_details')) {
            // 切換展開/收合
            if (details.style.maxHeight) {
                // 收合
                details.style.maxHeight = null;
                details.classList.remove('open');
            } else {
                // 展開
                details.classList.add('open');
                details.style.maxHeight = details.scrollHeight + "px";
            }
        }
    }
});

// 2. 審核功能 (同意/婉拒)
function respond(reservationId, action) {
    const actionText = (action === 'confirmed') ? '同意' : '婉拒';
    if (!confirm('確定要' + actionText + '這個預約嗎？')) return;

    // 呼叫後端 API
    fetch('api_respond.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id=${reservationId}&action=${action}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('操作成功！');
            
            // 更新畫面 DOM 元素 (把按鈕換成文字)
            const actionArea = document.getElementById('action-area-' + reservationId);
            const statusBadge = document.getElementById('status-badge-' + reservationId);
            
            if (actionArea) {
                actionArea.innerHTML = `<p style="color: #777;">已處理 (${action === 'confirmed' ? '預約成功' : '已婉拒'})</p>`;
            }
            
            if (statusBadge) {
                if(action === 'confirmed') {
                    statusBadge.innerText = "預約成功";
                    statusBadge.style.backgroundColor = "#4CAF50";
                } else {
                    statusBadge.innerText = "已婉拒";
                    statusBadge.style.backgroundColor = "#F44336";
                }
            }
        } else {
            alert('失敗：' + data.message);
        }
    })
    .catch(err => {
        console.error(err);
        alert('系統錯誤，請檢查網路連線');
    });
}