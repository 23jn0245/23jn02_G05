<?php
require_once 'DAO.php';

class Shain
{
    public int $shainID;
    public string $sei;
    public string $mei;
    public string $EMail;
    public string $password;
    public ?string $phone_number;
}

class ShainDAO
{

    private $table = 'Shain';

    // すべての社員情報を取得するメソッド
    public function get_shain_list()
    {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT * FROM " . $this->table;
        $stmt = $dbh->prepare($sql);
        $stmt->execute();

        $data = [];
        while ($row = $stmt->fetchObject('Shain')) {
            $data[] = $row;
        }
        return $data;
    }

    // 特定の社員情報を取得するメソッド
    public function get_shain_by_id($shainID)
    {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT * FROM " . $this->table . " WHERE shainID = :shainID";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':shainID', $shainID, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchObject('Shain');
    }

    // 社員情報を更新するメソッド（新規追加）
    public function updateShain($shainID, $phone, $password, $sei, $mei) {
        try {
            $dbh = DAO::get_db_connect();
            $sql = "UPDATE " . $this->table . " 
                    SET phone_number = :phone_number, 
                        password = :password, 
                        sei = :sei, 
                        mei = :mei 
                    WHERE shainID = :shainID";

            $stmt = $dbh->prepare($sql);
            
            // パスワードのハッシュ化処理
            //$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // パラメータのバインド
            $stmt->bindParam(':shainID', $shainID, PDO::PARAM_INT);
            $stmt->bindParam(':phone_number', $phone, PDO::PARAM_STR);
            $stmt->bindParam(':password', $password, PDO::PARAM_STR);
            $stmt->bindParam(':sei', $sei, PDO::PARAM_STR);
            $stmt->bindParam(':mei', $mei, PDO::PARAM_STR);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("社員情報更新エラー: " . $e->getMessage());
            return false;
        }
    }

    // 社員情報を追加するメソッド（重複チェック機能を追加）
    public function create_shain($data)
    {
        try {
            $dbh = DAO::get_db_connect();

            // 電話番号とメールアドレスの重複をチェック
            $sql_check = "SELECT COUNT(*) AS count FROM " . $this->table . " WHERE EMail = :email";
            $stmt_check = $dbh->prepare($sql_check);
            $stmt_check->bindParam(':email', $data['EMail'], PDO::PARAM_STR);
           // $stmt_check->bindParam(':phone', $data['phone_number'], PDO::PARAM_STR);
            $stmt_check->execute();
            $row = $stmt_check->fetch(PDO::FETCH_ASSOC);

            if ($row['count'] > 0) {
                return false; // 重複がある場合は追加しない
            }

            // 新しい shainID を生成
            $sql_max_id = "SELECT COALESCE(MAX(shainID), 0) + 1 AS newID FROM " . $this->table;
            $stmt_max = $dbh->prepare($sql_max_id);
            $stmt_max->execute();
            $newShainID = $stmt_max->fetch(PDO::FETCH_COLUMN);

            if (!$newShainID) {
                error_log("Failed to generate shainID. No data in table?");
                return false;
            }

            // データを挿入
            $sql = "INSERT INTO " . $this->table . " 
                    (shainID, sei, mei, EMail, phone_number, password) 
                    VALUES 
                    (:shainID, :sei, :mei, :email, :phone_number, :password)";
            $stmt = $dbh->prepare($sql);
            
            $stmt->bindParam(':shainID', $newShainID, PDO::PARAM_INT);
            $stmt->bindParam(':sei', $data['sei'], PDO::PARAM_STR);
            $stmt->bindParam(':mei', $data['mei'], PDO::PARAM_STR);
            $stmt->bindParam(':email', $data['EMail'], PDO::PARAM_STR);
            $stmt->bindParam(':phone_number', $data['phone_number'], PDO::PARAM_STR);
            $stmt->bindParam(':password', $data['password'], PDO::PARAM_STR);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            return false;
        }
    }
    // 社員情報を削除するメソッド
    public function delete_shain($shainID)
    {
        $dbh = DAO::get_db_connect();
        $sql = "DELETE FROM " . $this->table . " WHERE shainID = :shainID";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':shainID', $shainID, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // 社員情報更新のパスワードメソッド
    public function updateShainPassword($shainID, $hashedPassword) {
        $dbh = DAO::get_db_connect();
        $sql = "UPDATE " . $this->table . " SET password = :password WHERE shainID = :shainID";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        $stmt->bindParam(':shainID', $shainID, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // ログインメソッド
    public function login($email, $password)
    {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT * FROM " . $this->table . " WHERE EMail = :email AND password = :password";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchObject('Shain');
    }
}
?>
