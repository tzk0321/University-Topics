// 定義自訂 Plugin 來在中間繪製百分比和圖標
const centerContentPlugin = {
    id: 'centerContent',
    beforeDraw: (chart) => {
        const { ctx, chartArea: { left, top, width, height } } = chart;
        ctx.save();
        
        const centerX = left + width / 2;
        const centerY = top + height / 2;

        const chartOptions = chart.options.plugins.centerContent || {};
        const percentage = chartOptions.percentage;
        const iconUnicode = chartOptions.iconUnicode; // 我們在 options 中傳遞圖標 Unicode
        const iconColor = chartOptions.iconColor || '#333';
        const percentColor = chartOptions.percentColor || '#333';
        const percentFont = chartOptions.percentFont || 'bold 30px Arial';
        
        // --- 繪製百分比 ---
        ctx.font = percentFont;
        ctx.fillStyle = percentColor;
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(`${percentage}%`, centerX + 10, centerY); // 稍微向右偏移為圖標留位

        // --- 繪製圖標 ---
        if (iconUnicode) {
            ctx.font = '24px "Font Awesome 6 Free"'; // Font Awesome 字體
            ctx.fillStyle = iconColor;
            ctx.fillText(iconUnicode, centerX - 25, centerY); // 稍微向左偏移
        }
        
        ctx.restore();
    }
};

// 註冊 Plugin (只需一次)
Chart.register(centerContentPlugin);

// 3. 創建一個函數來繪製每個進度環
function createProgressRing(canvasId, percentage, ringColor, iconUnicode, iconColor, bottomText) {
    const ctx = document.getElementById(canvasId).getContext('2d');
    const remaining = 100 - percentage;

    // 每個環形圖的數據
    const data = {
        datasets: [{
            data: [percentage, remaining],
            backgroundColor: [ringColor, '#f0f0f0'], // 前景顏色和背景（未完成部分）顏色
            borderColor: ['#fff', '#fff'],
            borderWidth: 0, // 無邊框，讓它看起來更乾淨
            borderRadius: 50 // 讓環形末端是圓形的 (圓角)
        }]
    };

    new Chart(ctx, {
        type: 'doughnut',
        data: data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '80%', // 環形中間的空心大小
            rotation: -90, // 從上方開始繪製
            circumference: 360, // 完整的圓
            plugins: {
                legend: {
                    display: false // 不顯示圖例
                },
                tooltip: {
                    enabled: false // 不顯示工具提示
                },
                centerContent: { // 傳遞給 plugin 的自訂選項
                    percentage: percentage,
                    iconUnicode: iconUnicode,
                    iconColor: iconColor,
                    percentColor: ringColor // 百分比顏色與環形顏色相同
                }
            },
            // 在圖表下方添加自訂文字（可以不透過 plugin）
            // Chart.js 本身沒有直接在 canvas 下方顯示文字的選項，通常在 HTML 中處理
            // 我們在 HTML 結構中已經有 .skill-title 來處理底部文字
        }
    });
}

// 4. 初始化所有進度環圖表
createProgressRing(
    'webDesignChart',
    36.5,
    '#2fac74ff', 
);

createProgressRing(
    'htmlCssChart',
    94,
    '#2980b9', 
);

createProgressRing(
    'graphicDesignChart',
    70,
    '#e05583ff',
);

createProgressRing(
    'uiUxChart',
    43,
    '#f39c12',
);