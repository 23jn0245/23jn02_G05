<?php 
require_once('DAO.php'); // DAO 基类的包含

class Orders {
    public int $orderID;        // 注文ID
    public int $kaiinID;        // 会員ID
    public string $dish_name;   // 料理名
    public float $price;        // 単価
    public int $num;            // 数量
    public float $total_price;  // 合計金額
    public string $order_date;  // 注文日時
}

class OrdersDAO {

    /**
     * 会員IDに基づいて注文履歴を取得
     */
    public function get_orders_by_member_id($kaiinID) {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT * FROM Orders WHERE kaiinID = :kaiinID";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':kaiinID', $kaiinID, PDO::PARAM_INT);
        $stmt->execute();
    
        $data = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $order = new Orders();
            $order->orderID = isset($row['orderID']) ? (int)$row['orderID'] : 0;
            $order->kaiinID = isset($row['kaiinID']) ? (int)$row['kaiinID'] : 0;
            $order->dish_name = isset($row['dish_name']) ? $row['dish_name'] : '';
            $order->price = isset($row['price']) ? (float)$row['price'] : 0.0;
            $order->num = isset($row['num']) ? (int)$row['num'] : 0;
            $order->total_price = isset($row['total_price']) ? (float)$row['total_price'] : 0.0;
            $order->order_date = isset($row['order_date']) ? $row['order_date'] : '';
    
            $data[] = $order;
        }
        return $data;
    }
    
    
    

    /**
 * 新しい注文を作成
 */
public function create_order($kaiinID, $dish_name, $price, $num) {
    $dbh = DAO::get_db_connect();

    // 删除 total_price 列，因为它是一个计算列
    $sql = "INSERT INTO Orders (kaiinID, dish_name, price, num, order_date)
        VALUES (:kaiinID, :dish_name, :price, :num, GETDATE())";

    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':kaiinID', $kaiinID, PDO::PARAM_INT);
    $stmt->bindValue(':dish_name', $dish_name, PDO::PARAM_STR);
    $stmt->bindValue(':price', $price, PDO::PARAM_STR); // DECIMAL 类型使用字符串表示
    $stmt->bindValue(':num', $num, PDO::PARAM_INT);

    // 执行语句
    if ($stmt->execute()) {
        return $dbh->lastInsertId(); // 返回订单 ID
    }
    return false; // 如果执行失败，返回 false
}

    /**
     * 注文を削除
     */
    public function delete_order($orderID) {
        $dbh = DAO::get_db_connect();
        $sql = "DELETE FROM Orders WHERE orderID = :orderID";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':orderID', $orderID, PDO::PARAM_INT);

        return $stmt->execute(); // 成功時は true、失敗時は false を返す
    }
    // 会員IDを指定してすべての注文を削除するメソッド
    public function delete_orders_by_kaiinID($kaiinID) {
        $dbh = DAO::get_db_connect();
        $sql = "DELETE FROM Orders WHERE kaiinID = :kaiinID";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':kaiinID', $kaiinID, PDO::PARAM_INT);

        return $stmt->execute(); // 会員IDに基づく削除の成否を返す
    }
}
?>
