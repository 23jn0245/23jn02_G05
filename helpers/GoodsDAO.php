<?php
require_once 'DAO.php';

// 商品クラス
class Goods
{
    public $goodscode;          // 商品コード
    public $name;               // 商品名
    public $price;              // 価格
    public $img;                // 商品画像
    public $groupCode;          // グループコード
    public $detail;             // 商品詳細
}

// GoodsDAOクラス
class GoodsDAO
{
    // 推奨商品を取得するメソッド
    public function get_recommend_goods()
    {
        $dbh = DAO::get_db_connect();  // DB接続を取得
        $sql = "SELECT * FROM goods ORDER BY groupCode ASC";  // 商品コード66と67を除いた商品を取得
        $stmt = $dbh->prepare($sql);   // SQLクエリを準備
        $stmt->execute();  // クエリを実行

        $data = [];
        // 商品をGoodsクラスのインスタンスとして取得
        while ($row = $stmt->fetchObject('Goods')) {
            $data[] = $row;
        }
        return $data;  // 推奨商品のリストを返す
    }

    // グループコードに基づいて商品を取得するメソッド
    public function get_goods_by_groupcode(int $groupcode)
    {
        $dbh = DAO::get_db_connect();  // DB接続を取得
        $sql = "SELECT * FROM goods WHERE groupcode = :groupcode  ORDER BY groupCode ASC";  // 指定されたグループコードに一致し、商品コード66と67を除いた商品を取得
        $stmt = $dbh->prepare($sql);   // SQLクエリを準備
        $stmt->bindValue(':groupcode', $groupcode, PDO::PARAM_INT);  // パラメータとしてグループコードをバインド
        $stmt->execute();  // クエリを実行

        $data = [];
        // 商品をGoodsクラスのインスタンスとして取得
        while ($row = $stmt->fetchObject('Goods')) {
            $data[] = $row;
        }
        return $data;  // グループに関連する商品のリストを返す
    }

    // 商品コードに基づいて商品情報を取得するメソッド
    public function get_goods_info($goodsCode)
    {
        $dbh = DAO::get_db_connect();  // DB接続を取得
        $sql = "SELECT name, price FROM Goods WHERE goodscode = :goodscode";  // 商品コードに基づいて商品名と価格を取得
        $stmt = $dbh->prepare($sql);   // SQLクエリを準備
        $stmt->bindParam(':goodscode', $goodsCode, PDO::PARAM_INT);  // パラメータとして商品コードをバインド
        $stmt->execute();  // クエリを実行
        
        return $stmt->fetch(PDO::FETCH_ASSOC);  // 商品情報を連想配列として返す
    }

    // 商品名で検索を行うメソッド
    public function search_goods_by_name($keyword)
    {
        $dbh = DAO::get_db_connect();  // DB接続を取得
        $sql = "SELECT * FROM goods WHERE name LIKE :keyword ORDER BY groupCode ASC";  // 商品名にキーワードが含まれる商品を検索
        $stmt = $dbh->prepare($sql);   // SQLクエリを準備
        $stmt->bindValue(':keyword', '%' . $keyword . '%', PDO::PARAM_STR);  // キーワードをパラメータとしてバインド
        $stmt->execute();  // クエリを実行

        $data = [];
        // 商品をGoodsクラスのインスタンスとして取得
        while ($row = $stmt->fetchObject('Goods')) {
            $data[] = $row;
        }
        return $data;  // 商品名で検索した商品のリストを返す
    }

    // 商品コードに基づいて商品を取得するメソッド
    public function get_goods_by_code($goodsCode)
    {
        $dbh = DAO::get_db_connect();  // DB接続を取得
        $sql = "SELECT * FROM Goods WHERE goodsCode = :goodsCode ORDER BY groupCode ASC";  // 商品コードに基づいて商品を取得
        $stmt = $dbh->prepare($sql);   // SQLクエリを準備
        $stmt->bindValue(':goodsCode', $goodsCode, PDO::PARAM_INT);  // パラメータとして商品コードをバインド
        $stmt->execute();  // クエリを実行
        return $stmt->fetch(PDO::FETCH_ASSOC);  // 1件の商品データを連想配列として返す
    }

