<?php
// フォームのPOSTデータを受け取ってサニタイズ（セキュリティ対策）
$tel = isset($_POST['tel']) ? htmlspecialchars($_POST['tel'], ENT_QUOTES, 'UTF-8') : ''; // 電話番号の入力データ
$email = isset($_POST['email']) ? htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8') : ''; // メールアドレスの入力データ
$password = isset($_POST['password']) ? htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8') : ''; // パスワードの入力データ
$lastName = isset($_POST['lastName']) ? htmlspecialchars($_POST['lastName'], ENT_QUOTES, 'UTF-8') : ''; // 姓の入力データ
$firstName = isset($_POST['firstName']) ? htmlspecialchars($_POST['firstName'], ENT_QUOTES, 'UTF-8') : ''; // 名の入力データ

// 登録処理が成功したかどうかを示す変数
$registrationSuccess = true;
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>新規登録 - 完了</title>
    <link rel="stylesheet" href="CSS/RegistrationCompleteStyle.css"> <!-- CSSファイルの読み込み -->
</head>
<body>
    <h1>ご登録ありがとうございます</h1> <!-- 登録完了の見出し -->

    <div class="complete-message">
        <?php if ($registrationSuccess): ?> <!-- 登録が成功した場合 -->
            <p>アカウント作成が完了しました。</p> <!-- 成功メッセージ -->
            <p>「ホームへ」ボタンをクリックしてサービスをご利用ください。</p> <!-- 次のステップの案内 -->
        <?php else: ?> <!-- 登録に失敗した場合 -->
            <p>登録に失敗しました。もう一度お試しください。</p> <!-- 失敗メッセージ -->
        <?php endif; ?>

        <!-- ホームページへ戻るボタン -->
        <button type="button" id="btnHome" onclick="location.href='Toppage.php'">ホームへ</button>
    </div>
</body>
</html>
