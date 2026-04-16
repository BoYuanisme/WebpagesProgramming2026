<?php
session_start();
$cookieID = isset($_COOKIE["userID"]) ? $_COOKIE["userID"] : "目前無紀錄";
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>系統登入</title>
</head>
<body>
    <h2>系統登入</h2>
    <p>目前 Cookie 紀錄的 ID：<b><?php echo htmlspecialchars($cookieID); ?></b></p>
    
    <form action="logincheck.php" method="post">
        帳號：<input type="text" name="user" required><br><br>
        密碼：<input type="password" name="pwd" required><br><br>
        <input type="submit" value="登入">
    </form>
    
    <hr>
    <p><b>測試帳號：</b><br>
    學生：student / 123<br>
    教師：teacher / 123<br>
    管理者：admin / 123</p>
</body>
</html>