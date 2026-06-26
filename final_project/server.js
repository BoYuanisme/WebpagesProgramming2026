require('dotenv').config();
const express = require('express');
const mysql = require('mysql2/promise');
const bcrypt = require('bcrypt');
const session = require('express-session');
const cors = require('cors');
const path = require('path');
const nodemailer = require('nodemailer'); 
const { OAuth2Client } = require('google-auth-library');

const app = express();
app.use(express.json());
app.use(cors());

// 讓 Express 提供 public 資料夾底下的靜態網頁
app.use(express.static(path.join(__dirname, 'public')));

// 🌟 新增：信件防擾冷卻計時器 (記錄每個 user_id 最後一次寄信的時間戳記)
const emailCooldowns = new Map();

// 1. 設定 MySQL 資料庫連線池
const pool = mysql.createPool({
    host: 'localhost',
    user: 'root',         
    password: '', 
    database: 'oneirolab',
    waitForConnections: true,
    connectionLimit: 10,
    queueLimit: 0
});

// 🌟 5. 資料庫自動初始化 (確保資料表與新欄位存在)
async function initDB() {
    try {
        await pool.query(`
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(255) NOT NULL,
                password VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                role VARCHAR(50) DEFAULT 'dreamer',
                balance INT DEFAULT 100,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        `);
        await pool.query(`
            CREATE TABLE IF NOT EXISTS dreams (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT,
                content TEXT,
                sentiment VARCHAR(50),
                sentiment_score FLOAT,
                is_public BOOLEAN DEFAULT FALSE,
                ai_analysis TEXT,
                image_url VARCHAR(255),
                status VARCHAR(50) DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        `);
        
        // 自動嘗試加入擴充欄位 (若舊表存在但缺欄位時使用)
        const columnsToAdd = [
            'is_public BOOLEAN DEFAULT FALSE',
            'ai_analysis TEXT',
            'image_url VARCHAR(255)',
            'status VARCHAR(50) DEFAULT "active"'
        ];
        
        for (let colDef of columnsToAdd) {
            try {
                const colName = colDef.split(' ')[0];
                await pool.query(`ALTER TABLE dreams ADD COLUMN ${colDef}`);
                console.log(`[DB] 成功擴增欄位: ${colName}`);
            } catch (e) {
                // 如果報錯通常是因為欄位已存在，直接忽略即可
            }
        }
        console.log('✅ 資料庫結構檢測與初始化完成');
    } catch (err) {
        console.error('❌ 資料庫初始化錯誤:', err);
    }
}
initDB();

// 2. 設定 Session 
app.use(session({
    secret: 'oneirolab-super-secret-key', 
    resave: false,
    saveUninitialized: false,
    cookie: { secure: false, maxAge: 1000 * 60 * 60 * 24 }
}));

// 🔑 3. Groq AI 金鑰與設定
const AI_API_KEY = process.env.AI_API_KEY; 
const AI_URL = "https://api.groq.com/openai/v1/chat/completions";
const AI_MODEL = "llama-3.1-8b-instant"; 

// 📧 4. 寄信設定
const MY_EMAIL = process.env.MY_EMAIL; 
const MY_PASSWORD = process.env.MY_PASSWORD; 

// 填入你剛剛在 Google 後台拿到的那一長串 Client ID
const GOOGLE_CLIENT_ID = process.env.GOOGLE_CLIENT_ID; 
const googleClient = new OAuth2Client(GOOGLE_CLIENT_ID);

// ==========================================
// API 路由區域 - 帳號與基礎功能
// ==========================================

// [API] 註冊 (🌟 支援接收前端的 role 角色選擇)
app.post('/api/register', async (req, res) => {
    const { username, password, email, role } = req.body; 
    if (!username || !password || !email) {
        return res.status(400).json({ success: false, message: '請完整提供帳號、信箱與密碼' });
    }
    try {
        const [existing] = await pool.query('SELECT id FROM users WHERE username = ? OR email = ?', [username, email]);
        if (existing.length > 0) {
            return res.status(400).json({ success: false, message: '此帳號或 Email 已被註冊過囉！' });
        }

        const hashedPassword = await bcrypt.hash(password, 10);
        const userRole = (role === 'analyst') ? 'analyst' : 'dreamer';
        const initBalance = userRole === 'dreamer' ? 100 : 0; 

        await pool.query(
            'INSERT INTO users (username, password, email, role, balance) VALUES (?, ?, ?, ?, ?)', 
            [username, hashedPassword, email, userRole, initBalance]
        );
        res.json({ success: true, message: '註冊成功' });
    } catch (err) {
        console.error(err);
        res.status(500).json({ success: false, message: '伺服器錯誤' });
    }
});

