<?php

declare(strict_types=1);

namespace pw2_grupo_21\Model;

interface Repository {
    public function checkIfExists(String $email);
    public function save(User $user): void;
    public function getIdByEmail(String $email);
    public function saveToken($id, $token): void;
    public function getUserIdFromToken($token);

    public function checkCredentials(String $email, String $pass);
    public function getUserById($id);
    public function saveIban($iban, $id): void;
    public function updateBalance(float $balance, Transaction $transaction, int $id);

    public function getDashboardTransactions(int $user_id);
    public function getBalanceById(int $user_id);
    public function getUserByEmail(String $email);

    public function saveTransaction(Transaction $transaction);
    public function pendingTransactions(int $id);
    public function addAmount(float $amount, int $id);
    public function getTransactionById(int $transaction_id);
    public function payTransaction(Transaction $transaction);
    public function getTransactions(int $user_id);
    public function verifyOldPassword(int $user_id, $pass);
    public function changePassword(int $user_id, $pass);
}