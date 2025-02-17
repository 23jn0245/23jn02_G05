<?php
require_once '../helpers/DenpyoDAO.php';
require_once '../helpers/GoodsDAO.php';
require_once '../helpers/tableNoDAO.php';
$denpyoDAO = new DenpyoDAO();
$goodsDAO = new GoodsDAO();
$tabledb = new TableNoDAO();
// orderstate を 2 に更新する処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order'])) {
    $denpyoID = $_POST['denpyoID'];
    $denpyoDAO->update_order_state($denpyoID, 2);

    // ページをリロードして更新を反映
    header("Location: Halldetail.php");
    exit;
}

// データベースから orderstate が 1 の商品を取得
$orders = $denpyoDAO->get_orders_by_state(1);

// 商品情報を取得する処理
foreach ($orders as $key => $order) {
    $goods = $goodsDAO->get_goods_by_code($order['goodsCode']);
    $orders[$key]['goodsName'] = $goods['name'] ?? '不明な商品';
    $orders[$key]['goodsImage'] = $goods['img'] ?? 'default.png';
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ホール注文状況</title>
    <link rel="stylesheet" href="CSS/HalldetailStyle.css">
</head>
<body>
    <header>
        ホール
        <a href="Table.php">
            <button class="table-button">TABLE</button>
        </a>
    </header>

    <table class="order-table">
        <thead>
            <tr>
                <th>テーブル番号</th>
                <th>準備リスト</th>
                <th>商品届現状</th>
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

    <a href="Toppage.php">
        <button>戻る</button>
    </a>
</body>
</html>
