<?php 
session_start();
require_once '../helpers/tableNoDAO.php';

// セッションを開始する。既にセッションが開始されているか確認してから実行
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // セッションを開始
}

// セッションから会員情報を取得
if (isset($_SESSION['kaiinID']) && is_object($_SESSION['kaiin'])) {
    // $_SESSION['kaiin'] がオブジェクトであることを確認
    $kaiin = $_SESSION['kaiinID'];
    $tabledb = new TableNoDAO();
    $table = $tabledb->get_kaiin_no_by_id($kaiin); // 会員IDに基づいてテーブル情報を取得

    if ($table) { // テーブル情報が存在する場合のみ処理
        $table->kaiinID = 0; // 会員IDを0にリセット
        $table->yobidasiState = 0; // 呼び出し状態をリセット
        $table->syokujistate = 0; // 食事状態をリセット
        $tabledb->update_table_no($table); // テーブル情報を更新
    }
} else {
    // セッションが無効な場合のデフォルト処理（例えば、ログインページへリダイレクト）
    // header("Location: LogiN.php");
    exit;
}

// セッションをクリア
$_SESSION = [];
$session_name = session_name();

if (isset($_COOKIE[$session_name])) {
    setcookie($session_name, '', time() - 3600); // クッキーを無効化
}

session_destroy();
header('Location: Toppage.php'); // トップページへリダイレクト
exit;
