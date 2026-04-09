<?php

if(isset($_COOKIE["userName"])){
    echo "Welcome back, ".$_COOKIE["userName"];
}

?>
<h1>請登入以填寫夏令營報名表單</h1>
<form action="logincheck.php" method="post">
請輸入您的使用者名稱: <input type="text" name="userName"><br><br>
請輸入您的密碼: <input type="password" name="userPwd"><br><br>
<input type="submit"><br><br>

<?php
date_default_timezone_set("Asia/Taipei");
echo "Time now: " . date("Y-m-d H:i:s");
//header("Refresh:1");
?>

</form>