<?php 
require_once '../helpers/DenpyoDAO.php';
require_once '../helpers/KaiinDAO.php';
require_once '../helpers/OrdersDAO.php';

$errorMessage = '';
$isLoggedIn = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    $emailOrPhone = $_POST['txtMail'] ?? '';
    $password = $_POST['txtPassword'] ?? '';

    try {
        // DBから会員情報を取得
        $dao = new KaiinDAO();
        $member = $dao->get_member($emailOrPhone, $password);
      
        if ($member) {
            $kaiinID = $member->kaiinID;
            $denpyoDAO = new DenpyoDAO();
            $kaiinDAO = new KaiinDAO();
            $ordersDAO = new OrdersDAO();
            $denpyo = $denpyoDAO->get_denpyo_by_kaiinID($kaiinID);

            if ($denpyo) {
                echo "<script>alert('退会失败，管理者に連絡してください'); window.location.href='LogiN.php';</script>";
            } else {
                $kaiinDAO->delete_kaiin_by_id($kaiinID);
                $denpyoDAO->delete_denpyo_by_kaiinID($kaiinID);
                $ordersDAO->delete_orders_by_kaiinID($kaiinID);
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
    <title>退会ページ</title>
    <link rel="stylesheet" href="CSS/LogiNStyle.css">
    <script>
        function validateAndConfirm() {
            var email = document.getElementById('txtMail').value.trim();
            var password = document.getElementById('txtPassword').value.trim();
            
            if (email === '') {
                alert('Eメールを入力してください');
                document.getElementById('txtMail').focus();
                return false;
            }
            
            if (password === '') {
                alert('パスワードを入力してください');
                document.getElementById('txtPassword').focus();
                return false;
            }
            
            return confirm('本当に退会しますか？この操作は取り消せません。');
        }
    </script>
</head>
<body>
    <h1>退会ページ</h1>
    <?php if (!$isLoggedIn): ?>
    <div id="loginForm">
        <form action="" method="post" id="withdrawForm">
            <label for="txtMail">Eメール</label>
            <input type="text" id="txtMail" name="txtMail" placeholder="Eメールを入力してください" required>
            <label for="txtPassword">パスワード</label>
            <input type="password" id="txtPassword" name="txtPassword" placeholder="パスワードを入力してください" required>
            <small>半角英数字</small>
            <button type="submit" name="confirm" class="confirm-btn" onclick="return validateAndConfirm()">退会する</button>
            <button type="button" id="btnHome" onclick="location.href='LogiN.php'">戻る</button>
        </form>
        <?php if ($errorMessage): ?>
            <p style="color: red;"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <!-- ログイン成功画面 -->
    <div id="successScreen" class="success-container" style="display: block;">
        <h2>退会成功、ご利用いただき誠にありがとうございました。</h2>
        <button onclick="location.href='LogiN.php'">戻る</button>
    </div>
    <?php endif; ?>
</body>
</html>
