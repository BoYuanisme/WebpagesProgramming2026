<?php
// 資料庫設定 (XAMPP 預設)
$host = '127.0.0.1';
$user = 'root'; 
$pass = '';     
$dbname = 'hw4_spam_system'; // 自訂資料庫名稱

try {
    $pdo = new PDO("mysql:host=$host;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 1. 自動建立資料庫
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;");
    $pdo->exec("USE `$dbname`;");
    
    // 2. 自動建立資料表 (欄位：No, email)
    $sql = "CREATE TABLE IF NOT EXISTS `emails` (
        `No` INT(11) AUTO_INCREMENT PRIMARY KEY,
        `email` VARCHAR(255) NOT NULL UNIQUE
    );";
    $pdo->exec($sql);
} catch (PDOException $e) {
    die("資料庫連線失敗: " . $e->getMessage());
}
?>