<?php
session_start();

$user = $_POST["user"] ?? "";
$pwd = $_POST["pwd"] ?? "";

$users = [
    "student" => ["123", "student", "S-1001"],
    "teacher" => ["123", "teacher", "T-2001"],
    "admin" => ["123", "admin", "A-3001"]
];

if (isset($users[$user]) && $users[$user][0] === $pwd) {
    $role = $users[$user][1];
    $userID = $users[$user][2];

    $_SESSION["role"] = $role;
    
    setcookie("userID", $userID, time() + 3600, "/");
    
    header("Location: {$role}.php");
    exit();
} else {
    echo "<script>alert('帳號或密碼錯誤！'); window.location.href='login.php';</script>";
    exit();
}
?>