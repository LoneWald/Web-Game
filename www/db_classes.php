<?php 

class Account{
    public $id;
    public $login;
    public $password;
    public $email;
    public $nickname;

    function __construct($id, $login, $password, $email, $nickname){
        $this->id = $id;
        $this->login = $login;
        $this->password = $password;
        $this->email = $email;
        $this->nickname = $nickname;
    }
    
    
}

class Game{
    public $id;
    public $date;
    public $result;

    function __construct($id, $date, $result){
        $this->id = $id;
        $this->date = $date;
        $this->resultv = $result;
    }
}

class Querry{

    function InsertGame($result){
        include_once "db_connect_mysqli.php";
        $today = date("Y-m-d");
        $account = json_decode($_COOKIE['currentAccount']);
        $conn->begin_transaction();
        
        try{
            $stmt = $mysqli->prepare('INSERT INTO game (date, result) VALUES (?, ?)');
            $stmt->bind_param('si', $today, $result);
            $stmt->execute();

            $last_id = $mysqli->insert_id;

            $stmt = $mysqli->prepare('INSERT INTO account_game VALUES (?, ?)');
            $stmt->bind_param('ii', (int)$account->id, $last_id);
            $stmt->execute();
            $conn->commit();

            // $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // $pdo->beginTransaction();
            // $sql = $pdo->prepare('INSERT INTO game (date, result) VALUES (?, ?)');
            // $sql->execute(array($today, $result));
            // $last_id = $pdo->lastInsertId();
            // $sql = $pdo->prepare('INSERT INTO account_game VALUES (?, ?)');
            // $sql->execute(array($account->id, $last_id));
            // $pdo->commit();
        }catch (mysqli_sql_exception $exception) {
            $conn->rollback();
            throw $exception;
            }
        // catch(PDOException $e){
        //     $pdo->rollBack();
        //     echo "Ошибка: " . $e->getMessage();
        // }
    }
}
?>