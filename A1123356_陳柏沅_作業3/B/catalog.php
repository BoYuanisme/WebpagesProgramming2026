<?php
session_start();  // 啟用交談期

// 檢查是否是表單送回
if ( isset($_POST["Item"]) ) {
    // 取得購買的數量
    $_SESSION["Quantity"] = $_POST["Quantity"];
    $id = $_POST["Item"];  // 取得選擇商品
    $_SESSION["ID"] = $id; // 建立Session變數
    
    switch (strtoupper($id)) {
        case "S001":
            $_SESSION["Name"] = "10吋平板電腦";
            $_SESSION["Price"] = 12000;
            break;
        case "S002":
            $_SESSION["Name"] = "15.6吋筆記型電腦";
            $_SESSION["Price"] = 27000;
            break;
        case "S003":
            $_SESSION["Name"] = "iPhone智慧型手機";
            $_SESSION["Price"] = 21000;
            break;
    }
    header("Location: savecart.php");  // 轉址
}
?>