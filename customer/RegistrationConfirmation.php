<?php 
require_once '../helpers/KaiinDAO.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $lastName = $_POST['lastName'];
    $firstName = $_POST['firstName'];
    $phoneNumber = $_POST['phoneNumber'];

    $kaiin = new Kaiin();
    $kaiin->sei = $lastName;
    $kaiin->mei = $firstName;
    $kaiin->EMail = $email;
    $kaiin->tel = $phoneNumber;
    $kaiin->psword = $password;

    $dao = new KaiinDAO();
    $result = $dao->create_kaiin([
        'sei' => $kaiin->sei,
        'mei' => $kaiin->mei,
        'EMail' => $kaiin->EMail,
        'tel' => $kaiin->tel,
        'psword' => $kaiin->psword
    ]);

    if ($result) {
        $successMessage = "登録が成功しました。以下の情報をご確認ください。";
    } else {
        $errorMessage = "エラー:Emailがすでに登録されています"; // エラーメッセージ
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>情報登録確認</title>
    <link rel="stylesheet" href="CSS/RegistrationConfirmationStyle22.css">
    
</head>
<body>
    <div class="container">
        <h1>情報登録確認</h1>
        <?php if (isset($errorMessage)): ?>
            <p class="error-message"><?php echo htmlspecialchars($errorMessage); ?></p>
            <!-- 戻るボタン -->
            <button class="return-btn" onclick="location.href='Registration.php'">戻る</button>
        <?php elseif (isset($successMessage)): ?>
            <p class="success-message"><?php echo htmlspecialchars($successMessage); ?></p>
            <div class="info">
                <p>以下の情報を登録しました。</p>
                <ul>
                    <li>メールアドレス: <?php echo htmlspecialchars($email); ?></li>
                    <li>電話番号: <?php echo htmlspecialchars($phoneNumber); ?></li>
                    <li>姓: <?php echo htmlspecialchars($lastName); ?></li>
                    <li>名: <?php echo htmlspecialchars($firstName); ?></li>
                </ul>
            </div>
            <div class="button-container">
                <button type="button" class="confirm-btn" onclick="location.href='LogiN.php'">確定</button> <!-- 使用 JavaScript 実現跳转 -->
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
