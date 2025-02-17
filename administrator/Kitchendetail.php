<?php
require_once '../helpers/DenpyoDAO.php';
require_once '../helpers/GoodsDAO.php';
require_once '../helpers/tableNoDAO.php';
$denpyoDAO = new DenpyoDAO();
$goodsDAO = new GoodsDAO();
$tabledb = new TableNoDAO();
// データベースから orderstate が 0 の商品を取得
$orders = $denpyoDAO->get_orders_by_state(0);

// 商品情報を取得する処理（重複排除用の配列を用意）
$uniqueGoods = []; // 商品コードの重複チェック用
foreach ($orders as $key => $order) {
    // 商品コードがまだ処理されていない場合のみ取得
    if (!isset($uniqueGoods[$order['goodsCode']])) {
        $goods = $goodsDAO->get_goods_by_code($order['goodsCode']);
        $uniqueGoods[$order['goodsCode']] = [
            'name' => $goods['name'] ?? '不明な商品',
            'img' => $goods['img'] ?? 'default.png'
        ];
    }
    // 重複していても商品情報を追加
    $orders[$key]['goodsName'] = $uniqueGoods[$order['goodsCode']]['name'];
    $orders[$key]['goodsImage'] = $uniqueGoods[$order['goodsCode']]['img'];
}

// orderstate を 1 に更新する処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order'])) {
    $denpyoID = $_POST['denpyoID'];
    $denpyoDAO->update_order_state($denpyoID, 1);

    // ページをリロードして更新を反映
    header("Location: Kitchendetail.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>キッチン注文状況</title>
    <link rel="stylesheet" href="CSS/KitchendetailStyle.css">
</head>
<body>
    <header>
        キッチン
        <a href="Table.php">
            <button class="table-button">TABLE</button>
        </a>
    </header>

    <table class="order-table">
        <thead>
            <tr>
                <th>テーブル番号</th>
                <th>準備リスト</th>
                <th>料理の準備状況</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?=$tabledb->get_kaiin_no_by_id($order['groupCode'])->tableNo ?></td>
                    <td>
                        <img src="<?= htmlspecialchars($order['goodsImage']) ?>" alt="<?= htmlspecialchars($order['goodsName']) ?>" style="width: 50px; height: 50px;">
                        <?= htmlspecialchars($order['goodsName']) ?>（数量: <?= htmlspecialchars($order['num']) ?>）
                    </td>
                    <td class="status-button">
                        <form method="POST">
                            <input type="hidden" name="denpyoID" value="<?= htmlspecialchars($order['DenpyoID']) ?>">
                            <button type="submit" name="update_order">O</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script>
        function callStaff() {
            alert("スタッフを呼び出しました。");
        }
    </script>

    <a href="toppage.php">
        <button>戻る</button>
    </a>
</body>
</html>