// [API] 登入
app.post('/api/login', async (req, res) => {
    const { username, password } = req.body;
    try {
        const [rows] = await pool.query('SELECT * FROM users WHERE username = ?', [username]);
        if (rows.length === 0) return res.status(401).json({ success: false, message: '帳號或密碼錯誤' });

        const user = rows[0];
        const match = await bcrypt.compare(password, user.password);
        if (!match) return res.status(401).json({ success: false, message: '帳號或密碼錯誤' });

        req.session.userId = user.id;
        res.json({ 
            success: true, 
            user: { id: user.id, username: user.username, role: user.role, balance: user.balance } 
        });
    } catch (err) {
        console.error(err);
        res.status(500).json({ success: false, message: '伺服器錯誤' });
    }
});

// [API] Google 快捷登入與自動註冊
app.post('/api/google-login', async (req, res) => {
    const { token, role } = req.body;
    if (!token) {
        return res.status(400).json({ success: false, message: '缺少 Google 驗證憑證' });
    }

    try {
        const ticket = await googleClient.verifyIdToken({
            idToken: token,
            audience: GOOGLE_CLIENT_ID,
        });
        const payload = ticket.getPayload();
        const { email, name } = payload;

        const [rows] = await pool.query('SELECT * FROM users WHERE email = ?', [email]);
        let user;

        if (rows.length === 0) {
            const placeholderPassword = await bcrypt.hash(Math.random().toString(36), 10);
            const userRole = role || 'dreamer'; 
            const initBalance = 100; 

            let username = name || email.split('@')[0];
            const [userCheck] = await pool.query('SELECT id FROM users WHERE username = ?', [username]);
            if (userCheck.length > 0) {
                username = `${username}_${Math.floor(Math.random() * 1000)}`;
            }

            const [insertResult] = await pool.query(
                'INSERT INTO users (username, password, email, role, balance) VALUES (?, ?, ?, ?, ?)', 
                [username, placeholderPassword, email, userRole, initBalance]
            );
            
            user = { id: insertResult.insertId, username: username, role: userRole, balance: initBalance };
            console.log(`🆕 [Google 註冊] 成功建立 [${userRole}] 帳號！`);
        } else {
            user = rows[0];
            console.log(`👋 [Google 登入] 歡迎 ${user.username} 回來！`);
        }

        req.session.userId = user.id;
        res.json({
            success: true, message: 'Google 登入成功',
            user: { id: user.id, username: user.username, role: user.role, balance: user.balance }
        });

    } catch (err) {
        console.error("❌ Google 登入驗證失敗:", err);
        res.status(401).json({ success: false, message: 'Google 驗證未通過' });
    }
});

// [API] 登出
app.post('/api/logout', (req, res) => {
    req.session.destroy();
    res.json({ success: true, message: '已登出' });
});

// [API] 線上儲值
app.post('/api/payment', async (req, res) => {
    const { user_id, amount } = req.body;
    try {
        await pool.query('UPDATE users SET balance = balance + ? WHERE id = ?', [amount, user_id]);
        const [rows] = await pool.query('SELECT balance FROM users WHERE id = ?', [user_id]);
        res.json({ success: true, newBalance: rows[0].balance });
    } catch (err) {
        res.status(500).json({ success: false, message: '儲值失敗' });
    }
});

// ==========================================
// API 路由區域 - 夢境與擴充功能 (Dreamer)
// ==========================================

