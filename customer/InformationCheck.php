<?php
require_once '../helpers/KaiinDAO.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['kaiin'])) {
    $kaiin = $_SESSION['kaiin'];
    $kaiinID = $kaiin->kaiinID;
} else {
    header('Location: Login.php');
    exit();
}

// フォームからのデータを取得
$password = $_POST['password'];
$lastName = $_POST['lastName'];
$firstName = $_POST['firstName'];
$phone = $_POST['phoneNumber'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    $dao = new KaiinDAO();
    $result = $dao->update_kaiin($kaiinID, $phone, $password, $lastName, $firstName);

    if ($result) {
        header('Location: Toppage.php');
        exit();
    } else {
        $errorMessage = "情報の更新に失敗しました。再度試してください。";
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>情報変更確認画面</title>
    <link rel="stylesheet" href="CSS/InformationCheckStyle.css">
</head>
<body>
    <div class="container" id="confirmation-screen">
        <h2>お客様情報</h2>

        <div class="form-group">
            <label>姓</label>
            <input type="text" value="<?= htmlspecialchars($lastName); ?>" readonly>
        </div>
        <div class="form-group">
            <label>名</label>
            <input type="text" value="<?= htmlspecialchars($firstName); ?>" readonly>
        </div>
        <div class="form-group">
            <label>パスワード</label>
            <input type="password" value="<?= htmlspecialchars($password); ?>" readonly>
        </div>
        <div class="form-group">
            <label>電話番号</label>
            <input type="tel" value="<?= htmlspecialchars($phone); ?>" readonly>
        </div>

        <div class="button-container">
            <form method="POST" action="">
                <input type="hidden" name="password" value="<?= $password; ?>">
                <input type="hidden" name="lastName" value="<?= $lastName; ?>">
                <input type="hidden" name="firstName" value="<?= $firstName; ?>">
                <input type="hidden" name="phoneNumber" value="<?= $phone; ?>">
                <button type="submit" name="confirm" class="confirm-btn" onclick="return confirm('本当に更新しますか？')">確認</button>
                <button type="button" class="cancel-btn" onclick="location.href='Information.php'">キャンセル</button>
            </form>
        </div>

        <?php if (isset($errorMessage)): ?>
            <p style="color: red;"><?= $errorMessage; ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
