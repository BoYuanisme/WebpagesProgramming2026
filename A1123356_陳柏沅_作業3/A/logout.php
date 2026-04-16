<?php
session_start();

$_SESSION = array();
session_destroy();

setcookie("userID", "", time() - 3600, "/");

echo "<script>alert('已成功登出，並刪除 Cookie 紀錄！'); window.location.href='login.php';</script>";
exit();
?>