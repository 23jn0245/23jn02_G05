<?php
require_once('DAO.php');

// Denpyoクラス：データベースのdenpyoテーブルに対応するクラス
class Denpyo
{
    public int $DenpyoID;      // 新增主鍵
    public int $kaiinID;      // 会員ID
    public int $goodsCode;    // 商品コード
    public int $num;          // 商品数量
    public int $groupCode;    // グループID
    public int $orderState;   // 注文状態 (0=キッチン、1=ホール,3=オーダー終わり)
}

// DenpyoDAOクラス：Denpyoテーブルのデータを操作するクラス
class DenpyoDAO
{
    private $db;

    public function __construct()
    {
        $this->db = DAO::get_db_connect(); // データベース接続を初期化
    }

    // Denpyoテーブルからすべてのレコードを取得するメソッド
    public function get_denpyo()
    {
        $sql = "SELECT * FROM Denpyo";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $data = [];
        while ($row = $stmt->fetchObject('Denpyo')) {
            $data[] = $row;
        }
        return $data;
    }

    // Denpyoテーブルに新しいレコードを挿入するメソッド
    public function insert_denpyo($kaiinID, $goodsCode, $num, $groupCode, $orderState)
    {
        $sql = "INSERT INTO Denpyo (kaiinID, goodsCode, num, groupCode, OrderState) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(1, $kaiinID, PDO::PARAM_INT);
        $stmt->bindParam(2, $goodsCode, PDO::PARAM_INT);
        $stmt->bindParam(3, $num, PDO::PARAM_INT);
        $stmt->bindParam(4, $groupCode, PDO::PARAM_INT);
        $stmt->bindParam(5, $orderState, PDO::PARAM_INT);

        $stmt->execute();
    }

   // Denpyoテーブルから特定の会員IDでレコードを取得するメソッド
   public function get_denpyo_by_kaiinID($kaiinID)
   {
       $sql = "SELECT * FROM Denpyo WHERE kaiinID = ?";
       $stmt = $this->db->prepare($sql);
       $stmt->bindParam(1, $kaiinID, PDO::PARAM_INT);
       $stmt->execute();

       $data = [];
       while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
           $denpyo = new Denpyo();
           $denpyo->DenpyoID = $row['DenpyoID'];
           $denpyo->kaiinID = $row['kaiinID'];
           $denpyo->goodsCode = $row['goodsCode'];
           $denpyo->num = $row['num'];
           $denpyo->groupCode = $row['groupCode'];
           $denpyo->orderState = $row['OrderState'];
           $data[] = $denpyo;
       }
       return $data;
   }

    // Denpyoテーブルの特定のレコードを更新するメソッド
    public function update_denpyo($DenpyoID, $kaiinID, $goodsCode, $num, $groupCode, $orderState)
    {
        $sql = "UPDATE Denpyo SET num = ?, groupCode = ?, OrderState = ? WHERE DenpyoID = ?";
        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(1, $num, PDO::PARAM_INT);
        $stmt->bindParam(2, $groupCode, PDO::PARAM_INT);
        $stmt->bindParam(3, $orderState, PDO::PARAM_INT);
        $stmt->bindParam(4, $DenpyoID, PDO::PARAM_INT);

        $stmt->execute();
    }

    // Denpyoテーブルから特定のDenpyoIDでレコードを削除するメソッド
    public function delete_denpyo($DenpyoID)
    {
        $sql = "DELETE FROM Denpyo WHERE DenpyoID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(1, $DenpyoID, PDO::PARAM_INT);
        $stmt->execute();
    }

    // 注文状態を検索
    public function get_orders_by_state($state)
    {
        $sql = "SELECT d.*, k.kaiinID AS groupCode
                FROM Denpyo d
                JOIN Kaiin k ON d.kaiinID = k.kaiinID
                WHERE d.orderstate = :state";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':state', $state, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 特定の商品コードの注文の orderstate を更新するメソッド
    public function update_order_state($DenpyoID, $newState)
    {
        $sql = "UPDATE Denpyo SET orderstate = :newState WHERE DenpyoID = :DenpyoID";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':newState', $newState, PDO::PARAM_INT);
        $stmt->bindValue(':DenpyoID', $DenpyoID, PDO::PARAM_INT);
        $stmt->execute();
    }

    // 特定の商品コードの注文を削除するメソッド
    public function delete_order($DenpyoID)
    {
        $sql = "DELETE FROM Denpyo WHERE DenpyoID = :DenpyoID";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':DenpyoID', $DenpyoID, PDO::PARAM_INT);
        $stmt->execute();
    }
    // 特定の会員IDを指定してすべての注文情報を削除するメソッド
    public function delete_denpyo_by_kaiinID($kaiinID) {
        $sql = "DELETE FROM Denpyo WHERE kaiinID = :kaiinID";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':kaiinID', $kaiinID, PDO::PARAM_INT);
        $stmt->execute();
    }
}

?>