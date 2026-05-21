<?php
require 'db.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

header('Content-Type: application/json');
$action = $_POST['action'] ?? '';

// A. 建構資料庫：新增 Email
if ($action === 'add_email') {
    $email = trim($_POST['email']);
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO emails (email) VALUES (:email)");
        $stmt->execute(['email' => $email]);
        
        // 關鍵修正：檢查到底有沒有真正新增成功
        if ($stmt->rowCount() > 0) {
            echo json_encode(['status' => 'success', 'msg' => '🎉 新增成功！']);
        } else {
            // 如果 rowCount 是 0，代表資料庫裡已經有了，被 IGNORE 掉了
            echo json_encode(['status' => 'success', 'msg' => '💡 此 Email 已經存在於名單中囉！']);
        }
    } else {
        echo json_encode(['status' => 'error', 'msg' => '❌ 無效的 Email 格式']);
    }
    exit;
}

// B-1. 獲取寄送名單 (全部 或 隨機幾筆)
if ($action === 'get_targets') {
    $mode = $_POST['mode'];
    if ($mode === 'all') {
        $stmt = $pdo->query("SELECT email FROM emails");
    } else {
        $count = (int)$_POST['count'];
        // 使用 ORDER BY RAND() 達成隨機撈取
        $stmt = $pdo->prepare("SELECT email FROM emails ORDER BY RAND() LIMIT :limit");
        $stmt->bindValue(':limit', $count, PDO::PARAM_INT);
        $stmt->execute();
    }
    $emails = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo json_encode(['status' => 'success', 'targets' => $emails]);
    exit;
}

// B-2. 執行單筆寄信
if ($action === 'send_single') {
    $targetEmail = $_POST['email'];
    $subject = $_POST['subject'];
    $body = $_POST['body'];

    $mail = new PHPMailer(true);
    try {
        $mail->CharSet = 'UTF-8';
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';             
        $mail->SMTPAuth   = true;
        // 以下為你的發信帳號設定 (沿用之前的資料)
        $mail->Username   = 'a1123356@mail.nuk.edu.tw';     
        $mail->Password   = 'iuej jotc ldkv ebys';          
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        $mail->setFrom('a1123356@mail.nuk.edu.tw', 'Spam System');
        $mail->addAddress($targetEmail);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = nl2br(htmlspecialchars($body));

        $mail->send();
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'msg' => $mail->ErrorInfo]);
    }
    exit;
}
?>