<?php
require_once '../helpers/goodsDAO.php';
require_once '../helpers/GoodsGroupDAO.php';
// DAOオブジェクトの初期化
$goodsGroupADO = new GoodsGroupDAO();

// 商品グループリストを取得
$groups = $goodsGroupADO->get_goodsgroup();
// 配列の長さを取得
$groupCount = count($groups);
// 成功メッセージ初期化
$successMessage = "";

// 入力データを受け取る
$productName = $_POST['productName'] ?? null;
$productPrice = $_POST['productPrice'] ?? null;
$productImage = $_FILES['productImage']['name'] ?? null;
$groupCode = $_POST['groupCode'] ?? null;
$productDescription = $_POST['productDescription'] ?? null;

// 商品DAOを初期化して商品を追加
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $productName && $productPrice && $productImage && $groupCode && $productDescription) {
    // 単価が負の数の場合、エラーメッセージを表示
    if ($productPrice < 0) {
        $successMessage = "単価は負の数にできません。";
    } 
    // グループコードが1から$groupCountの間でない場合、エラーメッセージを表示
    elseif ($groupCode < 1 || $groupCode > $groupCount) {
        $successMessage = "グループコードは1から$groupCountの間で入力してください。";
    } else {
        $uploadDir = '../image/'; // 画像保存先のディレクトリ
        $uploadFilePath = $uploadDir . basename($productImage); // ../image/写真.jpg の形式でファイルパスを生成 basename($_FILES['productImage']['name'])

        // 画像を指定のディレクトリにアップロード
        if (move_uploaded_file($_FILES['productImage']['tmp_name'], $uploadFilePath)) {
            $goodsdb = new GoodsDAO();
            // データベースにはファイル名のみを保存
            $goodsdb->add_goods($productName, $productPrice, basename($productImage), $groupCode, $productDescription);
            $successMessage = "商品が正常に追加されました！";

            // 成功メッセージを表示するために、リロード後に成功メッセージを保持
            // header()によるリダイレクトを使用して、フォームの再送信を防止し、ページをリロード
            header("Location: " . $_SERVER['PHP_SELF'] . "?success=" . urlencode($successMessage));
            exit;
        } else {
            $successMessage = "画像のアップロードに失敗しました。";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>商品追加</title>
    <link rel="stylesheet" href="CSS/MenuEditStyle.css">
    <script>
        // ファイル選択時に画像をプレビューするスクリプト
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function () {
                const preview = document.getElementById('productImagePreview');
                preview.src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }

        // フォーム入力検証スクリプト
        function validateForm() {
            let isValid = true;
            const productName = document.getElementById('productName');
            const productPrice = document.getElementById('productPrice');
            const productImage = document.getElementById('productImageInput');
            const groupCode = document.getElementById('groupCode');
            const productDescription = document.getElementById('productDescription');

            // 必須フィールドを検証
            if (!productName.value.trim()) {
                alert("商品名を入力してください！");
                isValid = false;
            } else if (!productPrice.value.trim() || productPrice.value < 0) {
                alert("単価は負の数にできません！");
                isValid = false;
            } else if (!productImage.files.length) {
                alert("写真を選択してください！");
                isValid = false;
            } else if (!groupCode.value.trim() || groupCode.value < 1 || groupCode.value > <?=$groupCount?>) {
                alert("グループコードは1から<?=$groupCount?>の間で入力してください！");
                isValid = false;
            } else if (!productDescription.value.trim()) {
                alert("説明を入力してください！");
                isValid = false;
            }
            return isValid;
        }
    </script>
</head>
<body>
    <div class="container">
        <img src="../image/logo2.png" alt="ロゴ画像" class="logo">
        <h2>商品追加</h2>

        <?php if (isset($_GET['success'])): ?>
            <!-- 成功メッセージがセットされている場合、表示 -->
            <div class="success-message"><?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>

        <form method="post" action="" enctype="multipart/form-data" onsubmit="return validateForm()">
            <div>
                <label class="label" for="productName">商品名:</label>
                <input type="text" id="productName" name="productName" class="input-field" required>
            </div>

            <!-- 商品画像選択とプレビュー -->
            <div>
                <label class="label">写真:</label>
                <input type="file" id="productImageInput" name="productImage" class="input-field" accept="image/*" onchange="previewImage(event)" required>
                <img src="" alt="商品画像" id="productImagePreview"> <!-- プレビュー画像を表示 -->
            </div>

            <div>
                <label class="label" for="productDescription">説明:</label>
                <textarea id="productDescription" name="productDescription" class="input-field" rows="4" required></textarea>
            </div>

            <div>
                <label class="label" for="groupCode">グループコード:</label>
                <input type="number" id="groupCode" name="groupCode" class="input-field" placeholder="グループコード:1-<?=$groupCount?>を入力してください" required>
            </div>

            <div>
                <label class="label" for="productPrice">単価:</label>
                <input type="number" id="productPrice" name="productPrice" class="input-field" required>
            </div>

            <div>
                <button class="button" type="button" onclick="location.href='MenuIchiran.php'">戻る</button>
                <button class="button" type="submit">追加</button>
            </div>
        </form>
    </div>
</body>
</html>
