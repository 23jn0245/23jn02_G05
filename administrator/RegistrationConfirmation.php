<?php 
require_once '../helpers/ShainDAO.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $lastName = $_POST['lastName'];
    $firstName = $_POST['firstName'];
    $phoneNumber = $_POST['phoneNumber'];

    $Shain = new Shain();
    $Shain->sei = $lastName;
    $Shain->mei = $firstName;
    $Shain->EMail = $email;
    $Shain->phone_number = $phoneNumber;
    $Shain->password = $password;

    $dao = new ShainDAO();
    $result = $dao->create_shain([
        'sei' => $Shain->sei,
        'mei' => $Shain->mei,
        'EMail' => $Shain->EMail,
        'phone_number' => $Shain->phone_number,
        'password' => $Shain->password
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
    <link rel="stylesheet" href="CSS/RegistrationConfirmationStyle11.css">
    
</head>
<body>
    <div class="container">
        <h1>情報登録確認</h1>
        <?php if (isset($errorMessage)): ?>
            <p class="error-message"><?php echo htmlspecialchars($errorMessage); ?></p>
            <!-- 戻るボタン -->
            <button class="return-btn" onclick="location.href='Register.php'">戻る</button>
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
                <button type="button" class="confirm-btn" onclick="location.href='Login.php'">確定</button> <!-- 使用 JavaScript 実現跳转 -->
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
