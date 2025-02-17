<?php
require_once('DAO.php');

// TableNoクラス: tableNoテーブルの1行を表すクラス
class TableNo {
    public int $tableNo;         // テーブル番号
    public int $yobidasiState;   // 呼び出し状態 (0: 普通, 1: 呼び出し中)
    public int $syokujistate;    // 食事状態 (0: 人が座っていない, 1: 食事中)
    public int $kaiinID;         // 会員ID
}

// TableNoDAOクラス: tableNoテーブルへのアクセスを提供するクラス
class TableNoDAO {
    
    // 全てのテーブル情報を取得するメソッド
    public function get_all_table_no() {
        $dbh = DAO::get_db_connect(); // データベース接続を取得
        $sql = "SELECT * FROM tableNo"; // SQL文
        $stmt = $dbh->prepare($sql);   // ステートメント準備
        $stmt->execute();             // 実行

        $data = []; // 結果を格納する配列
        while ($row = $stmt->fetchObject('TableNo')) { // 結果をTableNoオブジェクトにマッピング
            $data[] = $row; // 配列に追加
        }
        return $data; // 全てのデータを返す
    }

    // 指定されたテーブル番号の情報を取得するメソッド
    public function get_table_no_by_id(int $tableNo) {
        $dbh = DAO::get_db_connect(); // データベース接続を取得
        $sql = "SELECT * FROM tableNo WHERE tableNo = :tableNo"; // SQL文
        $stmt = $dbh->prepare($sql);   // ステートメント準備
        $stmt->bindParam(':tableNo', $tableNo); // パラメータバインド
        $stmt->execute();             // 実行

        return $stmt->fetchObject('TableNo'); // 結果を返す (1件のみ)
    }
    
    // 指定された会員IDの情報を取得するメソッド
public function get_kaiin_no_by_id(int $kaiinID) {
    $dbh = DAO::get_db_connect(); // データベース接続を取得
    $sql = "SELECT * FROM tableNo WHERE kaiinID = :kaiinID"; // SQL文
    $stmt = $dbh->prepare($sql);   // ステートメント準備
    $stmt->bindParam(':kaiinID', $kaiinID, PDO::PARAM_INT); // パラメータバインド
    $stmt->execute();             // 実行

    return $stmt->fetchObject('TableNo'); // 結果を返す (1件のみ)
}

    // 新しいテーブル情報を追加するメソッド
    public function add_table_no(TableNo $tableNo) {
        $dbh = DAO::get_db_connect(); // データベース接続を取得
        $sql = "INSERT INTO tableNo (tableNo, yobidasiState, syokujistate, kaiinID) 
                VALUES (:tableNo, :yobidasiState, :syokujistate, :kaiinID)"; // SQL文
        $stmt = $dbh->prepare($sql);   // ステートメント準備

        // パラメータバインド
        $stmt->bindParam(':tableNo', $tableNo->tableNo);
        $stmt->bindParam(':yobidasiState', $tableNo->yobidasiState, PDO::PARAM_INT);
        $stmt->bindParam(':syokujistate', $tableNo->syokujistate, PDO::PARAM_INT);
        $stmt->bindParam(':kaiinID', $tableNo->kaiinID, PDO::PARAM_INT);

        return $stmt->execute(); // 実行し、成功したかどうかを返す
    }

    // テーブル情報を更新するメソッド
    public function update_table_no(TableNo $tableNo) {
        $dbh = DAO::get_db_connect(); // データベース接続を取得
        $sql = "UPDATE tableNo 
                SET yobidasiState = :yobidasiState, syokujistate = :syokujistate, kaiinID = :kaiinID 
                WHERE tableNo = :tableNo"; // SQL文
        $stmt = $dbh->prepare($sql);   // ステートメント準備

        // パラメータバインド
        $stmt->bindParam(':tableNo', $tableNo->tableNo);
        $stmt->bindParam(':yobidasiState', $tableNo->yobidasiState, PDO::PARAM_INT);
        $stmt->bindParam(':syokujistate', $tableNo->syokujistate, PDO::PARAM_INT);
        $stmt->bindParam(':kaiinID', $tableNo->kaiinID, PDO::PARAM_INT);

        return $stmt->execute(); // 実行し、成功したかどうかを返す
    }

    // テーブル情報を削除するメソッド
    public function delete_table_no(int $tableNo) {
        $dbh = DAO::get_db_connect(); // データベース接続を取得
        $sql = "DELETE FROM tableNo WHERE tableNo = :tableNo"; // SQL文
        $stmt = $dbh->prepare($sql);   // ステートメント準備
        $stmt->bindParam(':tableNo', $tableNo); // パラメータバインド

        return $stmt->execute(); // 実行し、成功したかどうかを返す
    }
}
?>
