<?php

declare(strict_types=1);

namespace pw2_grupo_21\Repository;

use pw2_grupo_21\Model\User;
use pw2_grupo_21\Model\Transaction;
use pw2_grupo_21\Model\Repository;
use PDO;
use DateTime;

final class MySQLRepository implements Repository {
    private const DATE_FORMAT = 'Y-m-d H:i:s';
    private const DATE_TRANSACTION = 'Y-m-d';

    private PDOSingleton $database;

    public function __construct(PDOSingleton $database) {
        $this->database = $database;
    }

    public function checkIfExists(String $email) {
        $query = <<<'QUERY'
        SELECT user_id FROM User WHERE email = :email
        QUERY;

        $statement = $this->database->connection()->prepare($query);
        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->execute();

        $num_rows = $statement->rowCount();

        return $num_rows;
    }

    public function save(User $user): void {
       
        $query = <<<'QUERY'
        INSERT INTO User(name, lastname, email, password, birthdate, phone, createdAt, updatedAt)
        VALUES(:name, :lastname, :email, :password, :birthdate, :phone, :createdAt, :updatedAt)
        QUERY;
        $statement = $this->database->connection()->prepare($query);

        $name = $user->name();
        $lastName = $user->lastName();
        $email = $user->email();
        $password = hash('sha256', $user->password());
        $phone = $user->phone();
        $birthdate = $user->birthdate();
        $createdAt = $user->createdAt()->format(self::DATE_FORMAT);
        $updatedAt = $user->updatedAt()->format(self::DATE_FORMAT);

        $statement->bindParam('name', $name, PDO::PARAM_STR);
        $statement->bindParam('lastname', $lastName, PDO::PARAM_STR);
        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->bindParam('password', $password, PDO::PARAM_STR);
        $statement->bindParam('birthdate', $birthdate, PDO::PARAM_STR);
        $statement->bindParam('phone', $phone, PDO::PARAM_STR);
        $statement->bindParam('createdAt', $createdAt, PDO::PARAM_STR);
        $statement->bindParam('updatedAt', $updatedAt, PDO::PARAM_STR);
        
        $statement->execute();
    }

    public function getIdByEmail(String $email) {
        $query = <<<'QUERY'
        SELECT user_id FROM User WHERE email = :email;
        QUERY;

        $statement = $this->database->connection()->prepare($query);
        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->execute();
        $user = $statement->fetch();

        if ($user['user_id'] != null) return $user['user_id'];

        return null;
    }

    public function saveToken($id, $token): void {
        $query = <<<'QUERY'
        INSERT INTO Token(user_id, token)
        VALUES(:user_id, :token)
        QUERY;

        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('user_id', $id, PDO::PARAM_STR);
        $statement->bindParam('token', $token, PDO::PARAM_STR);

        $statement->execute();
    }

    public function getUserIdFromToken($token) {
        $query = <<<'QUERY'
        SELECT user_id FROM Token WHERE token = :token AND activated = 0;
        QUERY;

        $statement = $this->database->connection()->prepare($query);
        $statement->bindParam('token', $token, PDO::PARAM_STR);
        $statement->execute();
        $user = $statement->fetch();

        if($user && $user['user_id'] != null) {

            $query = <<<'QUERY'
            UPDATE Token SET activated = 1 WHERE user_id = :user_id;
            QUERY;

            $statement = $this->database->connection()->prepare($query);
            $statement->bindParam('user_id', $user['user_id'], PDO::PARAM_INT);
            $statement->execute();

            $query = <<<'QUERY'
            UPDATE User SET balance = 20.0 WHERE user_id = :user_id;
            QUERY;

            $statement = $this->database->connection()->prepare($query);
            $statement->bindParam('user_id', $user['user_id'], PDO::PARAM_INT);
            $statement->execute();

            return $user['user_id'];
        }
        return null;
    }

