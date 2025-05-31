<?php

class Block
{
    public $index;
    public $timestamp;
    public $transactions;
    public $previousHash;
    public $hash;

    public function __construct($index, $timestamp, $transactions, $previousHash = '')
    {
        $this->index = $index;
        $this->timestamp = $timestamp;
        $this->transactions = $transactions;
        $this->previousHash = $previousHash;
        $this->hash = $this->calculateHash();
    }

    public function calculateHash(): string
    {
        // Convertir cada transacción a string (requiere __toString en Transaction)
        $transactionsArray = array_map(function ($tx) {
            return (string)$tx;
        }, is_array($this->transactions) ? $this->transactions : []); // seguridad extra

        return md5($this->index . $this->timestamp . implode('', $transactionsArray) . $this->previousHash);
    }

    public function saveSQL()
    {
        try {
            global $dbCommand;
            $dbCommand->execute('AddBlock', array($this->previousHash));

            $blockID = $dbCommand->execute('GetLastBlockId', array());
            echo "Bloque guardado correctamente con BlockID: " . $blockID . "<br>";

            foreach ($this->transactions as $transaction) {
                $transaction->saveSQL($blockID);
            }
        } catch (PDOException $e) {
            echo "Error al guardar el bloque: " . $e->getMessage();
        }
    }
}
