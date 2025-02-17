<?php 
session_start();
require_once '../helpers/tableNoDAO.php';

// セッションをクリア
$_SESSION = [];
$session_name = session_name();

if (isset($_COOKIE[$session_name])) {
    setcookie($session_name, '', time() - 3600); // クッキーを無効化
}

session_destroy();
header('Location: login.php'); // トップページへリダイレクト
exit;
