<?php

declare(strict_types=1);

namespace pw2_grupo_21\Model;
use DateTime;

final class Transaction {

    private $id;
    private $senderid;
    private $receiverid;
    private $sendername;
    private $receivername;
    private $amount;
    private $date;
    private $payed;

    public function __construct() {}

    public static function withParams(
        $sender_id,  
        $receiver_id,  
        $amount,  
        $date,  
        $sender_name, 
        $receiver_name,
        $payed
    ) {
        $instance = new self();
        $instance->senderid = $sender_id;
        $instance->receiverid = $receiver_id;
        $instance->amount = $amount;
        $instance->date = $date;
        $instance->sendername = $sender_name;
        $instance->receivername = $receiver_name;
        $instance->payed = $payed;

        return $instance;
    }

    public static function withId(
        $id,
        $sender_id,  
        $receiver_id,  
        $amount,  
        $date,  
        $sender_name, 
        $receiver_name,
        $payed
    ) {
        $instance = new self();
        $instance->id = $id;
        $instance->senderid = $sender_id;
        $instance->receiverid = $receiver_id;
        $instance->amount = $amount;
        $instance->date = $date;
        $instance->sendername = $sender_name;
        $instance->receivername = $receiver_name;
        $instance->payed = $payed;

        return $instance;
    }


    public function senderName() {
        return $this->sendername;
    }
    
    public function setSenderName($sendername) {
        $this->sendername = $sendername;
        return $this;
    }

    public function receiverName() {
        return $this->receivername;
    }

    public function setReceiverName($receivername) {
        $this->receiver_name = $receivername;
        return $this;
    }

    public function Id() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    
    public function senderId() {
        return $this->senderid;
    }

    
    public function setSenderId($sender_id) {
        $this->senderid = $sender_id;
        return $this;
    }

    
    public function receiverId() {
        return $this->receiverid;
    }

    
    public function setReceiverId($receiverid) {
        $this->receiverid = $receiverid;
    }

    
    public function amount() {
        return $this->amount;
    }

    
    public function setAmount($amount) {
        $this->amount = $amount;
        return $this;
    }

    public function payed() {
        return $this->payed;
    }

    
    public function setPayed($payed) {
        $this->payed = $payed;
        return $this;
    }

    public function date() {
        return $this->date;
    }

    public function setDate($date) {
        $this->date = $date;
        return $this;
    }



}