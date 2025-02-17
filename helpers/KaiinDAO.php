<?php
require_once 'DAO.php';

class Kaiin {
    public int $kaiinID;
    public string $sei;
    public string $mei;
    public string $EMail;
    public string $tel;
    public string $psword;
}

class KaiinDAO {
    private $table = 'Kaiin';

    // すべての会員情報を取得するメソッド
    public function get_kaiin_list() {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT * FROM " . $this->table;
        $stmt = $dbh->prepare($sql);
        $stmt->execute();
        $data = [];
        while ($row = $stmt->fetchObject('Kaiin')) {
            $data[] = $row;
        }
        return $data;
    }

    // 特定の会員情報を取得するメソッド
    public function get_kaiin_by_id($kaiinID) {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT * FROM " . $this->table . " WHERE kaiinID = :kaiinID";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':kaiinID', $kaiinID, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchObject('Kaiin');
    }

    // 会員情報を更新するメソッド
    public function update_kaiin($kaiinID, $tel, $password, $sei, $mei) {
        $dbh = DAO::get_db_connect();
        //$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE " . $this->table . " SET tel = :tel, psword = :password, sei = :sei, mei = :mei WHERE kaiinID = :kaiinID";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':kaiinID', $kaiinID, PDO::PARAM_INT);
        $stmt->bindParam(':tel', $tel, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $stmt->bindParam(':sei', $sei, PDO::PARAM_STR);
        $stmt->bindParam(':mei', $mei, PDO::PARAM_STR);
        return $stmt->execute();
    }

    // 会員情報を追加するメソッド
    public function create_kaiin($data) {
        try {
            $dbh = DAO::get_db_connect();

            // Eメールの重複チェック
            $sql_check = "SELECT COUNT(*) AS count FROM Kaiin WHERE EMail = :email";
            $stmt_check = $dbh->prepare($sql_check);
            $stmt_check->bindParam(':email', $data['EMail'], PDO::PARAM_STR);
            $stmt_check->execute();
            $row = $stmt_check->fetch(PDO::FETCH_ASSOC);
            if ($row['count'] > 0) {
                return false;
            }

            // パスワードをハッシュ化
            //$hashedPassword = password_hash($data['psword'], PASSWORD_DEFAULT);

            // 新しい会員IDの生成
            $sql_max_id = "SELECT MAX(kaiinID) AS maxID FROM Kaiin";
            $stmt_max = $dbh->prepare($sql_max_id);
            $stmt_max->execute();
            $row = $stmt_max->fetch(PDO::FETCH_ASSOC);
            $newKaiinID = $row['maxID'] + 1;

            // 新規会員情報の挿入
            $sql = "INSERT INTO Kaiin (kaiinID, sei, mei, EMail, tel, psword) VALUES (:kaiinID, :sei, :mei, :email, :tel, :psword)";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':kaiinID', $newKaiinID, PDO::PARAM_INT);
            $stmt->bindParam(':sei', $data['sei'], PDO::PARAM_STR);
            $stmt->bindParam(':mei', $data['mei'], PDO::PARAM_STR);
            $stmt->bindParam(':email', $data['EMail'], PDO::PARAM_STR);
            $stmt->bindParam(':tel', $data['tel'], PDO::PARAM_STR);
            $stmt->bindParam(':psword', $data['psword'], PDO::PARAM_STR);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            return false;
        }
    }
    // メールアドレスまたは電話番号で会員情報を取得するメソッド（ログイン用）
    public function get_member($EMailOrPhone, $psword) {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT * FROM Kaiin WHERE EMail = :EMailOrPhone";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':EMailOrPhone', $EMailOrPhone, PDO::PARAM_STR);
        $stmt->execute();
        $memberData = $stmt->fetchObject("Kaiin");

        if($memberData !== false){
            if($psword === $memberData->psword){
                return $memberData;
            }
        }
        return false;
    }
     // 会員IDを指定して会員情報を削除するメソッド
     public function delete_kaiin_by_id($kaiinID) {
        $dbh = DAO::get_db_connect();
        $sql = "DELETE FROM " . $this->table . " WHERE kaiinID = :kaiinID";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':kaiinID', $kaiinID, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
