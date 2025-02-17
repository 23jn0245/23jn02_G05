<?php
// KaiinDAO クラスをインクルード（データベース操作用）
require_once '../helpers/KaiinDAO.php';

// セッションを開始する。既にセッションが開始されているか確認してから実行
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // セッションを開始
}

// セッションから会員情報を取得
if (isset($_SESSION['kaiin']) && is_object($_SESSION['kaiin'])) {
    // $_SESSION['kaiin'] がオブジェクトであることを確認
    $kaiin = $_SESSION['kaiin'];
} else {
    // セッションが無効な場合のデフォルト処理（例えば、ログインページへリダイレクト）
    header("Location: LogiN.php");
    exit;
}

// 必要なデータがない場合に備えてデフォルト値を設定
$phone = isset($kaiin->tel) ? $kaiin->tel : '未登録';
$email = isset($kaiin->EMail) ? $kaiin->EMail : '未登録';
$name = isset($kaiin->sei) && isset($kaiin->mei) ? $kaiin->sei . ' ' . $kaiin->mei : '未登録';
?>

<!DOCTYPE html>
<html lang="ja"> <!-- ページの言語設定を日本語に指定 -->
<head>
    <meta charset="UTF-8"> <!-- 文字エンコードをUTF-8に設定 -->
    <title>マイページ</title> <!-- ページタイトルを設定 -->
    <link rel="stylesheet" href="CSS/MypageStyle.css"> <!-- 外部CSSファイルをリンク -->
</head>
<body>
    <h1>マイページ</h1> <!-- 見出しで「マイページ」を表示 -->
    <div class="container"> <!-- ユーザー情報を表示するためのコンテナ -->
        <div class="info"> <!-- ユーザーの名前を表示するセクション -->
            <label for="userName">名前</label> <!-- 名前のラベル -->
            <input type="text" id="userName" name="userName" 
                   value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>" readonly> <!-- ユーザー名を表示する入力欄（readonly） -->
        </div>
        <div class="info"> <!-- 電話番号を表示するセクション -->
            <label for="userPhone">電話番号</label> <!-- 電話番号のラベル -->
            <input type="tel" id="userPhone" name="userPhone" 
                   value="<?php echo htmlspecialchars($phone, ENT_QUOTES, 'UTF-8'); ?>" readonly> <!-- ユーザーの電話番号を表示する入力欄（readonly） -->
        </div>
        <div class="info"> <!-- メールアドレスを表示するセクション -->
            <label for="userEmail">Email</label> <!-- メールアドレスのラベル -->
            <input type="email" id="userEmail" name="userEmail" 
                   value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>" readonly> <!-- ユーザーのメールアドレスを表示する入力欄（readonly） -->
        </div>
        
        <div class="button-row"> <!-- ボタンを配置する行 -->
            <button type="button" onclick="location.href='Information.php'">情報編集</button> <!-- 情報編集ボタン：クリックすると情報編集ページへ遷移 -->
        </div>
        <div class="buttons"> <!-- 戻るボタンのセクション -->
            <button type="button" class="btnBack" onclick="location.href='Toppage.php'">戻る</button> <!-- 戻るボタン：クリックするとトップページへ遷移 -->
        </div>
    </div>
</body>
</html>
