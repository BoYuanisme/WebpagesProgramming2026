<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>【作業4】垃圾郵件寄送系統</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: #f4f6f9; 
            padding: 40px 20px; 
            display: flex; 
            justify-content: center; 
            gap: 30px;
        }
        .card { 
            background: white; 
            padding: 30px; 
            border-radius: 12px; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.08); 
            width: 450px; 
            box-sizing: border-box;
        }
        h3 { 
            border-bottom: 2px solid #eef2f5; 
            padding-bottom: 15px; 
            margin-top: 0;
            color: #2c3e50;
            font-size: 20px;
        }
        .form-group { margin-bottom: 20px; }
        label { 
            display: block; 
            margin-bottom: 8px; 
            font-weight: bold; 
            font-size: 14px;
            color: #34495e;
        }
        input[type="text"], input[type="email"], input[type="number"], select, textarea { 
            width: 100%; 
            padding: 10px; 
            border: 1px solid #dcdde1; 
            border-radius: 6px; 
            box-sizing: border-box;
            font-size: 14px;
            transition: border 0.2s;
        }
        input:focus, select:focus, textarea:focus {
            border-color: #3498db;
            outline: none;
        }
        button { 
            background: #3498db; 
            color: white; 
            border: none; 
            padding: 12px; 
            width: 100%; 
            border-radius: 6px; 
            cursor: pointer; 
            font-size: 16px;
            font-weight: bold;
            transition: background 0.2s;
        }
        button:hover { background: #2980b9; }
        button:disabled { background: #bdc3c7; cursor: not-allowed; }
        
        #addMsg { 
            color: #27ae60; 
            font-weight: bold; 
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
        }
        
        /* --- 進度條優化樣式 --- */
        #progressContainer { 
            display: none; 
            margin-top: 25px; 
        }
        .progress-bar-bg { 
            width: 100%; 
            background-color: #e0e0e0; 
            border-radius: 6px; 
            height: 25px; 
            overflow: hidden;
            position: relative; 
            margin-bottom: 10px;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
        }
        .progress-bar-fill { 
            height: 100%; 
            background-color: #2ecc71; 
            width: 0%; 
            transition: width 0.3s ease; 
        }
        .progress-text-inside {
            position: absolute;
            width: 100%;
            text-align: center;
            top: 0;
            left: 0;
            line-height: 25px; 
            font-weight: bold;
            color: #2c3e50;
            font-size: 13px;
        }
        #progressText { 
            text-align: center; 
            font-size: 14px; 
            color: #7f8c8d; 
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="card">
        <h3>A. 建構名單資料庫</h3>
        <div class="form-group">
            <label>新增 Email 到資料庫</label>
            <input type="email" id="newEmail" placeholder="例如: test@mail.nuk.edu.tw">
        </div>
        <button onclick="addEmail()">加入資料庫</button>
        <p id="addMsg"></p>
    </div>

    <div class="card">
        <h3>B. 垃圾郵件寄送控制</h3>
        
        <div class="form-group">
            <label>寄送模式</label>
            <select id="sendMode" onchange="toggleRandomCount()">
                <option value="all">全部寄送</option>
                <option value="random">隨機寄送幾筆</option>
            </select>
        </div>

        <div class="form-group" id="countDiv" style="display: none;">
            <label>隨機寄送筆數</label>
            <input type="number" id="randomCount" value="5" min="1">
        </div>

        <div class="form-group">
            <label>寄送間隔時間 (秒)</label>
            <input type="number" id="interval" value="2" min="0">
        </div>

        <div class="form-group">
            <label>信件主旨</label>
            <input type="text" id="subject" value="這是垃圾郵件測試">
        </div>

        <div class="form-group">
            <label>信件內容</label>
            <textarea id="body" rows="4">你好，這是一封測試信件！</textarea>
        </div>

        <button onclick="startSending()" id="sendBtn">開始寄送 🚀</button>

        <div id="progressContainer">
            <div class="progress-bar-bg">
                <div class="progress-bar-fill" id="progressBar"></div>
                <div class="progress-text-inside" id="progressPercentText">0%</div>
            </div>
            <div id="progressText">準備寄送...</div>
        </div>
    </div>

    <script>
        // 切換隨機輸入框顯示/隱藏
        function toggleRandomCount() {
            const mode = document.getElementById('sendMode').value;
            document.getElementById('countDiv').style.display = mode === 'random' ? 'block' : 'none';
        }

        // A. 新增 Email (含重複檢查判定)
        async function addEmail() {
            const email = document.getElementById('newEmail').value.trim();
            const msgElement = document.getElementById('addMsg');
            if(!email) return alert('請輸入 Email');
            
            let fd = new FormData();
            fd.append('action', 'add_email');
            fd.append('email', email);

            try {
                let res = await fetch('api.php', { method: 'POST', body: fd });
                let data = await res.json();
                
                msgElement.innerText = data.msg;
                if(data.msg.includes('已經存在')) {
                    msgElement.style.color = '#f39c12'; 
                } else if(data.status === 'success') {
                    msgElement.style.color = '#27ae60'; 
                    document.getElementById('newEmail').value = '';
                } else {
                    msgElement.style.color = '#c0392b'; 
                }
            } catch (e) {
                alert('系統錯誤，請確認 api.php 運作正常且 MySQL 已啟動！');
            }
        }

        // B. 寄信控制核心邏輯 (含進度條百分比動態更新與防呆)
        async function startSending() {
            const mode = document.getElementById('sendMode').value;
            const count = document.getElementById('randomCount').value;
            const interval = document.getElementById('interval').value;
            const subject = document.getElementById('subject').value;
            const body = document.getElementById('body').value;
            
            const btn = document.getElementById('sendBtn');
            const progressContainer = document.getElementById('progressContainer');
            const progressBar = document.getElementById('progressBar');
            const progressPercentText = document.getElementById('progressPercentText');
            const progressText = document.getElementById('progressText');

            if(!subject || !body) return alert('請填寫主旨與內容');

            // 鎖定按鈕
            btn.disabled = true;
            btn.innerText = "發送中...";

            // 1. 取得要寄送的名單
            let fd = new FormData();
            fd.append('action', 'get_targets');
            fd.append('mode', mode);
            fd.append('count', count);

            let targets = [];
            try {
                let res = await fetch('api.php', { method: 'POST', body: fd });
                let data = await res.json();
                targets = data.targets;
            } catch (e) {
                alert('無法取得發送名單，請確認資料庫內已有資料。');
                btn.disabled = false;
                btn.innerText = "開始寄送 🚀";
                return;
            }

            let total = targets.length;
            if(total === 0) {
                alert("資料庫中沒有可發送的 Email 名單！");
                btn.disabled = false;
                btn.innerText = "開始寄送 🚀";
                return;
            }

            // 🛑 【核心防呆機制】處理設定數量 n 大於 資料庫名單總數 m 的狀況
            if (mode === 'random' && parseInt(count) > total) {
                alert(`💡 提示：您設定隨機寄送 ${count} 筆，但目前資料庫內僅有 ${total} 筆名單。\n系統將自動調整為寄送上限 ${total} 封，且不會重複寄送給同一個信箱。`);
            }

            // 2. 初始化進度條
            progressContainer.style.display = 'block';
            progressBar.style.width = '0%';
            progressPercentText.innerText = '0%';
            
            // 3. 迴圈發送與秒數限制
            for (let i = 0; i < total; i++) {
                // 如果不是第一筆，且設定有時間間隔，則進行等待
                if (i > 0 && interval > 0) {
                    progressText.innerText = `⏳ 等待 ${interval} 秒後發送下一封...`;
                    await new Promise(resolve => setTimeout(resolve, interval * 1000));
                }

                progressText.innerText = `正在寄送至: ${targets[i]}...`;

                // 呼叫寄單筆的 API
                let mailFd = new FormData();
                mailFd.append('action', 'send_single');
                mailFd.append('email', targets[i]);
                mailFd.append('subject', subject);
                mailFd.append('body', body);

                try {
                    await fetch('api.php', { method: 'POST', body: mailFd });
                } catch(e) {
                    console.error(`寄送至 ${targets[i]} 時發生連線錯誤`);
                }

                // 4. 計算並同步更新進度條與中央百分比數字
                let percent = Math.round(((i + 1) / total) * 100);
                progressBar.style.width = percent + '%';
                progressPercentText.innerText = percent + '%';
                progressText.innerText = `目前進度: ${i + 1} / ${total} 封`;
            }

            // 5. 發送結束恢復按鈕
            progressText.innerText = `✅ 全部發送完成！成功處理 ${total} 封郵件。`;
            btn.disabled = false;
            btn.innerText = "開始寄送 🚀";
        }
    </script>

</body>
</html>