<?php
session_start();

// データベース接続情報
define('DSN', 'sqlsrv:server=10.32.97.1\\sotsu');
define('DB_USER', '23jn02_G05');
define('DB_PASSWORD', '23jn02_G05');

try {
    // データベースに接続
    $pdo = new PDO(DSN, DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("データベース接続失敗: " . $e->getMessage());
}

// テーブル番号と状態を取得
$tableNumber = isset($_POST['tableNumber']) ? $_POST['tableNumber'] : null;
$status = isset($_POST['status']) ? $_POST['status'] : null;

// テーブル番号と状態が有効か確認
if ($tableNumber !== null && $status !== null) {
    // SQL文を準備してテーブルの状態を更新
    $sql = "UPDATE table_status SET status = :status WHERE table_number = :table_number";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':status', $status, PDO::PARAM_STR);
    $stmt->bindParam(':table_number', $tableNumber, PDO::PARAM_INT);
    $stmt->execute();

    // 状態更新後、管理者ページにリダイレクト
    header('Location: administrator/Tabletest.php');
    exit();
} else {
    echo "無効な入力です。";
}
?>
