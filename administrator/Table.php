<?php
require_once('../helpers/tableNoDAO.php');
$tabledb = new TableNoDAO();

// POSTリクエストでテーブル状態の更新が送信されたかをチェック
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tableNo'])) {
    $tableNo = $_POST['tableNo'];
    $table = $tabledb->get_table_no_by_id($tableNo); // 指定されたテーブル番号でテーブル情報を取得
    if ($table) {
        // テーブルの呼び出し状態を更新（呼び出し状態を0に設定）
        $table->yobidasiState = 0;
        $tabledb->update_table_no($table); // データベース内のテーブル情報を更新
    }

    // 重定向到当前页面，避免刷新时重复提交表单
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

$tables = $tabledb->get_all_table_no(); // 全てのテーブル情報をデータベースから取得
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>テーブル状況</title>
    <link rel="stylesheet" href="CSS/TableStyle.css">
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
            // テーブルの状況を確認し表示
            $status = $table->yobidasiState === 1 ? "呼び出し中" : ($table->syokujistate === 1 ? "食事中" : "空席");
            $activeClass = $table->yobidasiState === 1 ? "active" : ""; // 呼び出し中の場合に"active"クラスを追加

            // テーブルの呼び出し状態が0の場合、リセットボタンを無効にする
            $disabledClass = $table->yobidasiState === 0 ? "disabled" : ""; // 呼び出し状態が0なら"disabled"クラスを追加

            // テーブルが空席の場合はリンク無効化と警告メッセージ表示
            if ($table->kaiinID == 0) {
                echo "<div class='table-box $activeClass' id='table{$table->tableNo}'>
                        <h2>テーブル {$table->tableNo}</h2>
                        <p id='status{$table->tableNo}'>状況: $status</p>
                        <form method='POST'>
                            <input type='hidden' name='tableNo' value='{$table->tableNo}'> <!-- 表格编号 -->
                            <button type='submit' class='reset-button $disabledClass' " . ($table->yobidasiState === 0 ? 'disabled' : '') . ">リセット</button> <!-- リセットボタン -->
                        </form>
                    </div>";
            } else {
                // テーブルが空席でない場合はリンク有効化
                echo "<a href='TableTop.php?tableNo={$table->tableNo}&kaiinID={$table->kaiinID}'>
                        <div class='table-box $activeClass' id='table{$table->tableNo}'>
                            <h2>テーブル {$table->tableNo}</h2>
                            <p id='status{$table->tableNo}'>状況: $status</p>
                            <form method='POST'>
                                <input type='hidden' name='tableNo' value='{$table->tableNo}'> <!-- 表格编号 -->
                                <button type='submit' class='reset-button $disabledClass' " . ($table->yobidasiState === 0 ? 'disabled' : '') . ">リセット</button> <!-- リセットボタン -->
                            </form>
                        </div>
                    </a>";
            }
        }
        ?>
    </div>

    <a href="Toppage.php">
        <button>戻る</button> <!-- 戻るボタン -->
    </a>
</body>
</html>
