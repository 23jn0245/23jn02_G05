<?php
session_start();
require_once '../helpers/tableNoDAO.php';

// セッションを開始する。既にセッションが開始されているか確認してから実行
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // セッションを開始
}



$tabledb = new TableNoDAO();
$tables = $tabledb->get_all_table_no(); // 全てのテーブル情報をデータベースから取得

// POSTリクエストでテーブル状態の更新が送信されたかをチェック
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tableNo'])) {
    $tableNo = $_POST['tableNo'];
    $kaiinID = $_SESSION['kaiinID'];
    // テーブル情報の更新
    $table = new TableNo();
    $table->tableNo = $tableNo;
    $table->yobidasiState = 0; // リセット
    $table->syokujistate = 1; // 食事中に変更
    $table->kaiinID = $kaiinID;

    $tabledb->update_table_no($table); // テーブル情報を更新

    // トップページへリダイレクト
    header('Location: Toppage.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>テーブル状況</title>
    <link rel="stylesheet" href="CSS/ChooseTableNoStyle.css">
    <style>
        /* 状態によるテーブルボックスの色変更 */
        .table-box { padding: 20px; border: 1px solid #ccc; margin: 10px; text-align: center; }
        .table-box.yellow { background-color: yellow; }
        .table-box.default { background-color: #f4f4f4; }
        .reset-button.disabled { background-color: gray; color: white; cursor: not-allowed; }
    </style>
</head>
<body>
    <header>
        テーブル状況
    </header>

    <!-- テーブル状況グリッド -->
    <div class="table-grid">
        <?php
        // 各テーブル情報を表示
        foreach ($tables as $table) {
            // テーブルの状態に基づいてクラスと状態を設定
            $status = $table->syokujistate === 1 ? "食事中" : "空席";
            $boxClass = $table->syokujistate === 1 ? "yellow" : "default"; // 色設定
            $buttonDisabled = $table->syokujistate === 1 ? "disabled" : ""; // ボタン制御

            echo "<div class='table-box $boxClass' id='table{$table->tableNo}'>";
            echo "<h2>テーブル {$table->tableNo}</h2>";
            echo "<p id='status{$table->tableNo}'>状況: $status</p>";

            // 選択するボタン、$table->syokujistate が 1 の場合は無効
            echo "<form method='POST'>";
            echo "<input type='hidden' name='tableNo' value='" . htmlspecialchars($table->tableNo) . "'>";
            echo "<button type='submit' class='reset-button $buttonDisabled' $buttonDisabled>選択する</button>";
            echo "</form>";

            echo "</div>";
        }
        ?>
    </div>
</body>
</html>