    public function checkCredentials(String $email, String $pass) {
        $query = <<<'QUERY'
        SELECT user_id FROM User WHERE password = :password AND email = :email;
        QUERY;

        $password = hash('sha256', $pass);

        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->bindParam('password', $password, PDO::PARAM_STR);

        $statement->execute();
        $user = $statement->fetch();

        if ($user && $user['user_id'] != null) {
            $query = <<<'QUERY'
            SELECT activated FROM Token WHERE user_id = :user_id;
            QUERY;

            $statement = $this->database->connection()->prepare($query);
            $statement->bindParam('user_id', $user['user_id'], PDO::PARAM_INT);
            $statement->execute();

            $activated = $statement->fetch();

            if($activated['activated'] == 1) return $user['user_id'];
            
        }

        return null;
    }

    public function getUserById($id) {

        $query = <<<'QUERY'
        SELECT * FROM User WHERE user_id = :user_id;
        QUERY;

        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('user_id', $id, PDO::PARAM_STR);

        $statement->execute();
        $userInfo = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (empty($userInfo)) {
            return null;
        }

        $user = new User();
        foreach ($userInfo as $row) {
            $user->setId((int)$row['user_id']);
            $user->setName($row['name']);
            $user->setLastName($row['lastname']);
            $user->setEmail($row['email']);
            $user->setPhone($row['phone']);
            $user->setBirthdate($row['birthdate']);
            $user->setPassword($row['password']);
            $user->setIBAN($row['iban']);
            $user->setBalance((float)$row['balance']);
        }

        return $user;
    }

    public function saveIban($iban, $id): void {

        $query = <<<'QUERY'
        UPDATE User SET iban = :iban WHERE user_id = :user_id;
        QUERY;

        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('user_id', $id, PDO::PARAM_STR);
        $statement->bindParam('iban', $iban, PDO::PARAM_STR);

        $statement->execute();
    }

    public function updateBalance(float $balance, Transaction $transaction, int $id) {
        $query = <<<'QUERY'
        UPDATE User SET balance = :balance WHERE user_id = :user_id;
        QUERY;

        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('balance', $balance, PDO::PARAM_STR);
        $statement->bindParam('user_id', $id, PDO::PARAM_STR);

        $statement->execute();

        $query = <<<'QUERY'
        INSERT INTO Transactions(sender_id, receiver_id, sender_name, receiver_name, amount, date, payed)
        VALUES(:sender_id, :receiver_id, :sender_name, :receiver_name, :amount, :date, :payed)
        QUERY;

        $date = $transaction->date()->format(self::DATE_FORMAT);

        $statement = $this->database->connection()->prepare($query);

        $sender_id = $transaction->senderId();
        $receiver_id = $transaction->receiverId();
        $sender_name = $transaction->senderName();
        $receiver_name = $transaction->receiverName();
        $amount = $transaction->amount();
        $payed = (bool)$transaction->payed();

        $statement->bindParam('sender_id', $sender_id, PDO::PARAM_INT);
        $statement->bindParam('receiver_id', $receiver_id, PDO::PARAM_INT);
        $statement->bindParam('sender_name', $sender_name, PDO::PARAM_STR);
        $statement->bindParam('receiver_name', $receiver_name, PDO::PARAM_STR);
        $statement->bindParam('amount', $amount, PDO::PARAM_STR);
        $statement->bindParam('date', $date, PDO::PARAM_STR);
        $statement->bindParam('payed', $payed, PDO::PARAM_BOOL);

        $statement->execute();


    }

    public function getDashboardTransactions(int $user_id) {

        $query = <<<'QUERY'
        SELECT * FROM Transactions WHERE (sender_id = :sender_id OR receiver_id = :receiver_id) AND payed = 1 ORDER BY date DESC LIMIT 5;
        QUERY;

        $statement = $this->database->connection()->prepare($query);
        $statement->bindParam('sender_id', $user_id, PDO::PARAM_STR);
        $statement->bindParam('receiver_id', $user_id, PDO::PARAM_STR);
        $statement->execute();

        $count = 0;
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $data [] = Transaction::withParams($row['sender_id'],$row['receiver_id'],$row['amount'],$row['date'],$row['sender_name'],$row['receiver_name'],$row['payed']);
            $count++;
            if($count == 5) {
                return $data;
            }
        }
        
