<?php 
require_once('DAO.php');

class GoodsGroup {
    public int $groupCode;
    public string $groupName;
}

class GoodsGroupDAO {

    public function get_goodsgroup() {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT * FROM groups";
        $stmt = $dbh->prepare($sql);
        $stmt->execute();
    
        $data = [];
        while ($row = $stmt->fetchObject('GoodsGroup')) {
            $data[] = $row;
        }
        return $data;
    }

    public function get_goodsname($groupCode) {
        // データベース接続を取得
        $dbh = DAO::get_db_connect();

        // 指定されたgroupCodeに一致する行を取得
        $sql = "SELECT * FROM groups WHERE groupCode = :groupCode";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':groupCode', $groupCode, PDO::PARAM_INT);
        $stmt->execute();

        // 結果をGoodsGroupオブジェクトとして取得
        $result = $stmt->fetchObject('GoodsGroup');

        // 結果を返却（存在しない場合はnull）
        return $result;
    }
}
