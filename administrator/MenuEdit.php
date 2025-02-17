<?php
require_once '../helpers/goodsDAO.php';
require_once '../helpers/GoodsGroupDAO.php';
// DAOオブジェクトの初期化
$goodsGroupADO = new GoodsGroupDAO();

// 商品グループリストを取得
$groups = $goodsGroupADO->get_goodsgroup();
// 配列の長さを取得
$groupCount = count($groups);
// GETパラメータから商品情報を取得
$goodscode = $_GET['goodsCode'] ?? '';
$name = $_GET['name'] ?? '';
$price = $_GET['price'] ?? '';
$detail = $_GET['detail'] ?? '';
$groupCode = $_GET['groupCode'] ?? '';
$img = $_GET['img'] ?? '';

// POSTリクエストで送信されたデータを処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // フォームから送信されたデータを取得
    $productName = $_POST['productName'] ?? $name; // 商品名
    $productPrice = $_POST['productPrice'] ?? $price; // 単価
    // 商品画像が新たにアップロードされていない場合、元の画像を使用
    $productImage = !empty($_FILES['productImage']['tmp_name']) ? $_FILES['productImage']['name'] : $img; // 商品画像
    $productDescription = $_POST['productDescription'] ?? $detail; // 商品説明
    $productGroupCode = $_POST['groupCode'] ?? $groupCode; // グループコード

    // 画像がアップロードされた場合、サーバーに保存
    if (!empty($_FILES['productImage']['tmp_name'])) {
        $uploadDir = '../image/'; // アップロードディレクトリ
        $uploadedFilePath = $uploadDir . basename($_FILES['productImage']['name']);
        move_uploaded_file($_FILES['productImage']['tmp_name'], $uploadedFilePath);
        $productImage = $uploadedFilePath; // 画像パスを更新
    }

    // 入力検証
    if ($productPrice < 0) {
        echo "<script>alert('単価は負の数にできません。');</script>";
        $updateResult = false;
    } elseif ($productGroupCode < 1 || $productGroupCode > $groupCount) {
        echo "<script>alert('グループコードは1から$groupCountの間で入力してください。');</script>";
        $updateResult = false;
    } else {
        // 商品情報を更新
        $goodsdb = new GoodsDAO();
        $updateResult = $goodsdb->updategoods($goodscode, $productName, $productPrice, $productImage, $productGroupCode, $productDescription);

        // 更新成功時にメッセージを表示
        if ($updateResult) {
            header("Location: ?goodsCode=$goodscode&name=$productName&price=$productPrice&img=$productImage&groupCode=$productGroupCode&detail=$productDescription&success=商品情報を更新しました！");
            exit;
        } else {
            echo "<script>alert('商品情報の更新に失敗しました。');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>商品情報入力</title>
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
            const groupCode = document.getElementById('groupCode');
            const productDescription = document.getElementById('productDescription');

            // 必須フィールドを検証
            if (!productName.value.trim()) {
                alert("商品名を入力してください！");
                isValid = false;
            } else if (!productPrice.value.trim() || productPrice.value < 0) {
                alert("単価は負の数にできません！");
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
        <h2>商品情報編集</h2>

        <!-- 成功メッセージ -->
        <?php if (isset($_GET['success'])): ?>
            <div class="success-message"><?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>

        <!-- 商品編集フォーム -->
        <form method="post" action="" enctype="multipart/form-data" onsubmit="return validateForm()">
            <div>
                <label class="label" for="productName">商品名:</label>
                <input type="text" id="productName" name="productName" class="input-field" 
                       value="<?= htmlspecialchars($name) ?>" required>
            </div>

            <!-- 商品画像選択とプレビュー -->
            <div>
                <label class="label">写真:</label>
                <input type="file" id="productImageInput" name="productImage" class="input-field" 
                       accept="image/*" onchange="previewImage(event)">
                <img src="<?= htmlspecialchars($img) ?>" alt="商品画像" id="productImagePreview" style="width: 100px;">
            </div>

            <div>
                <label class="label" for="productDescription">説明:</label>
                <textarea id="productDescription" name="productDescription" class="input-field" 
                          rows="4" required><?= htmlspecialchars($detail) ?></textarea>
            </div>

            <div>
                <label class="label" for="groupCode">グループコード:</label>
                <input type="number" id="groupCode" name="groupCode" class="input-field" 
                       value="<?= htmlspecialchars($groupCode) ?>" placeholder="グループコード:1-<?=$groupCount?>を入力してください" required>
            </div>

            <div>
                <label class="label" for="productPrice">単価:</label>
                <input type="number" id="productPrice" name="productPrice" class="input-field" 
                       value="<?= htmlspecialchars($price) ?>" required>
            </div>

            <div>
                <button class="button" type="button" onclick="location.href='MenuIchiran.php'">戻る</button>
                <button class="button" type="submit">変更</button>
            </div>
        </form>
    </div>
</body>
</html>