        if (isset($data)) return $data;

        return null;
    }

    public function getBalanceById(int $user_id) {
        $query = <<<'QUERY'
        SELECT balance FROM User WHERE user_id = :user_id;
        QUERY;

        $statement = $this->database->connection()->prepare($query);
        $statement->bindParam('user_id', $user_id, PDO::PARAM_STR);
        $statement->execute();

        $userbalance = $statement->fetch();

        if ($userbalance != null) return $userbalance['balance'];
        return null;    
    }

    public function getUserByEmail(String $email) {
        $query = <<<'QUERY'
        SELECT * FROM User WHERE email = :email;
        QUERY;

        $statement = $this->database->connection()->prepare($query);
        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->execute();
        $userInfo = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (empty($userInfo)) {
            return null;
        }

        $user = new User();
        foreach ($userInfo as $row) {
            $user->setId((int)$row['user_id']);
            $user->setName($row['name']);
            $user->setLastName($row['lastname']);
            $user->setEmail($row['email']);
            $user->setPhone($row['phone']);
            $user->setBirthdate($row['birthdate']);
            $user->setPassword($row['password']);
            $user->setIBAN($row['iban']);
            $user->setBalance((float)$row['balance']);
        }
        return $user;
    }

    public function saveTransaction(Transaction $transaction) {
        $query = <<<'QUERY'
        INSERT INTO Transactions(sender_id, receiver_id, sender_name, receiver_name, amount, date, payed)
        VALUES(:sender_id, :receiver_id, :sender_name, :receiver_name, :amount, :date, :payed)
        QUERY;

        $date = $transaction->date()->format(self::DATE_FORMAT);

        $statement = $this->database->connection()->prepare($query);

        $sender_id = $transaction->senderId();
        $receiver_id = $transaction->receiverId();
        $sender_name = $transaction->senderName();
        $receiver_name = $transaction->receiverName();
        $amount = $transaction->amount();
        $payed = (bool)$transaction->payed();

        $statement->bindParam('sender_id', $sender_id, PDO::PARAM_INT);
        $statement->bindParam('receiver_id', $receiver_id, PDO::PARAM_INT);
        $statement->bindParam('sender_name', $sender_name, PDO::PARAM_STR);
        $statement->bindParam('receiver_name', $receiver_name, PDO::PARAM_STR);
        $statement->bindParam('amount', $amount, PDO::PARAM_STR);
        $statement->bindParam('date', $date, PDO::PARAM_STR);
        $statement->bindParam('payed', $payed, PDO::PARAM_BOOL);

        $statement->execute();
    }

    public function pendingTransactions(int $id) {

        $query = <<<'QUERY'
        SELECT * FROM Transactions WHERE sender_id = :sender_id AND payed = 0 ORDER BY date DESC;
        QUERY;

        $statement = $this->database->connection()->prepare($query);
        $statement->bindParam('sender_id', $id, PDO::PARAM_STR);

        $statement->execute();

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $data [] = Transaction::withId((int)$row['transaction_id'],$row['sender_id'],$row['receiver_id'],$row['amount'],$row['date'],$row['sender_name'],$row['receiver_name'],$row['payed']);
        }
        
        if (isset($data)) return $data;

        return null;
    }

    public function addAmount(float $amount, int $id) {
        $query = <<<'QUERY'
        UPDATE User SET balance = :balance WHERE user_id = :user_id;
        QUERY;

        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('balance', $amount, PDO::PARAM_STR);
        $statement->bindParam('user_id', $id, PDO::PARAM_STR);

        $statement->execute();

    }

    public function getTransactionById(int $transaction_id) {
        $query = <<<'QUERY'
        SELECT * FROM Transactions WHERE transaction_id = :transaction_id AND payed = 0;
        QUERY;

        $statement = $this->database->connection()->prepare($query);
        $statement->bindParam('transaction_id', $transaction_id, PDO::PARAM_STR);

        $statement->execute();

        $transactionInfo = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (empty($transactionInfo)) {
            return null;
        }

        $transaction = new Transaction();
        foreach ($transactionInfo as $row) {
            $transaction->setId((int)$row['transaction_id']);
            $transaction->setSenderId($row['sender_id']);
            $transaction->setSenderName($row['sender_name']);
            $transaction->setReceiverId($row['receiver_id']);
            $transaction->setReceiverName($row['receiver_name']);
            $transaction->setamount($row['amount']);
            $transaction->setDate($row['date']);
            $transaction->setPayed($row['payed']);
        }
        return $transaction;

    }


    private function updateBalanceById(float $balance, int $id) {
        $query = <<<'QUERY'
        UPDATE User SET balance = :balance WHERE user_id = :user_id;
        QUERY;

        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('balance', $balance, PDO::PARAM_STR);
        $statement->bindParam('user_id', $id, PDO::PARAM_STR);

        $statement->execute();

    }

    public function payTransaction(Transaction $transaction) {
        $oldBalance = $this->getBalanceById((int)$transaction->senderId());

        $newBalance = ((float)$oldBalance - (float)$transaction->amount());

        $this->updateBalanceById($newBalance, (int)$transaction->senderId());

        $oldBalance = $this->getBalanceById((int)$transaction->receiverId());

        $newBalance = ((float)$oldBalance + (float)$transaction->amount());

        $this->updateBalanceById($newBalance, (int)$transaction->receiverId());

        $query = <<<'QUERY'
        UPDATE Transactions SET payed = 1 WHERE transaction_id = :transaction_id;
        QUERY;

        $statement = $this->database->connection()->prepare($query);
        $statement->bindParam('transaction_id', $transaction->Id(), PDO::PARAM_STR);
        

        $statement->execute();

    }

    public function getTransactions(int $user_id) {

        $query = <<<'QUERY'
        SELECT * FROM Transactions WHERE (sender_id = :sender_id OR receiver_id = :receiver_id) AND payed = 1 ORDER BY date DESC;
        QUERY;

        $statement = $this->database->connection()->prepare($query);
        $statement->bindParam('sender_id', $user_id, PDO::PARAM_STR);
        $statement->bindParam('receiver_id', $user_id, PDO::PARAM_STR);
        $statement->execute();

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $data [] = Transaction::withParams($row['sender_id'],$row['receiver_id'],$row['amount'],$row['date'],$row['sender_name'],$row['receiver_name'],$row['payed']);
 
        }
        
        if (isset($data)) return $data;

        return null;
    }

    public function verifyOldPassword(int $user_id, $pass) {
        $query = <<<'QUERY'
        SELECT user_id FROM User WHERE password = :password AND user_id = :user_id;
        QUERY;

        $password = hash('sha256', $pass);

        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('user_id', $user_id, PDO::PARAM_STR);
        $statement->bindParam('password', $password, PDO::PARAM_STR);

        $statement->execute();
        $user = $statement->fetch();

        if ($user && $user['user_id'] != null) {
            return true;
            
        }

        return false;

    }

    public function changePassword(int $user_id, $pass) {
        $query = <<<'QUERY'
        UPDATE User SET password = :password  WHERE user_id = :user_id;
        QUERY;

        $password = hash('sha256', $pass);

        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('user_id', $user_id, PDO::PARAM_STR);
        $statement->bindParam('password', $password, PDO::PARAM_STR);

        $statement->execute();
    }



    
}