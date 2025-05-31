<?php

class Transaction
{
    public $sender;
    public $receiver;
    public $amount;
    //private $dbCommand;

    public function __construct( $sender, $receiver, $amount)
    {
        $this->sender = $sender;
        $this->receiver = $receiver;
        $this->amount = $amount;
        //$this->dbCommand = $dbCommand;
    }

    public function saveSQL($blockID)
    {
        try {

            // Llamar al procedimiento almacenado AddTransaction
            global $dbCommand;
            $dbCommand->execute('AddTransaction', array($this->sender, $this->receiver, $this->amount, $blockID));
           
            


            
        } catch (PDOException $e) {
            echo "Error al guardar la transacción: " . $e->getMessage();
        }
    }

    public function __toString()
    {
        return $this->sender . $this->receiver . $this->amount;
    }
}
?>