<?php
// フォームがPOST送信された場合
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // フォームの質問1と質問2の回答を取得。もし値がセットされていない場合は空文字を設定。
    $question1 = isset($_POST['question1']) ? $_POST['question1'] : ''; // 質問1の回答
    $question2 = isset($_POST['question2']) ? $_POST['question2'] : ''; // 質問2の回答
    $remarks = isset($_POST['txtBikou']) ? $_POST['txtBikou'] : ''; // 備考欄の入力値
    // 送信後に表示する感謝メッセージを設定
    $thank_you_message = "アンケートにご協力いただき、ありがとうございます。";
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>アンケート</title>
    <link rel="stylesheet" href="CSS/SurveYstyle.css"> <!-- 外部CSSファイルを読み込み -->
</head>
<body>

    <!-- フォームが送信されていない場合、アンケートフォームを表示 -->
    <?php if (!isset($thank_you_message)): ?>
        <h1>アンケート</h1>
        <div class="container" id="survey-container">
            <!-- アンケートフォーム -->
            <form method="post" action="">
                <!-- 質問1: 料理の味について -->
                <div class="question">1.料理の味はどうでしたか？</div>
                <div class="options">
                    <!-- ラジオボタンで回答を選択 -->
                    <label><input type="radio" name="question1" value="非常に満足" required> 非常に満足</label>
                    <label><input type="radio" name="question1" value="満足"> 満足</label>
                    <label><input type="radio" name="question1" value="普通"> 普通</label>
                    <label><input type="radio" name="question1" value="不満"> 不満</label>
                    <label><input type="radio" name="question1" value="非常に不満"> 非常に不満</label>
                </div>

                <!-- 質問2: スタッフの接客について -->
                <div class="question">2.スタッフの接客はどうでしたか？</div>
                <div class="options">
                    <!-- ラジオボタンで回答を選択 -->
                    <label><input type="radio" name="question2" value="非常に満足" required> 非常に満足</label>
                    <label><input type="radio" name="question2" value="満足"> 満足</label>
                    <label><input type="radio" name="question2" value="普通"> 普通</label>
                    <label><input type="radio" name="question2" value="不満"> 不満</label>
                    <label><input type="radio" name="question2" value="非常に不満"> 非常に不満</label>
                </div>

                <!-- 備考欄 -->
                <div class="question">備考</div>
                <textarea id="txtBikou" name="txtBikou" placeholder="備考を入力してください" rows="5"></textarea>

                <!-- 送信ボタン -->
                <button type="submit">送信</button>
                <!-- 戻るボタン（マイページへ遷移） -->
                <button type="button" class="back-button" onclick="location.href='MypagE.php'">戻る</button>
            </form>
        </div>

    <?php else: ?>
        <!-- 送信後に感謝のメッセージを表示 -->
        <div id="thank-you-message" class="thank-you">
            <?php echo $thank_you_message; ?> <!-- 感謝メッセージ -->
            <br>
            <!-- 戻るボタン（マイページへ遷移） -->
            <button class="back-button" onclick="location.href='MypagE.php'">戻る</button>
        </div>
    <?php endif; ?>

</body>
</html>