// [API] 紀錄夢境 (混合式 Groq AI + 規則引擎 + 郵件預警)
app.post('/api/dreams', async (req, res) => {
    const { user_id, content } = req.body;
    
    try {
        const [userRows] = await pool.query('SELECT username, email, balance FROM users WHERE id = ?', [user_id]);
        if (userRows.length === 0 || userRows[0].balance < 10) {
            return res.status(400).json({ success: false, message: '餘額不足' });
        }
        const currentUser = userRows[0];

        let sentiment = "中性";
        let score = 0.0;
        let isRuleMatched = false;

        const scoreMatch = content.match(/(\d+)\s*分/);
        if (scoreMatch) {
            const examScore = parseInt(scoreMatch[1], 10);
            if (examScore >= 0 && examScore <= 100) {
                isRuleMatched = true;
                if (examScore === 60) { sentiment = "中性"; score = 0.00; } 
                else if (examScore < 60) { sentiment = "負向"; score = parseFloat(((examScore - 60) / 60).toFixed(2)); } 
                else { sentiment = "正向"; score = parseFloat(((examScore - 60) / 40).toFixed(2)); }
            }
        }

        if (!isRuleMatched) {
            try {
                const prompt = `你是一位專業的心理學分析師。請精確分析以下夢境的情緒感受與強度：「${content}」。
                請直接回傳純 JSON 格式，不要包含任何 Markdown 標記，格式如下：
                {"sentiment": "正向" 或是 "負向" 或是 "中性", "score": 數字介於-1到1}`;

                const response = await fetch(AI_URL, {
                    method: 'POST',
                    headers: { 'Authorization': `Bearer ${AI_API_KEY}`, 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        model: AI_MODEL,
                        messages: [{ role: 'user', content: prompt }],
                        response_format: { type: "json_object" },
                        temperature: 0.0 
                    })
                });

                const data = await response.json();
                if (data.choices && data.choices[0].message.content) {
                    let aiText = data.choices[0].message.content.trim();
                    if (aiText.startsWith("```")) { aiText = aiText.replace(/^```json\s*/i, "").replace(/```$/, "").trim(); }
                    const aiResult = JSON.parse(aiText);
                    sentiment = aiResult.sentiment || "中性";
                    score = parseFloat(aiResult.score) || 0.0;
                } else { throw new Error("AI 回傳格式不正確"); }
            } catch (aiError) {
                sentiment = "中性"; score = 0.0;
            }
        }

        const finalBalance = Math.max(0, currentUser.balance - 10);
        await pool.query('UPDATE users SET balance = ? WHERE id = ?', [finalBalance, user_id]);
        
        await pool.query(
            'INSERT INTO dreams (user_id, content, sentiment, sentiment_score) VALUES (?, ?, ?, ?)',
            [user_id, content, sentiment, score]
        );

        // 郵件防擾機制
        if (sentiment === "負向") {
            const now = Date.now();
            const lastSent = emailCooldowns.get(user_id) || 0;
            const COOLDOWN_TIME = 10 * 60 * 1000; 

            if (now - lastSent > COOLDOWN_TIME) {
                try {
                    let transporter = nodemailer.createTransport({
                        service: 'gmail', auth: { user: MY_EMAIL, pass: MY_PASSWORD }
                    });
                    
                    let mailOptions = {
                        from: `"Oneirolab 心靈實驗室" <${MY_EMAIL}>`,
                        to: currentUser.email || MY_EMAIL,
                        subject: `【潛意識預警】${currentUser.username}，您的 AI 夢境健康關懷週報`,
                        html: `<div style="padding:20px;background:#1e293b;color:#fff;border-radius:10px;">
                                <h2 style="color:#f43f5e;">🧠 潛意識健康通知</h2>
                                <p>親愛的 ${currentUser.username}：AI 系統偵測到您記錄了一則帶有壓力的夢境。</p>
                                <p><strong>夢境摘要：</strong> ${content}</p>
                                <p>請記得適度放鬆休息，心靈實驗室關心您！</p>
                               </div>`
                    };
                    
                    await transporter.sendMail(mailOptions);
                    emailCooldowns.set(user_id, now); 
                } catch (mailErr) {
                    console.error("✉️ 郵件寄送失敗:", mailErr.message);
                }
            }
        }

        res.json({ success: true, newBalance: finalBalance });
    } catch (err) {
        console.error("❌ 伺服器內部錯誤:", err);
        res.status(500).json({ success: false, message: '伺服器內部錯誤' });
    }
});

