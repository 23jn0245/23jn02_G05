<?php
require_once('../helpers/tableNoDAO.php');
require_once('../helpers/DenpyoDAO.php');
require_once('../helpers/GoodsDAO.php');

$tabledb = new TableNoDAO();
$denpyodb = new DenpyoDAO();

// URLからテーブル番号と会員IDを取得
$tableNo = isset($_GET['tableNo']) ? $_GET['tableNo'] : null;
$kaiinID = isset($_GET['kaiinID']) ? $_GET['kaiinID'] : null;

// URLパラメータが不足している場合のエラー処理
if (!$tableNo || !$kaiinID) {
    echo "Error: Missing tableNo or kaiinID parameters.";
    exit;
}

// 合計金額の初期化
$totalAmount = 0;

// 会員IDに基づいて注文履歴を取得
$denpyos = $denpyodb->get_denpyo_by_kaiinID($kaiinID);

// キャンセルされた注文を削除
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteDenpyoID'])) {
    $DenpyoID = (int)$_POST['deleteDenpyoID'];
    $denpyodb->delete_denpyo($DenpyoID);
        // キャンセル成功後、TableTop.phpにリダイレクト
        header("Location: TableTop.php?tableNo={$tableNo}&kaiinID={$kaiinID}");
        exit();
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>テーブル詳細</title>
    <link rel="stylesheet" href="CSS/TableTopStyle.css">
</head>
<body>

    <header>
    テーブル（NO<?php echo $tableNo; ?>:詳細）
    </header>

    <form method="post" action="">
        <table class="details-table">
            <thead>
                <tr>
                    <th>注文した商品名（数）</th>
                    <th>金額</th>
                    <th>キャンセル</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($denpyos as $denpyo) {
                    $goodsCode = $denpyo->goodsCode;
                    $goodsdb = new GoodsDAO();
                    $goods = $goodsdb->get_goods_by_code($goodsCode);
                    $num = $denpyo->num;

                    if ($goods) {
                        $itemTotal = $goods['price'] * $num;
                        $totalAmount += $itemTotal * 1.1; // 税込み計算
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($goods['name']) . "（" . htmlspecialchars($num) . "）</td>";
                        echo "<td>" . htmlspecialchars($itemTotal) . "円</td>";
                        echo "<td>";
                        echo "<button type='submit' name='deleteDenpyoID' value='" . htmlspecialchars($denpyo->DenpyoID) . "' onclick=\"return confirm('この注文をキャンセルしますか？');\">キャンセル</button>";
                        echo "</td>";
                        echo "</tr>";
                    }
                }
                ?>
            </tbody>
        </table>

        <div class="total-amount">
            <?php echo "合計金額(税込み): " . htmlspecialchars($totalAmount) . "円"; ?>
        </div>
    </form>

    <a href="Table.php">
        <button class="back-button">戻る</button>
    </a>

</body>
</html>
