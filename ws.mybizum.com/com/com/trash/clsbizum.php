<?php
require_once '../blockchain/Clsblockchain.php';
require_once '../blockchain/Clstransaction.php';
require_once '../blockchain/Clsblock.php';

class ClsBizum
{
    public static function enviar(string $sender, string $receiver, float $amount, DBCommand $dbCommand): Transaction
    {
        if (!$sender || !$receiver || $amount <= 0) {
            throw new Exception("❌ Datos inválidos para enviar Bizum.");
        }

        $blockchain = new Blockchain(); 
        
        $transaction = new Transaction($sender, $receiver, $amount);

        $latestBlock = $blockchain->getLatestBlock();
       
     

        $newBlock = new Block(
            $latestBlock->index + 1,
            time(),
            [$transaction],
            $latestBlock->hash
        );

        // Guardar bloque y transacción
        $blockID = $newBlock->savePDO($dbCommand);
        $transaction->savePDO($dbCommand, $blockID);

        // Añadir a blockchain local
        $blockchain->addBlock($newBlock);

        return $transaction;
    }
}