// [API] 讀取個人夢境列表
app.get('/api/dreams', async (req, res) => {
    const { user_id, search } = req.query;
    try {
        let query = "SELECT * FROM dreams WHERE user_id = ? AND status != 'deleted' ORDER BY created_at DESC";
        let params = [user_id];
        if (search) { 
            query = "SELECT * FROM dreams WHERE user_id = ? AND content LIKE ? AND status != 'deleted' ORDER BY created_at DESC"; 
            params.push(`%${search}%`); 
        }

        const [rows] = await pool.query(query, params);
        res.json(rows);
    } catch (err) { res.status(500).json({ error: '無法讀取資料' }); }
});

// [擴充 API] 取得集體意識公共地圖資料
app.get('/api/dreams/public', async (req, res) => {
    try {
        // 抓取設定為公開，且未被刪除/隱藏的夢境
        const [rows] = await pool.query(`
            SELECT d.id, d.content, d.sentiment, d.sentiment_score, d.created_at 
            FROM dreams d 
            WHERE d.is_public = TRUE AND d.status = 'active'
            ORDER BY d.created_at DESC LIMIT 50
        `);
        res.json(rows);
    } catch (err) {
        res.status(500).json({ error: '無法讀取公共地圖' });
    }
});

// [擴充 API] 開關夢境公開狀態
app.post('/api/dreams/:id/toggle-public', async (req, res) => {
    const dreamId = req.params.id;
    try {
        const [dream] = await pool.query('SELECT is_public FROM dreams WHERE id = ?', [dreamId]);
        if (dream.length === 0) return res.status(404).json({ success: false });
        
        const newValue = !dream[0].is_public;
        await pool.query('UPDATE dreams SET is_public = ? WHERE id = ?', [newValue, dreamId]);
        res.json({ success: true, is_public: newValue });
    } catch (err) {
        res.status(500).json({ success: false });
    }
});

// [擴充 API] AI 深度解構 (佛洛伊德與榮格)
app.post('/api/dreams/:id/analyze-deep', async (req, res) => {
    const dreamId = req.params.id;
    try {
        const [rows] = await pool.query('SELECT content, ai_analysis FROM dreams WHERE id = ?', [dreamId]);
        if (rows.length === 0) return res.status(404).json({ success: false });

        if (rows[0].ai_analysis) {
            return res.json({ success: true, analysis: rows[0].ai_analysis });
        }

        const content = rows[0].content;
        const prompt = `你是一位精通佛洛伊德(Freud)與榮格(Jung)精神分析的 AI 心理學家。請針對以下夢境：「${content}」
        進行深度解析。請包含：
        1. 佛洛伊德視角（潛意識壓抑與慾望）
        2. 榮格視角（集體潛意識與原型符號）
        請用繁體中文，並以溫暖、專業的語氣回答，不超過 200 字。`;

        const response = await fetch(AI_URL, {
            method: 'POST',
            headers: { 'Authorization': `Bearer ${AI_API_KEY}`, 'Content-Type': 'application/json' },
            body: JSON.stringify({
                model: AI_MODEL,
                messages: [{ role: 'user', content: prompt }],
                temperature: 0.5 
            })
        });

        const data = await response.json();
        const analysisText = data.choices[0].message.content.trim();

        await pool.query('UPDATE dreams SET ai_analysis = ? WHERE id = ?', [analysisText, dreamId]);
        res.json({ success: true, analysis: analysisText });
    } catch (err) {
        res.status(500).json({ success: false, message: 'AI 深度分析失敗' });
    }
});

// [擴充 API] 產生夢境視覺相片 (模擬)
app.post('/api/dreams/:id/generate-image', async (req, res) => {
    const dreamId = req.params.id;
    try {
        const [rows] = await pool.query('SELECT image_url FROM dreams WHERE id = ?', [dreamId]);
        if (rows.length === 0) return res.status(404).json({ success: false });

        if (rows[0].image_url) {
            return res.json({ success: true, image_url: rows[0].image_url });
        }
        
        // 為了避免真實生圖 API 的高延遲與費用，我們在此使用 Picsum 動態隨機圖片結合 seed 來模擬「專屬夢境圖」
        const mockImageUrl = `https://picsum.photos/seed/dream_${dreamId}/600/400`;
        await pool.query('UPDATE dreams SET image_url = ? WHERE id = ?', [mockImageUrl, dreamId]);
        
        res.json({ success: true, image_url: mockImageUrl });
    } catch (err) {
        res.status(500).json({ success: false });
    }
});


