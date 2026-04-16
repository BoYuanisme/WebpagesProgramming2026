<?php
session_start();

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "teacher") {
    echo "<script>alert('權限不足，請重新登入！'); window.location.href='login.php';</script>";
    exit();
}

$myID = $_COOKIE["userID"] ?? "未知 ID";
?>
<!DOCTYPE html>
<html>
<head><title>教師專區</title></head>
<body style="background-color: #e6ffe6;">
    <h2>歡迎來到教師專區</h2>
    <p>您的專屬 ID (來自 Cookie)：<b><?php echo htmlspecialchars($myID); ?></b></p>
    
    <hr>
    <a href="logout.php"><button>登出並刪除 Cookie</button></a>
</body>
</html>