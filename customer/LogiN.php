<?php
session_start();
require_once '../helpers/KaiinDAO.php';
require_once '../helpers/tableNoDAO.php';
$errorMessage = '';
$isLoggedIn = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emailOrPhone = $_POST['txtMail'] ?? '';
    $password = $_POST['txtPassword'] ?? '';
    try {
        // DBから会員情報を取得
        $dao = new KaiinDAO();
        $member = $dao->get_member($emailOrPhone, $password);
      
        if ($member) {
            $_SESSION['kaiin'] = $member;
            $_SESSION['kaiinID'] = $member->kaiinID;//后面有时间改成用户自己选择
            $tdao = new tableNoDAO();
            if($tdao->get_kaiin_no_by_id($member->kaiinID)){
                header('location:Toppage.php');
            }
            $isLoggedIn = true;
        } else {
            // 会員情報が不正な場合
            $errorMessage = 'Eメールまたはパスワードが間違っています。';
        }
    } catch (Exception $e) {
        // 例外が発生した場合の処理
        error_log("ログイン処理中にエラーが発生しました: " . $e->getMessage());
        $errorMessage = 'ログイン処理中にエラーが発生しました。後ほど再度お試しください。';
    }
   
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>会員ログイン</title>
    <link rel="stylesheet" href="CSS/LogiNStyle.css">
</head>
<body>
    <h1>会員ログイン</h1>
    
    <?php if (!$isLoggedIn): ?>
    <div id="loginForm">
        <form action="" method="post">
            <label for="txtMail">Eメール</label>
            <input type="text" id="txtMail" name="txtMail" placeholder="Eメールを入力してください" required>

            <label for="txtPassword">パスワード</label>
            <input type="password" id="txtPassword" name="txtPassword" placeholder="パスワードを入力してください" required>
            <small>半角英数字</small>

            <button type="submit" id="btnLogin">ログイン</button>
            <button type="button" id="btnNew" onclick="location.href='Registration.php'">新規登録の方</button>
            <button type="button" id="btnCMenber" onclick="location.href='Cancelmembership.php'">退会の方</button>
            <button type="button" id="btnHome" onclick="location.href='Toppage.php'">ホームへ</button>
        </form>
        <?php if ($errorMessage): ?>
            <p style="color: red;"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <!-- ログイン成功画面 -->
    <div id="successScreen" class="success-container" style="display: block;">
        <h2>ログイン成功しました！</h2>
        <button onclick="location.href='ChooseTableNo.php'">テーブル番号を選んでください</button>
    </div>
    <?php endif; ?>
</body>
</html>
