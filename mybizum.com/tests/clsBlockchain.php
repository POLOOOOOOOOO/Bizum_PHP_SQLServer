<?php

require_once 'Clsblock.php';
require_once 'Clstransaction.php';


class Blockchain
{
    public $chain;
    public $dbCommand;

    public function __construct($dbCommand)
    {
        $this->dbCommand = $dbCommand;
        $this->chain = [];
        $this->createGenesisBlock();
    }

    public function createGenesisBlock()
    {
        // Crear el bloque génesis correctamente, asegurándose de que las transacciones sean un array vacío.
        $genesisBlock = new Block(0, time(), [], null); // Transacciones vacías (array vacío)
        $this->chain[] = $genesisBlock;
    }



    public function getChain()
    {
        return $this->chain;
    }

    public function getLatestBlock()
    {
        return end($this->chain);
    }

    public function addBlock($newBlock)
    {
        $newBlock->previousHash = $this->getLatestBlock()->hash;
        $newBlock->hash = $newBlock->calculateHash();
        array_push($this->chain, $newBlock);
    }

    public function isChainValid()
    {
        for ($i = 1; $i < count($this->chain); $i++) {
            $currentBlock = $this->chain[$i];
            $previousBlock = $this->chain[$i - 1];

            if ($currentBlock->hash !== $currentBlock->calculateHash()) {
                return false;
            }

            if ($currentBlock->previousHash !== $previousBlock->hash) {
                return false;
            }
        }

        return true;
    }

    /*
    public function getGenesisHash(): string
    {
    // Asegúrate de que el primer bloque existe
    if (!empty($this->chain)) {
        return $this->chain[0]->hash;
    }
        

    throw new Exception("El bloque génesis no ha sido creado.");
    }
    */

    public function __toString(): string
    {
        return json_encode($this->chain, JSON_PRETTY_PRINT);
    }



    // function SAVE PARA GUARDAR LA BLOCKCHAIN EN LA BASE DE DATOS no lo suso
    public function addBlockSQL($newBlock)
    {
        // Llamar al procedimiento almacenado para agregar el bloque
        $stmt = $this->dbCommand->execute("EXEC AddBlock ?", [$newBlock->previousHash]);

        // Obtener el BlockID del bloque recién creado
        $blockID = $this->dbCommand->getPDOObject()->lastInsertId();

        // Ahora, agrega las transacciones asociadas al bloque
        foreach ($newBlock->transactions as $transaction) {
            $transaction->saveSQL($this->dbCommand->getPDOObject(), $blockID);
        }

        // Calcular el hash del bloque y actualizarlo
        $hash = $this->dbCommand->execute("EXEC CalculateHash ?", [$blockID]);

        // Actualizar el bloque con el hash
        $stmt = $this->dbCommand->execute("UPDATE Blocks SET Hash = ? WHERE BlockID = ?", [$hash, $blockID]);

        // Añadir el bloque a la cadena de bloques en memoria
        $this->chain[] = $newBlock;
    }
}
