<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者ダッシュボード</title>
    <link rel="stylesheet" href="CSS/ToppageStyle.css">
</head>
<body>
    <div class="logo">
        <!-- ロゴ画像を表示 -->
        <img src="../image/logo2.png" alt="会社ロゴ">
    </div>

    <div class="container">
        <div class="menu">
            <?php
            // ボタンリスト定義
            $menuItems = [
                ["ホール", "Halldetail.php"],
                ["キッチン", "Kitchendetail.php"],
                ["マイページ", "mypage.php"],
                ["テーブル図", "Table.php"],
                ["ログアウト", "LogOut.php"],
                ["メニュー一覧", "MenuIchiran.php"]
            ];

            // 各ボタンを動的に生成
            foreach ($menuItems as $item) {
                echo '<a href="' . $item[1] . '">';
                echo '<button>' . $item[0] . '</button>';
                echo '</a>';
            }
            ?>
        </div>
    </div>
</body>
</html>
