<?php
require_once 'DAO.php';

class Cart {
    public int $kaiinID;   // 会員ID
    public int $goodsCode; // 商品コード
    public int $num;       // 商品数量
    public int $groupCode; // グループID
}

class CartDAO {

    // 特定の会員のカート情報を取得
    public function get_cart_list($kaiinID) {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT * FROM Cart WHERE kaiinID = :kaiinID"; // 会員IDでカートのデータを取得
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':kaiinID', $kaiinID, PDO::PARAM_INT);
        $stmt->execute();

        $data = [];
        // カート情報を取得し、Cartオブジェクトとして返す
        while ($row = $stmt->fetchObject('Cart')) {
            $data[] = $row;
        }

        return $data;
    }

    // カートから特定の商品を削除
    public function delete_cart($kaiinID, $goodsCode) {
        $dbh = DAO::get_db_connect();
        $sql = "DELETE FROM Cart WHERE kaiinID = :kaiinID AND goodsCode = :goodsCode"; // 会員IDと商品コードでカートの商品を削除
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':kaiinID', $kaiinID, PDO::PARAM_INT);
        $stmt->bindParam(':goodsCode', $goodsCode, PDO::PARAM_INT);
        $stmt->execute();
    }

    // 商品をカートに追加
    public function add_to_cart($cart) {
        $dbh = DAO::get_db_connect();
        $sql = "INSERT INTO Cart (kaiinID, goodsCode, num, groupCode) 
                VALUES (:kaiinID, :goodsCode, :num, :groupCode)"; // カートに商品を追加
        
        $stmt = $dbh->prepare($sql);

        // パラメータをバインド
        $stmt->bindParam(':kaiinID', $cart->kaiinID);
        $stmt->bindParam(':goodsCode', $cart->goodsCode);
        $stmt->bindParam(':num', $cart->num);
        $stmt->bindParam(':groupCode', $cart->groupCode);

        return $stmt->execute(); // 成功時にtrueを返す
    }

    // カートに商品がすでに存在するかチェック
    public function cart_exists(int $kaiinID, int $goodsCode) {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT kaiinID, goodsCode, num FROM Cart WHERE kaiinID = :kaiinID AND goodsCode = :goodsCode"; // 商品がカートに存在するか確認
        $stmt = $dbh->prepare($sql);

        // パラメータをバインド
        $stmt->bindValue(':kaiinID', $kaiinID, PDO::PARAM_INT);
        $stmt->bindValue(':goodsCode', $goodsCode, PDO::PARAM_INT);

        $stmt->execute();
        if ($stmt->fetch() !== false) {
            return true; // 商品がカートに存在する場合
        } else {
            return false; // 商品がカートに存在しない場合
        }
    }

    // カートに商品がない場合、商品を挿入する
    public function insert(int $kaiinID, int $goodsCode, int $num, int $groupCode) {
        $dbh = DAO::get_db_connect();
        if (!$this->cart_exists($kaiinID, $goodsCode)) { // 商品がカートに存在しない場合
            $sql = "INSERT INTO Cart (kaiinID, goodsCode, num, groupCode) 
                    VALUES (:kaiinID, :goodsCode, :num, :groupCode)";
            $stmt = $dbh->prepare($sql);

            // パラメータをバインド
            $stmt->bindValue(':kaiinID', $kaiinID, PDO::PARAM_INT);
            $stmt->bindValue(':goodsCode', $goodsCode, PDO::PARAM_INT);
            $stmt->bindValue(':num', $num, PDO::PARAM_INT);
            $stmt->bindValue(':groupCode', $groupCode, PDO::PARAM_INT);

            $stmt->execute();
        } else { // 商品がすでにカートに存在する場合
            $sql = "UPDATE Cart SET num = num + :num WHERE kaiinID = :kaiinID AND goodsCode = :goodsCode"; // 商品の数量を更新
            $stmt = $dbh->prepare($sql);

            // パラメータをバインド
            $stmt->bindValue(':kaiinID', $kaiinID, PDO::PARAM_INT);
            $stmt->bindValue(':goodsCode', $goodsCode, PDO::PARAM_INT);
            $stmt->bindValue(':num', $num, PDO::PARAM_INT);
            $stmt->bindValue(':groupCode', $groupCode, PDO::PARAM_INT);
            $stmt->execute();
        }
    }

    // カート内の商品数量を更新
    public function update_cart($kaiinID, $goodsCode, $newNum) {
        $dbh = DAO::get_db_connect();
        $sql = "UPDATE Cart SET num = :num WHERE kaiinID = :kaiinID AND goodsCode = :goodsCode"; // カート内の商品数量を更新
        
        $stmt = $dbh->prepare($sql);

        // パラメータをバインド
        $stmt->bindParam(':kaiinID', $kaiinID, PDO::PARAM_INT);
        $stmt->bindParam(':goodsCode', $goodsCode, PDO::PARAM_INT);
        $stmt->bindParam(':num', $newNum, PDO::PARAM_INT);

        return $stmt->execute(); // 成功時にtrueを返す
    }
}
?>