// ==========================================
// API 路由區域 - 後台監控與擴充功能 (Analyst)
// ==========================================

// [API] 基礎儀表板數據
app.get('/api/analyst/dashboard', async (req, res) => {
    try {
        const [summary] = await pool.query('SELECT sentiment, COUNT(*) as count FROM dreams WHERE status = "active" GROUP BY sentiment');
        const [timeline] = await pool.query('SELECT created_at, sentiment_score FROM dreams WHERE status = "active" ORDER BY created_at DESC LIMIT 20');
        // 倒轉回來讓圖表時間軸向右遞增
        res.json({ summary, timeline: timeline.reverse() });
    } catch (err) { res.status(500).json({ error: '無法生成儀表板' }); }
});

// [擴充 API] 集體情緒天氣預報
app.get('/api/analyst/weather', async (req, res) => {
    try {
        // 取最近 30 筆夢境情緒均值
        const [rows] = await pool.query('SELECT AVG(sentiment_score) as avgScore, COUNT(*) as count FROM dreams WHERE status = "active"');
        const avgScore = rows[0].avgScore || 0;
        
        let weather = '多雲 (平靜)';
        let emoji = '⛅';
        let desc = '全網情緒穩定，潛意識流動和緩。';
        
        if (avgScore > 0.3) { weather = '晴空萬里'; emoji = '☀️'; desc = '群體情緒高漲，充滿正向能量與創造力。'; }
        else if (avgScore < -0.3) { weather = '雷陣雨'; emoji = '⛈️'; desc = '偵測到社會群體潛在焦慮或壓力波動，建議關注。'; }
        
        res.json({ weather, emoji, desc, avgScore, totalDreams: rows[0].count });
    } catch (err) {
        res.status(500).json({ error: '無法生成天氣預報' });
    }
});

// [擴充 API] 社會事件關鍵詞關聯
app.get('/api/analyst/keywords', async (req, res) => {
    const { keyword } = req.query;
    if (!keyword) return res.json({ count: 0, avgScore: 0, matches: [] });
    
    try {
        const [rows] = await pool.query(
            'SELECT id, content, sentiment, sentiment_score FROM dreams WHERE content LIKE ? AND status = "active" LIMIT 10',
            [`%${keyword}%`]
        );
        const count = rows.length;
        const avgScore = count > 0 ? rows.reduce((acc, curr) => acc + curr.sentiment_score, 0) / count : 0;
        
        res.json({ count, avgScore, matches: rows });
    } catch (err) {
        res.status(500).json({ error: '關鍵字搜尋失敗' });
    }
});

// [擴充 API] 使用者與內容審核管理 (列表)
app.get('/api/analyst/moderation', async (req, res) => {
    try {
        const [rows] = await pool.query(`
            SELECT d.id, u.username, d.content, d.sentiment, d.is_public, d.status, d.created_at 
            FROM dreams d 
            LEFT JOIN users u ON d.user_id = u.id 
            ORDER BY d.created_at DESC 
            LIMIT 50
        `);
        res.json(rows);
    } catch (err) {
        res.status(500).json({ error: '無法讀取審核資料' });
    }
});

// [擴充 API] 隱藏/刪除違規內容
app.post('/api/analyst/moderation/:id/hide', async (req, res) => {
    try {
        const dreamId = req.params.id;
        await pool.query('UPDATE dreams SET status = "deleted", is_public = FALSE WHERE id = ?', [dreamId]);
        res.json({ success: true });
    } catch (err) {
        res.status(500).json({ success: false });
    }
});

const PORT = 3000;
app.listen(PORT, () => console.log(`🚀 Oneirolab 智慧雙引擎伺服器已在 http://localhost:${PORT} 啟動！`));