    // 商品コードに基づいて商品を削除するメソッド
    public function delete_goods_by_code($goodsCode)
    {
        $dbh = DAO::get_db_connect();  // DB接続を取得
        $sql = "DELETE FROM Goods WHERE goodsCode = :goodsCode";  // 商品コードに基づいて商品を削除
        $stmt = $dbh->prepare($sql);   // SQLクエリを準備
        $stmt->bindValue(':goodsCode', $goodsCode, PDO::PARAM_INT);  // パラメータとして商品コードをバインド

        try {
            $stmt->execute();  // クエリを実行
            return $stmt->rowCount();  // 削除された行数を返す
        } catch (PDOException $e) {
            // エラー発生時の処理
            error_log('Delete Error: ' . $e->getMessage());  // エラーメッセージをログに記録
            return false;  // 削除失敗の場合は false を返す
        }
    }

        // 商品を追加するメソッド
    public function add_goods($name, $price, $img, $groupCode, $detail)
    {
        $dbh = DAO::get_db_connect();  // DB接続を取得

        // $img に "../image/" を追加
        $imgPath = "../image/" . $img;  // ../image/ を画像パスの先頭に付加

        $sql = "INSERT INTO Goods (name, price, img, groupCode, detail) 
                VALUES (:name, :price, :img, :groupCode, :detail)";  // 商品を追加するINSERT文を準備
        $stmt = $dbh->prepare($sql);   // SQLクエリを準備

        // パラメータをバインド
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->bindValue(':price', $price, PDO::PARAM_INT);
        $stmt->bindValue(':img', $imgPath, PDO::PARAM_STR);  // 修正した画像パスを使用
        $stmt->bindValue(':groupCode', $groupCode, PDO::PARAM_INT);
        $stmt->bindValue(':detail', $detail, PDO::PARAM_STR);

        try {
            $stmt->execute();  // クエリを実行
            return $dbh->lastInsertId();  // 挿入された商品のIDを返す
        } catch (PDOException $e) {
            // エラー発生時の処理
            error_log('Insert Error: ' . $e->getMessage());  // エラーメッセージをログに記録
            return false;  // 挿入失敗の場合は false を返す
        }
    }


    // 商品情報を更新するメソッド
    public function updategoods($goodscode, $name, $price, $img, $groupCode, $detail)
    {
        $dbh = DAO::get_db_connect();  // DB接続を取得
        $sql = "UPDATE Goods 
                SET name = :name, 
                    price = :price, 
                    img = :img, 
                    groupCode = :groupCode, 
                    detail = :detail 
                WHERE goodsCode = :goodsCode";  // 商品コードに基づいて商品情報を更新するUPDATE文を準備
        $stmt = $dbh->prepare($sql);   // SQLクエリを準備

        // パラメータをバインド
        $stmt->bindValue(':goodsCode', $goodscode, PDO::PARAM_INT);  // 商品コード
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);           // 商品名
        $stmt->bindValue(':price', $price, PDO::PARAM_INT);         // 価格
        $stmt->bindValue(':img', $img, PDO::PARAM_STR);             // 商品画像
        $stmt->bindValue(':groupCode', $groupCode, PDO::PARAM_INT); // グループコード
        $stmt->bindValue(':detail', $detail, PDO::PARAM_STR);       // 商品詳細

        try {
            $stmt->execute();  // クエリを実行
            return $stmt->rowCount();  // 更新された行数を返す
        } catch (PDOException $e) {
            // エラー発生時の処理
            error_log('Update Error: ' . $e->getMessage());  // エラーメッセージをログに記録
            return false;  // 更新失敗の場合は false を返す
        }
    }
}
?>
