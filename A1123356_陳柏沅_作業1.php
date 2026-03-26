<?php
$nameErr=$emailErr=$phoneErr=$genderErr=$birthdayErr=$cityErr=$typeErr=$familyErr=$pwdErr="";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (empty($_POST["name"])) {
        $nameErr = "姓名是必填欄位！"; 
    }
    if (empty($_POST["phone"])) { 
        $phoneErr = "電話是必填欄位！"; 
    }
    if (empty($_POST["email"])) { 
        $emailErr = "電子郵件是必填欄位！"; 
    }
    if (empty($_POST["gender"])) { 
        $genderErr = "請選擇性別！"; 
    }
    if (empty($_POST["birthday"])) {
        $birthdayErr = "請選擇生日！";
    }
    if (empty($_POST["city"])) {
        $cityErr = "請選擇營隊地點！";
    }
    if (empty($_POST["type"])) {
        $typeErr = "請至少選擇一個營隊類型！";
    } elseif (count($_POST["type"]) > 3) {
        $typeErr = "最多只能選擇三個營隊類型！";
    }
    if (!isset($_POST["family_count"]) || $_POST["family_count"] === "") {
        $familyErr = "請填寫同行人數！";
    }
    if (empty($_POST["pwd"])) {
        $pwdErr = "請設定查詢密碼！";
    }
}
?>

