<?php
// 必要なヘルパーファイルをインクルード
require_once '../helpers/CartDAO.php';  
require_once '../helpers/KaiinDAO.php';
require_once '../helpers/GoodsDAO.php';

// セッションがまだ開始されていない場合のみ session_start() を実行
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // セッションを開始
}

// 必要なデータがない場合に備えてデフォルト値を設定
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;
$goodsDAO = new GoodsDAO();

if ($product_id > 0) {
    // 商品情報を取得
    $productList = $goodsDAO->get_recommend_goods();
    $product = null;

    foreach ($productList as $item) {
        if ($item->goodscode == $product_id) {
            $product = $item;
            break;
        }
    }

    if ($product) {
        // 商品情報の設定
        $name = htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8');
        $price = htmlspecialchars($product->price, ENT_QUOTES, 'UTF-8');
        $img = htmlspecialchars($product->img, ENT_QUOTES, 'UTF-8');
        $detail = htmlspecialchars($product->detail, ENT_QUOTES, 'UTF-8');
        $groupCode = htmlspecialchars($product->groupCode, ENT_QUOTES, 'UTF-8');
    } else {
        echo "指定された商品が見つかりませんでした。";
        exit;
    }
} else {
    echo "無効な商品IDです。";
    exit;
}

// toppageへ戻る処理
if (isset($_GET['groupCode'])) {
    $_SESSION['groupCode'] = $_GET['groupCode'];
} elseif (isset($product)) {
    // 商品情報からgroupCodeを取得してセッションに保存
    $_SESSION['groupCode'] = $product->groupCode;
}

// 商品をカートに追加する処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_to_cart') {
    $goodsCode = intval($_POST['goodsCode'] ?? 0);
    $num = intval($_POST['dishQuantity'] ?? 0);
    $cartDAO = new CartDAO();
    $cart = new Cart();
    // セッションから会員情報を取得
    if (isset($_SESSION['kaiin']) && is_object($_SESSION['kaiin'])) {
        $kaiin = $_SESSION['kaiin'];
    } else {
        // セッションが無効な場合、ログインページにリダイレクト
        header("Location: LogiN.php");
        exit;
    }
    $kaiinID = $kaiin->kaiinID;
    $cart->kaiinID = $kaiinID;
    $cart->goodsCode = $goodsCode;
    $cart->num = $num;
    $cart->groupCode = $product->groupCode;

    if ($cartDAO->add_to_cart($cart)) {
        $_SESSION['cart_message'] = "商品がカートに追加されました。";
    } else {
        $_SESSION['cart_message'] = "カートへの追加に失敗しました。";
    }

    // リダイレクトしてメッセージを表示
    header("Location: MenuDetail.php?product_id=$product_id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>料理詳細</title>
    <link rel="stylesheet" href="CSS/MenuDetailtestStyle.css">
</head>
<body>
    <h1>料理詳細</h1>
    <div class="container">
        <img src="../image/<?= $img ?>" alt="<?= $name ?>" class="dish-photo">
        <div class="dish-info">
            <p><label>料理名: </label><?= $name ?></p>
            <p><label>値段: </label><?= $price ?>円</p>
            <p><label>料理説明: </label><?= $detail ?></p>
        </div>
        <form action="" method="post">
            <div class="dish-info">
                <label for="dishQuantity">数 (個): </label>
                <input type="number" id="dishQuantity" name="dishQuantity" value="1" min="1">
            </div>
            <input type="hidden" name="kaiinID" value="<?= isset($kaiin) ? $kaiin->id : 0 ?>">
            <input type="hidden" name="goodsCode" value="<?= $product_id ?>">
            <input type="hidden" name="action" value="add_to_cart">
            <button type="submit">カートに入れる</button>
        </form>
        <div class="btn-row">
            <button type="button" class="btn-cart" onclick="location.href='cart.php'">カートを見る</button>
            <button type="button" class="btn-home" onclick="location.href='Toppage.php?groupCode=<?= $groupCode ?>'">戻る</button>
        </div>
    </div>

    <!-- セッションメッセージの表示 -->
    <?php if (isset($_SESSION['cart_message'])): ?>
    <div class="popup-message">
        <p><?= htmlspecialchars($_SESSION['cart_message'], ENT_QUOTES, 'UTF-8') ?></p>
        <button class="close-button" onclick="location.href='Toppage.php'">閉じる</button>
    </div>
    <?php 
        unset($_SESSION['cart_message']);  // メッセージを表示した後、セッションから削除
    endif; 
    ?>
</body>
</html>