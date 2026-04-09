<?php
session_start();
?>
<h1>登入結果</h1>

<?php

$defaultName="nuk";
$defaultPwd="123456";
$userName=$_POST["userName"];
$userPwd=$_POST["userPwd"];

if($defaultName==$userName && $defaultPwd==$userPwd){
    echo "登入成功";
    $_SESSION["check"]=1;
    $cookiedate=strtotime("+10 seconds",time());
    setcookie("userName",$defaultName,$cookiedate);
    header("Location:form.php");
    exit();
}else{
    echo "登入失敗，將在3秒後重新導向到登入頁面";
    header("Refresh:3;url='login.php'");
    exit();
}
?>