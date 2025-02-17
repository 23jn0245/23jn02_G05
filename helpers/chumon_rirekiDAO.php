<?php 
require_once 'DAO.php';

// 注文クラス
class Order {
    public int $orderID;        // 注文ID
    public int $kaiinID;        // 会員ID
    public string $dish_name;   // 料理名
    public float $price;        // 料理単価
    public int $quantity;       // 数量
    public float $total_price;  // 合計金額（税込10%）
    public string $order_date;  // 注文日時
}

// OrdersDAOクラス
class OrdersDAO {

    // すべての注文を取得するメソッド
    public function get_orders() {
        $dbh = DAO::get_db_connect();  // DB接続を取得
        $sql = "SELECT * FROM Orders"; // 注文テーブルから全てのデータを選択
        $stmt = $dbh->prepare($sql);   // SQLを準備
        
        $stmt->execute();  // クエリを実行

        $data = [];

        // 各注文をOrderクラスのインスタンスとして取得
        while ($row = $stmt->fetchObject('Order')) {
            $data[] = $row;
        }
        return $data;  // 注文データを返す
    }

    // 注文IDに基づいて単一の注文を取得するメソッド
    public function get_order_by_id($orderID) {
        $dbh = DAO::get_db_connect();  // DB接続を取得
        $sql = "SELECT * FROM Orders WHERE orderID = :orderID";  // 指定した注文IDに基づいてデータを選択
        $stmt = $dbh->prepare($sql);   // SQLを準備
        $stmt->bindParam(':orderID', $orderID, PDO::PARAM_INT);  // パラメータをバインド
        
        $stmt->execute();  // クエリを実行
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Order');  // 結果をOrderクラスのインスタンスとして取得
        return $stmt->fetch();  // 単一の注文データを返す
    }

    // 注文を追加するメソッド
    public function add_order($kaiinID, $dish_name, $price, $quantity) {
        $dbh = DAO::get_db_connect();  // DB接続を取得
        $sql = "INSERT INTO Orders (kaiinID, dish_name, price, quantity)  // 注文をOrdersテーブルに挿入
                VALUES (:kaiinID, :dish_name, :price, :quantity)";
        $stmt = $dbh->prepare($sql);   // SQLを準備
        $stmt->bindParam(':kaiinID', $kaiinID, PDO::PARAM_INT);  // 会員IDをバインド
        $stmt->bindParam(':dish_name', $dish_name, PDO::PARAM_STR);  // 料理名をバインド
        $stmt->bindParam(':price', $price, PDO::PARAM_STR);  // 価格をバインド
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);  // 数量をバインド
        
        return $stmt->execute();  // 実行して結果を返す
    }

    // 注文を更新するメソッド
    public function update_order($orderID, $kaiinID, $dish_name, $price, $quantity) {
        $dbh = DAO::get_db_connect();  // DB接続を取得
        $sql = "UPDATE Orders SET kaiinID = :kaiinID, dish_name = :dish_name, price = :price, quantity = :quantity
                WHERE orderID = :orderID";  // 注文IDに基づいてデータを更新
        $stmt = $dbh->prepare($sql);   // SQLを準備
        $stmt->bindParam(':orderID', $orderID, PDO::PARAM_INT);  // 注文IDをバインド
        $stmt->bindParam(':kaiinID', $kaiinID, PDO::PARAM_INT);  // 会員IDをバインド
        $stmt->bindParam(':dish_name', $dish_name, PDO::PARAM_STR);  // 料理名をバインド
        $stmt->bindParam(':price', $price, PDO::PARAM_STR);  // 価格をバインド
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);  // 数量をバインド
        
        return $stmt->execute();  // 実行して結果を返す
    }

    // 注文を削除するメソッド
    public function delete_order($orderID) {
        $dbh = DAO::get_db_connect();  // DB接続を取得
        $sql = "DELETE FROM Orders WHERE orderID = :orderID";  // 指定された注文IDに基づいて注文を削除
        $stmt = $dbh->prepare($sql);   // SQLを準備
        $stmt->bindParam(':orderID', $orderID, PDO::PARAM_INT);  // 注文IDをバインド
        
        return $stmt->execute();  // 実行して結果を返す
    }
}
