<?php
session_start();

if (!isset($_SESSION['success_data'])) {
    header("Location: form.php"); 
    exit();
}

$data = $_SESSION['success_data'];
?>

<html>
<head>
    <title>報名結果</title>
</head>
<body>
    <hr width='100%'>
    <center><h2>🎉 報名成功！以下是您提交的資料：</h2></center>

    <div style='font-size: 20px; margin-left: 20px;'>
        <b>姓名：</b> <?php echo htmlspecialchars($data["name"]); ?><br><br>
        <b>電話：</b> <?php echo htmlspecialchars($data["phone"]); ?><br><br>
        <b>電子郵件：</b> <?php echo htmlspecialchars($data["email"]); ?><br><br>

        <?php
        $gender_show = "";
        if ($data["gender"] == "male") $gender_show = "男";
        if ($data["gender"] == "female") $gender_show = "女";
        if ($data["gender"] == "secret") $gender_show = "不方便透露";
        ?>
        <b>性別：</b> <?php echo $gender_show; ?><br><br>

        <b>生日：</b> <?php echo htmlspecialchars($data["birthday"]); ?><br><br>
        
        <?php
        $city_map = [
            "taipei" => "台北", "taoyuan" => "桃園", "hsinchu" => "新竹",
            "taichung" => "台中", "chiayi" => "嘉義", "tainan" => "台南", "kaohsiung" => "高雄"
        ];
        $city_show = $city_map[$data["city"]] ?? $data["city"];
        ?>
        <b>營隊地點：</b> <?php echo $city_show; ?><br><br>

        <?php
        $type_map = [
            "basketball" => "籃球", "baseball" => "棒球", "soccer" => "足球",
            "badminton" => "羽球", "swim" => "游泳", "music" => "音樂"
        ];
        $type_shows = [];
        foreach ($data["type"] as $t) {
            $type_shows[] = $type_map[$t] ?? $t;
        }
        ?>
        <b>營隊類型：</b> <?php echo htmlspecialchars(join("、", $type_shows)); ?><br><br>

        <b>同行親友：</b> <?php echo htmlspecialchars($data["family_count"] ?? "0"); ?> 人<br><br>
        <b>期待程度：</b> <?php echo htmlspecialchars($data["expectation"] ?? "10"); ?> 分<br><br>
        <b>方便聯絡時間：</b> <?php echo htmlspecialchars($data["contact_time"] ?? "未填寫"); ?><br><br>

        <?php
        $pwd_show = !empty($data["pwd"]) ? "******** (已設定)" : "未設定";
        ?>
        <b>查詢密碼：</b> <?php echo $pwd_show; ?><br><br>

        <?php
        $color = $data["tshirt_color"] ?? "#1266b5";
        ?>
        <b>T-shirt 顏色：</b> <span style='background-color: <?php echo $color; ?>; padding: 0 20px; border: 1px solid #000;'>&nbsp;</span> (<?php echo htmlspecialchars($color); ?>)<br><br>

        <b>備註：</b> <?php echo nl2br(htmlspecialchars($data["message"] ?? "無")); ?><br><br>
        
        <br>
        <button onclick="window.location.href='form.php'">回表單</button>
    </div>
</body>
</html>