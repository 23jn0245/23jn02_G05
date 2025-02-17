<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>注文履歴</title>
    <link rel="stylesheet" href="CSS/OrderHistoryStyle.css">
</head>

<body>
    <img src="../image/logo2.png" alt="Logo" class="logo">

    <h1>注文履歴</h1>

    <?php
    session_start();
    require_once '../helpers/OrdersDAO.php';
    require_once '../helpers/KaiinDAO.php';

    if (isset($_SESSION['kaiin']) && is_object($_SESSION['kaiin'])) {
        $kaiin = $_SESSION['kaiin'];
    } else {
        header("Location: LogiN.php");
        exit;
    }

    // セッションから会員IDを取得
    $kaiinID = $_SESSION['kaiinID'] ?? null;

    if ($kaiinID) {
        $ordersDAO = new OrdersDAO();
        $orderHistory = $ordersDAO->get_orders_by_member_id($kaiinID);

        // 日付で順序を分類
        $groupedOrders = [];
        foreach ($orderHistory as $order) {
            $date = date('Y-m-d', strtotime($order->order_date));
            if (!isset($groupedOrders[$date])) {
                $groupedOrders[$date] = [];
            }
            $groupedOrders[$date][] = $order;
        }

        if (!empty($groupedOrders)) {
            ?>
            <div class="container">
                <?php foreach ($groupedOrders as $date => $orders): ?>
                    <h2 class="order-date-title">注文日: <?= htmlspecialchars($date, ENT_QUOTES, 'UTF-8') ?></h2>
                    <table>
                        <thead>
                            <tr>
                                <th>商品名</th>
                                <th>単価</th>
                                <th>数量</th>
                                <th>合計（税込み）</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $dailyTotal = 0;
                            foreach ($orders as $order):
                                $dailyTotal += $order->total_price;
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($order->dish_name, ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars(number_format($order->price), ENT_QUOTES, 'UTF-8') ?> 円</td>
                                    <td><?= htmlspecialchars($order->num, ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars(number_format($order->total_price), ENT_QUOTES, 'UTF-8') ?> 円</td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="daily-total">
                                <td colspan="3">合計金額（税込み）</td>
                                <td><?= htmlspecialchars(number_format($dailyTotal), ENT_QUOTES, 'UTF-8') ?> 円</td>
                            </tr>
                        </tbody>
                    </table>
                <?php endforeach; ?>
            <div class="btn-row">
            <button type="button" class="btn-back" onclick="location.href='Toppage.php'">戻る</button>
        </div>
            </div>
            <?php
        } else {
            // 注文履歴がない場合の処理
            ?>
            <div class="container">
                <p>注文履歴がありません。</p>
                <button type="button" class="btn-back" onclick="location.href='Toppage.php'">ホームへ戻る</button>
            </div>
            <?php
        }
    } else {
        // 会員IDがセッションに存在しない場合の処理
        ?>
        <div class="container">
            <p>注文履歴がありません。</p>
            <button type="button" class="btn-back" onclick="location.href='Toppage.php'">ホームへ戻る</button>
        </div>
        <?php
    }
    ?>

    <script>
        function callStaff() {
            alert("店員を呼び出しました。少々お待ちください。");
        }
    </script>
</body>

</html>
