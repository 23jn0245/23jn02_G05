<?php
require_once '../helpers/ShainDAO.php'; // DAOクラスをインクルード

// ログイン処理を実行する関数
define('ERROR_MESSAGE', 'ログインIDまたはパスワードが正しくありません。'); // エラーメッセージ定義

function login($loginID, $password) {
    $shainDAO = new ShainDAO();

    // 社員データを取得
    $shain = $shainDAO->login($loginID, $password);
    if ($shain) {
        return $shain; // ログイン成功時は社員データを返す
    }
    return null; // ログイン失敗時はnullを返す
}

// POSTリクエスト処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginID = $_POST['login-id'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($loginID) || empty($password)) {
        $errorMessage = 'ログインIDとパスワードを入力してください。';
    } else {
        $shain = login($loginID, $password);
        if ($shain) {
            // ログイン成功
            session_start();
            $_SESSION['shain'] = $shain;
            $_SESSION['shainID'] = $shain->shainID;
            $_SESSION['sei'] = $shain->sei;
            $_SESSION['mei'] = $shain->mei;
            header('Location: toppage.php');
            exit;
        } else {
            $errorMessage = ERROR_MESSAGE;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8"> <!-- ページの文字コードをUTF-8に設定 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- モバイルデバイスに適応するためのビューポート設定 -->
    <title>ログイン画面</title> <!-- ページタイトル -->
    <link rel="stylesheet" href="CSS/loginStyle.css"> <!-- 外部CSSファイルのリンク -->
</head>
<body>
    <!-- ログインフォームコンテナ -->
    <div class="login-container" id="loginForm">
        <h2>ログイン画面</h2>

        <?php if (!empty($errorMessage)): ?>
            <p style="color: red;"> <?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?> </p>
        <?php endif; ?>
        
        <form method="post" action="">
            <!-- ログインID入力欄 -->
            <label for="login-id">ログインID</label>
            <input type="text" id="login-id" name="login-id" placeholder="ログインIDを入力してください" required>

            <!-- パスワード入力欄 -->
            <label for="password">パスワード</label>
            <input type="password" id="password" name="password" placeholder="パスワードを入力してください" required>

            <!-- ログインボタン -->
            <button type="submit" id="loginButton">ログイン</button>
        </form>

        <!-- 新規登録リンク -->
        <div class="register-link">
            <a href="register.php">
                <button type="button">新規登録</button>
            </a>
        </div>
    </div>
</body>
</html>