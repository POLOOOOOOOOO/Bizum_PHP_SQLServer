<?php
require_once 'Clsblockchain.php';
require_once 'Clstransaction.php';
require_once 'Clsblock.php';

class ClsBizum
{
    private $dbCommand;

    public function __construct($dbCommand)
    {
        $this->dbCommand = $dbCommand;
    }

    public function enviar(string $sender, string $receiver, float $amount): Transaction
    {
        if (!$sender || !$receiver || $amount <= 0) {
            throw new Exception("❌ Datos inválidos para enviar Bizum.");
        }

        $blockchain = new Blockchain();
        $blockchain->loadFromDatabase($this->dbCommand);
        $transaction = new Transaction($sender, $receiver, $amount);

        $latestBlock = $blockchain->getLatestBlock();

        if (!$latestBlock || !isset($latestBlock->index)) {
            throw new Exception("❌ No se pudo obtener el último bloque. La blockchain está vacía o mal inicializada.");
        }

        $newBlock = new Block(
            $latestBlock->index + 1,
            time(),
            [$transaction],
            $latestBlock->hash
        );
       
//ME ESTA PETANDO AQUI DEBAJO
        // Guardar bloque y transacción
    
        $blockID = $newBlock->savePDO($this->dbCommand);
       

        $transaction->savePDO($this->dbCommand, $blockID);

        // Añadir a blockchain local
        $blockchain->addBlock($newBlock);

        return $transaction;
    }
}
