<?php
require_once '../helpers/ShainDAO.php';

// セッション開始
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// セッションから会員情報を取得
if (isset($_SESSION['shain']) && is_object($_SESSION['shain'])) {
    $shain = $_SESSION['shain'];
} else {
    header("Location: LogiN.php");
    exit;
}

// 既存の情報を取得（デフォルト値を設定）
$phone = isset($shain->phone_number) ? $shain->phone_number : '未登録';
$sei = isset($shain->sei) ? $shain->sei : '未登録';
$mei = isset($shain->mei) ? $shain->mei : '未登録';
//onsubmit="return validateForm(event)"
// <script>
//         function validateForm(event) {
//             let valid = true;
//             document.querySelectorAll("input").forEach(input => {
//                 if (!input.checkValidity()) {
//                     input.nextElementSibling.style.display = "block";
//                     valid = false;
//                 } else {
//                     input.nextElementSibling.style.display = "none";
//                 }
//             });

//             return valid;
//         }
//     </script>
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>情報編集</title>
    <link rel="stylesheet" href="CSS/InformationEditStyle.css">
    <style>
        input:invalid { border-color: red; }
        button:disabled { background-color: #ddd; cursor: not-allowed; }
        input.blur-textbox { color: gray; }
    </style>
</head>
<body>
    <h1>情報編集</h1>

    <form id="editForm" class="container" action="InformationCheck.php" method="POST" >
        <div class="section">
            <h2>連絡情報</h2>
            <div class="form-group">
                <label for="phoneNumber">電話番号</label>
                <input type="text" id="phoneNumber" name="phoneNumber" value="<?= htmlspecialchars($phone); ?>" class="blur-textbox" required pattern="^\d{2,4}-\d{2,4}-\d{4}$">
                <div class="error-message" id="phoneError">電話番号(例:XXX-XXXX-XXXX)を入力してください。</div>
            </div>
        
            <div class="form-group">
                <label for="password">パスワード</label>
                <input type="password" id="password" name="password" placeholder="パスワードを入力してください" required pattern=".{8,}">
                <div class="error-message" id="passwordError">パスワードは最低8文字以上で入力してください。</div>
            </div>
        </div>

        <div class="section">
            <h2>管理者情報</h2>
            <div class="form-group">
                <label for="lastName">姓</label>
                <input type="text" id="lastName" name="lastName" value="<?= htmlspecialchars($sei); ?>" class="blur-textbox" required pattern="^[ぁ-んァ-ヶ一-龯]+$">
                <div class="error-message" id="lastNameError">姓は漢字、ひらがな、カタカナで入力してください。</div>
            </div>
            <div class="form-group">
                <label for="firstName">名</label>
                <input type="text" id="firstName" name="firstName" value="<?= htmlspecialchars($mei); ?>" class="blur-textbox" required pattern="^[ぁ-んァ-ヶ一-龯]+$">
                <div class="error-message" id="firstNameError">名は漢字、ひらがな、カタカナで入力してください。</div>
            </div>
        </div>

        <div class="button-container">
            <button type="submit" class="confirm-btn">確認</button>
            <button type="button" class="cancel-btn" onclick="location.href='Mypage.php'">戻る</button>
        </div>
    </form>

    
</body>
</html>
