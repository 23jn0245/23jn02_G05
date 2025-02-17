<?php
require_once '../helpers/KaiinDAO.php';
require_once '../helpers/CartDAO.php';
require_once '../helpers/GoodsDAO.php';
require_once '../helpers/OrdersDAO.php';
require_once '../helpers/DenpyoDAO.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!class_exists('Kaiin')) {
    require_once '../helpers/Kaiin.php';
}

if (isset($_SESSION['kaiin']) && is_object($_SESSION['kaiin'])) {
    $kaiin = $_SESSION['kaiin'];
} else {
    header("Location: LogiN.php");
    exit;
}

$kaiinID = $kaiin->kaiinID;
$ordersdb = new OrdersDAO();
$denpyodb = new DenpyoDAO();

// カートから商品を削除する処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_goods_code'])) {
    $goodsCodeToDelete = intval($_POST['delete_goods_code']);
    $cartDao = new CartDAO();
    $cartDao->delete_cart($kaiinID, $goodsCodeToDelete);
}

// 注文確定の処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_order'])) {
    $cartDao = new CartDAO();
    $cartList = $cartDao->get_cart_list($kaiinID);

    foreach ($cartList as $cart) {
        $goodsCode = $cart->goodsCode;
        $num = $cart->num;
        $groupCode = $cart->groupCode;
        $goodsdb = new GoodsDAO();
        $goods = $goodsdb->get_goods_by_code($goodsCode);

        if ($goods) {
            $goodsName = $goods['name'];
            $goodsPrice = $goods['price'];
            $totalPrice = $goodsPrice * $num;

            // Denpyoテーブルにデータを挿入
            $orderState = 0; // 注文状態は0で初期化
            $denpyodb->insert_denpyo($kaiinID, $goodsCode, $num, $groupCode, $orderState);

            // Ordersテーブルにデータを挿入
           // $ordersdb->create_order($kaiinID, $goodsName, $goodsPrice, $num);
             // カートをクリア
            $cartDao->delete_cart($kaiinID,$goodsCode);
        }
    }

   

    $success = true;
}

// カート情報を取得
$cartDao = new CartDAO();
$cartList = $cartDao->get_cart_list($kaiinID);

// 初期化
$totalPrice = 0;
$hasItems = !empty($cartList);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>カート</title>
    <link rel="stylesheet" href="CSS/CartStyle.css">
</head>
<body>
    <h1>カート</h1>

    <?php if (isset($success) && $success): ?>
    <div id="success-container">
        <p class="success-message">注文が確定しました！</p>
        <button class="success-button" onclick="location.href='Toppage.php'">ホームへ戻る</button>
    </div>
    <?php elseif ($hasItems): ?>
    <div class="container" id="cart-container">
        <table id="cart-table">
            <thead>
                <tr>
                    <th>料理名</th>
                    <th>金額</th>
                    <th>個数</th>
                    <th>合計</th>
                    <th>削除</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cartList as $cart): ?>
                    <?php
                    $goodsCode = $cart->goodsCode;
                    $num = $cart->num;

                    $goodsdb = new GoodsDAO();
                    $goods = $goodsdb->get_goods_by_code($goodsCode);

                    if ($goods) {
                        $goodsName = $goods['name'];
                        $goodsPrice = $goods['price'];
                        $itemTotal = $goodsPrice * $num;
                        $totalPrice += $itemTotal;
                    }
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($goodsName) ?></td>
                        <td><?= number_format($goodsPrice) ?>円</td>
                        <td><?= $num ?></td>
                        <td><?= number_format($itemTotal) ?>円</td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="delete_goods_code" value="<?= $goodsCode ?>">
                                <button type="submit" class="delete-button">削除</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr class="total">
                    <td colspan="3">合計金額(税込)</td>
                    <td><?= number_format($totalPrice) ?>円</td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        <form method="POST" id="confirm-order-form">
            <div class="button-row">
                <button type="submit" name="confirm_order" class="btn-confirm">注文確定</button>
                <button type="button" class="btn-back" onclick="location.href='Toppage.php'">ホームへ戻る</button>
            </div>
        </form>
    </div>
    <?php else: ?>
    <div class="container">
        <p>カートの中には何も入っていません。</p>
        <button type="button" class="btn-back" onclick="location.href='Toppage.php'">ホームへ戻る</button>
    </div>
    <?php endif; ?>
</body>
</html>
