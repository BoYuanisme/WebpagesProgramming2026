<?php
session_start();

if (!isset($_SESSION["check"]) || $_SESSION["check"] !== 1) {
    echo "<script>alert('請先登入！'); window.location.href='login.php';</script>";
    exit();
}

$nameErr = $emailErr = $phoneErr = $genderErr = $birthdayErr = $cityErr = $typeErr = $familyErr = $pwdErr = "";

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

    if (
        $nameErr == "" && $phoneErr == "" && $emailErr == "" &&
        $genderErr == "" && $birthdayErr == "" && $cityErr == "" &&
        $typeErr == "" && $familyErr == "" && $pwdErr == ""
    ) {
        $_SESSION['success_data'] = $_POST;
        
        header("Location: result.php");
        exit();
    }
}
?>

<html>

<head>
    <title>夏令營報名表</title>
    <style>
        .error {
            color: #FF0000;
        }
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

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <table width="100%" border="0" cellpadding="10">
            <tr>
                <td width="60%" valign="top">

                    姓名: <input type="text" name="name" value="<?php echo $_POST['name'] ?? ''; ?>">
                    <span class="error">* <?php echo $nameErr; ?></span>
                    <br><br>

                    電話: <input type="text" name="phone" value="<?php echo $_POST['phone'] ?? ''; ?>">
                    <span class="error">* <?php echo $phoneErr; ?></span>
                    <br><br>

                    電子郵件: <input type="email" name="email" value="<?php echo $_POST['email'] ?? ''; ?>">
                    <span class="error">* <?php echo $emailErr; ?></span>
                    <br><br>

                    性別:
                    <input type="radio" name="gender" value="male" <?php if (isset($_POST['gender']) && $_POST['gender'] == "male") echo "checked"; ?>>男
                    <input type="radio" name="gender" value="female" <?php if (isset($_POST['gender']) && $_POST['gender'] == "female") echo "checked"; ?>>女
                    <input type="radio" name="gender" value="secret" <?php if (isset($_POST['gender']) && $_POST['gender'] == "secret") echo "checked"; ?>>不方便透露
                    <span class="error">* <?php echo $genderErr; ?></span>
                    <br><br>

                    生日:
                    <input type="date" name="birthday" value="<?php echo $_POST['birthday'] ?? ''; ?>">
                    <span class="error">* <?php echo $birthdayErr; ?></span>
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
                    <span class="error">* <?php echo $cityErr; ?></span>
                    <br><br>

                    想參加的營隊類型:
                    <input type="checkbox" name="type[]" value="basketball" <?php if (isset($_POST['type']) && in_array("basketball", $_POST['type'])) echo "checked"; ?>> 籃球
                    <input type="checkbox" name="type[]" value="baseball" <?php if (isset($_POST['type']) && in_array("baseball", $_POST['type'])) echo "checked"; ?>> 棒球
                    <input type="checkbox" name="type[]" value="soccer" <?php if (isset($_POST['type']) && in_array("soccer", $_POST['type'])) echo "checked"; ?>> 足球
                    <input type="checkbox" name="type[]" value="badminton" <?php if (isset($_POST['type']) && in_array("badminton", $_POST['type'])) echo "checked"; ?>> 羽球
                    <input type="checkbox" name="type[]" value="swim" <?php if (isset($_POST['type']) && in_array("swim", $_POST['type'])) echo "checked"; ?>> 游泳
                    <input type="checkbox" name="type[]" value="music" <?php if (isset($_POST['type']) && in_array("music", $_POST['type'])) echo "checked"; ?>> 音樂
                    <span class="error">* <?php echo $typeErr; ?></span>
                    <br><br>

                    同行親友陪同人數 (0~5人):
                    <input type="number" name="family_count" min="0" max="5" value="<?php echo $_POST['family_count'] ?? '0'; ?>"> 人
                    <span class="error">* <?php echo $familyErr; ?></span>
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
                    <span class="error">* <?php echo $pwdErr; ?></span>
                    <br><br>

                    營隊 T-shirt 想要的顏色:
                    <input type="color" name="tshirt_color" value="<?php echo $_POST['tshirt_color'] ?? '#1266b5'; ?>">
                    <br><br>

                    備註:<br>
                    <textarea name="message" rows="4" cols="50"><?php echo $_POST['message'] ?? ''; ?></textarea>
                    <br><br>

                </td>

                <td width="40%" valign="top" align="center">

                    <h3>💰 夏令營費用參考表</h3>

                    <table border="1" cellpadding="8" cellspacing="0" width="90%">
                        <tr bgcolor="#1266b5">
                            <th><font color="white">營隊項目</font></th>
                            <th><font color="white">基本費用</font></th>
                            <th><font color="white">備註</font></th>
                        </tr>

                        <tr>
                            <td align="center">🏀 籃球營</td>
                            <td align="center">$ 3,500</td>
                            <td align="center">含球衣一套</td>
                        </tr>
                        <tr bgcolor="#f2f2f2">
                            <td align="center">⚾ 棒球營</td>
                            <td align="center">$ 4,200</td>
                            <td align="center">需自備手套</td>
                        </tr>
                        <tr>
                            <td align="center">⚽ 足球營</td>
                            <td align="center">$ 3,800</td>
                            <td align="center">含專屬護具</td>
                        </tr>
                        <tr bgcolor="#f2f2f2">
                            <td align="center">🏸 羽球營</td>
                            <td align="center">$ 3,200</td>
                            <td align="center">含室內場地費</td>
                        </tr>
                        <tr>
                            <td align="center">🏊 游泳營</td>
                            <td align="center">$ 4,500</td>
                            <td align="center">含個人泳帽</td>
                        </tr>
                        <tr bgcolor="#f2f2f2">
                            <td align="center">🎵 音樂營</td>
                            <td align="center">$ 5,000</td>
                            <td align="center">含樂器租借費</td>
                        </tr>
                        <tr>
                            <td colspan="3" bgcolor="#ff8000">
                                <font color="white"><b>💡 親友陪同：每人加收 $ 500 餐費</b></font>
                            </td>
                        </tr>
                    </table>

                </td>
            </tr>
        </table>
        <center>
            <input type="button" value="重新填寫" onclick="window.location.href=window.location.pathname">
            <input type="submit" value="提交報名表">
        </center>
        <a name="bottom"></a>
    </form>

</body>

</html>