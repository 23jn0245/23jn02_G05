<?php
session_start();
require_once('../helpers/DenpyoDAO.php');
require_once('../helpers/GoodsDAO.php');
require_once('../helpers/OrdersDAO.php');
require_once('../helpers/tableNoDAO.php');
if (isset($_SESSION['kaiin']) && is_object($_SESSION['kaiin'])) {
    $kaiin = $_SESSION['kaiin'];
} else {
    header("Location: LogiN.php");
    exit;
}
// セッションから会員IDを取得
$kaiinID = $_SESSION['kaiinID'] ?? null; // 会員IDがセッションに保存されていない場合は null
$totalAmount = 0; // 合計金額の初期化
$denpyodb = new DenpyoDAO();
$ordersdb = new OrdersDAO();
$tabledb = new tableNoDAO();
$goodsdb = new GoodsDAO();
// 会員IDに基づいて注文履歴を取得
$denpyos = $denpyodb->get_denpyo_by_kaiinID($kaiinID);
// 会計ボタンが押されたかを判定
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_payment'])) {
    try {
        foreach ($denpyos as $denpyo) {
            $goodsCode = $denpyo->goodsCode;
            $goods = $goodsdb->get_goods_by_code($goodsCode);
            $dish_name = $goods['name'];
            $price = $goods['price'];
            $num = $denpyo->num;

            // 注文履歴に追加
            $ordersdb->create_order($kaiinID, $dish_name, $price, $num);

            // 伝票表から削除
            $denpyodb->delete_denpyo($denpyo->DenpyoID);
        }

        // テーブル情報を更新
        $table = $tabledb->get_kaiin_no_by_id($kaiinID);
        $table->yobidasiState = 0;
        $table->syokujistate = 0;
        $table->kaiinID = 0;
        $tabledb->update_table_no($table);

        // 成功メッセージのフラグを設定
        $paymentSuccess = true;
    } catch (Exception $e) {
        $errorMessage = $e->getMessage();
    }
}

// 表示用データを準備
$denpyodb = new DenpyoDAO();
$denpyos = $denpyodb->get_denpyo_by_kaiinID($kaiinID);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>会計</title>
    <link rel="stylesheet" href="CSS/AccountinGStyle.css">
</head>
<body>
    <h1>会計</h1>

    <?php if (!empty($paymentSuccess)) : ?>
        <!-- 支払い成功メッセージ -->
        <div class="container">
            <p class="success-message">レジにお越しください。</p>
            <button class="success-button" onclick="location.href='LogOut.php'">ホームへ戻る</button>
        </div>
    <?php elseif (!empty($errorMessage)) : ?>
        <!-- エラーメッセージ表示 -->
        <div class="container">
            <p class="error-message">エラーが発生しました: <?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></p>
            <button class="error-button" onclick="location.href='Toppage.php'">ホームへ戻る</button>
        </div>
    <?php elseif (!empty($denpyos)) : ?>
        <!-- 支払い情報を表示するコンテナ -->
        <div class="container" id="payment-container">
            <h2>支払い情報</h2>
            <table class="details-table">
                <thead>
                    <tr>
                        <th>料理名</th>
                        <th>金額</th>
                        <th>個数</th>
                        <th>合計</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($denpyos as $denpyo) {
                        $goodsCode = $denpyo->goodsCode;
                        $goods = $goodsdb->get_goods_by_code($goodsCode);
                        $num = $denpyo->num;

                        if ($goods) {
                            $itemTotal = $goods['price'] * $num; // 商品利用金額
                            $totalAmount += $itemTotal * 1.1; // 税込み金額を計算
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($goods['name'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars(number_format($goods['price']), ENT_QUOTES, 'UTF-8') ?> 円</td>
                                <td><?= htmlspecialchars($num, ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars(number_format($itemTotal), ENT_QUOTES, 'UTF-8') ?> 円</td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                    <tr class="total">
                        <td colspan="3">合計金額(税込み)</td>
                        <td><?= htmlspecialchars(number_format($totalAmount), ENT_QUOTES, 'UTF-8') ?> 円</td>
                    </tr>
                </tbody>
            </table>

            <form method="post">
                <div class="button-row">
                    <button type="submit" name="confirm_payment">会計</button>
                    <button type="button" onclick="location.href='Toppage.php'">ホームへ戻る</button>
                </div>
            </form>
        </div>

    <?php else : ?>
        <!-- 注文履歴がない場合 -->
        <div class="container">
            <p>何も入っていません。</p>
            <button type="button" onclick="location.href='Toppage.php'">ホームへ戻る</button>
        </div>
    <?php endif; ?>

</body>
</html>