<html>
    <head>
        <title>夏令營報名表</title>
        <style>
            .error {color: #FF0000;}
        </style>
    </head>
    <body>
        <center>
            <h1>
                <font size="7">夏令營報名表</font><br><br>
                <font size="4"><i>活動負責人：BoYuan</i></font>
            </h1>
        </center>
        <hr width="100%">
        <p align="right"><a href="#bottom">前往頁尾</a></p>
        <p><span class="error">* 必填欄位</span></p>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">

            姓名: <input type="text" name="name" value="<?php echo $_POST['name'] ?? ''; ?>">
            <span class="error">* <?php echo $nameErr;?></span>
            <br><br>

            電話: <input type="text" name="phone" value="<?php echo $_POST['phone'] ?? ''; ?>">
            <span class="error">* <?php echo $phoneErr;?></span>
            <br><br>

            電子郵件: <input type="email" name="email" value="<?php echo $_POST['email'] ?? ''; ?>">
            <span class="error">* <?php echo $emailErr;?></span>
            <br><br>

            性別:
            <input type="radio" name="gender" value="male" <?php if (isset($_POST['gender']) && $_POST['gender'] == "male") echo "checked"; ?>>男
            <input type="radio" name="gender" value="female" <?php if (isset($_POST['gender']) && $_POST['gender'] == "female") echo "checked"; ?>>女
            <input type="radio" name="gender" value="secret" <?php if (isset($_POST['gender']) && $_POST['gender'] == "secret") echo "checked"; ?>>不方便透露
            <span class="error">* <?php echo $genderErr;?></span>
            <br><br>

            生日: 
            <input type="date" name="birthday" value="<?php echo $_POST['birthday'] ?? ''; ?>">
            <span class="error">* <?php echo $birthdayErr;?></span>
            <br><br>

            營隊地點:
            <select name="city">
                <option value="">請選擇地點...</option>
                <option value="taipei" <?php if (isset($_POST['city']) && $_POST['city'] == "taipei") echo "selected"; ?>>台北</option>
                <option value="taoyuan" <?php if (isset($_POST['city']) && $_POST['city'] == "taoyuan") echo "selected"; ?>>桃園</option>
                <option value="hsinchu" <?php if (isset($_POST['city']) && $_POST['city'] == "hsinchu") echo "selected"; ?>>新竹</option>
                <option value="taichung" <?php if (isset($_POST['city']) && $_POST['city'] == "taichung") echo "selected"; ?>>台中</option>
                <option value="chiayi" <?php if (isset($_POST['city']) && $_POST['city'] == "chiayi") echo "selected"; ?>>嘉義</option>
                <option value="tainan" <?php if (isset($_POST['city']) && $_POST['city'] == "tainan") echo "selected"; ?>>台南</option>
                <option value="kaohsiung" <?php if (isset($_POST['city']) && $_POST['city'] == "kaohsiung") echo "selected"; ?>>高雄</option>
            </select>
            <span class="error">* <?php echo $cityErr;?></span>
            <br><br>

            想參加的營隊類型:
            <input type="checkbox" name="type[]" value="basketball" <?php if (isset($_POST['type']) && in_array("basketball", $_POST['type'])) echo "checked"; ?>> 籃球
            <input type="checkbox" name="type[]" value="baseball" <?php if (isset($_POST['type']) && in_array("baseball", $_POST['type'])) echo "checked"; ?>> 棒球
            <input type="checkbox" name="type[]" value="soccer" <?php if (isset($_POST['type']) && in_array("soccer", $_POST['type'])) echo "checked"; ?>> 足球
            <input type="checkbox" name="type[]" value="badminton" <?php if (isset($_POST['type']) && in_array("badminton", $_POST['type'])) echo "checked"; ?>> 羽球
            <input type="checkbox" name="type[]" value="swim" <?php if (isset($_POST['type']) && in_array("swim", $_POST['type'])) echo "checked"; ?>> 游泳
            <input type="checkbox" name="type[]" value="music" <?php if (isset($_POST['type']) && in_array("music", $_POST['type'])) echo "checked"; ?>> 音樂
            <span class="error">* <?php echo $typeErr;?></span>
            <br><br>

            同行親友陪同人數 (0~5人): 
            <input type="number" name="family_count" min="0" max="5" value="<?php echo $_POST['family_count'] ?? '0'; ?>"> 人
            <span class="error">* <?php echo $familyErr;?></span>
            <br><br>

            對本次營隊的期待程度 (1~10分): 
            <input type="range" name="expectation" min="1" max="10" value="<?php echo $_POST['expectation'] ?? '10'; ?>" style="accent-color: #ff8000;" oninput="document.getElementById('expect_val').innerText = this.value">
            <span id="expect_val" style="color: #ff8000; font-weight: bold; font-size: 20px;">
                <?php echo $_POST['expectation'] ?? '10'; ?>
            </span> 分
            <br><br>

            方便聯絡的時間: 
            <input type="time" name="contact_time" value="<?php echo $_POST['contact_time'] ?? ''; ?>">
            <br><br>

            設定報名查詢密碼: 
            <input type="password" id="myPassword" name="pwd" value="<?php echo $_POST['pwd'] ?? ''; ?>">
            <button type="button" onclick="var pwd = document.getElementById('myPassword'); if(pwd.type === 'password') { pwd.type = 'text'; this.innerText = '😑'; } else { pwd.type = 'password'; this.innerText = '😮'; }">😮</button>
            <span class="error">* <?php echo $pwdErr;?></span>
            <br><br>

            營隊 T-shirt 想要的顏色: 
            <input type="color" name="tshirt_color" value="<?php echo $_POST['tshirt_color'] ?? '#1266b5'; ?>">
            <br><br>

            備註:<br>
            <textarea name="message" rows="4" cols="50"><?php echo $_POST['message'] ?? ''; ?></textarea>
            <br><br>

            <center>
                <input type="button" value="重新填寫" onclick="window.location.href=window.location.pathname">
                <input type="submit" value="提交報名表">
            </center>
            <a name="bottom"></a>
        </form>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST" && 
            $nameErr == "" && $phoneErr == "" && $emailErr == "" && 
            $genderErr == "" && $birthdayErr == "" && $cityErr == "" && 
            $typeErr == "" && $familyErr == "" && $pwdErr == "") {
            
            echo "<hr width='100%'>";
            echo "<center><h2>🎉 報名成功！以下是您提交的資料：</h2></center>";
            
            echo "<div style='font-size: 20px; margin-left: 20px;'>";
            echo "<b>姓名：</b> " . $_POST["name"] . "<br><br>";
            echo "<b>電話：</b> " . $_POST["phone"] . "<br><br>";
            echo "<b>電子郵件：</b> " . $_POST["email"] . "<br><br>";
            
            $gender_show = "";
            if ($_POST["gender"] == "male") $gender_show = "男";
            if ($_POST["gender"] == "female") $gender_show = "女";
            if ($_POST["gender"] == "secret") $gender_show = "不方便透露";
            echo "<b>性別：</b> " . $gender_show . "<br><br>";
            
            echo "<b>生日：</b> " . $_POST["birthday"] . "<br><br>";
            echo "<b>營隊地點：</b> " . $_POST["city"] . "<br><br>";
            
            echo "<b>營隊類型：</b> " . join(", ", $_POST["type"]) . "<br><br>";
            
            echo "<b>同行親友：</b> " . ($_POST["family_count"] ?? "0") . " 人<br><br>";
            echo "<b>期待程度：</b> " . ($_POST["expectation"] ?? "10") . " 分<br><br>";
            echo "<b>方便聯絡時間：</b> " . ($_POST["contact_time"] ?? "未填寫") . "<br><br>";
            
            $pwd_show = !empty($_POST["pwd"]) ? "******** (已設定)" : "未設定";
            echo "<b>查詢密碼：</b> " . $pwd_show . "<br><br>";
            
            $color = $_POST["tshirt_color"] ?? "#ff0000";
            echo "<b>T-shirt 顏色：</b> <span style='background-color: $color; padding: 0 20px; border: 1px solid #000;'>&nbsp;</span> ($color)<br><br>";

            echo "<b>備註：</b> " . ($_POST["message"] ?? "無") . "<br><br>";
            echo "</div>";
        }
        ?>
    </body>
</html>