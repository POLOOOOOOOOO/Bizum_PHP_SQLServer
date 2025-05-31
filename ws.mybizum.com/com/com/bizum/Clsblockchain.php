<?php

require_once 'Clsblock.php';

class Blockchain
{
    public array $chain = [];

    public function __construct()
    {
        $this->chain = [];
    }

    private function createGenesisBlock(): Block
    {
        return new Block(0, time(), [], '0');
    }

    public function loadFromDatabase(DBCommand $dbCommand): void
    {
        $this->chain = [];

        $blocks = $dbCommand->execute('GetAllBlocks');
    
        $xml = simplexml_load_string($blocks);

        $ultimoBloque = null;
        $maxBlockID = -1;

        foreach ($xml->Block as $block) {
            $blockID = (int)$block->BlockID;

            if ($blockID > $maxBlockID) {
                $maxBlockID = $blockID;
                $ultimoBloque = $block;
            }
        }
        $block = new Block(
            (int)$ultimoBloque->BlockID,
            strtotime($ultimoBloque->Timestamp),
            [],
            $ultimoBloque->PreviousHash
        );
        $block->hash = $ultimoBloque->Hash ?? $block->calculateHash();

        $this->chain[] = $block;
        
        //header('Content-Type:text/xml');
        //echo $ultimoBloque->asXML();


        // if (!is_array($blocks) || count($blocks) === 0) {
        //     throw new Exception("❌ Error al cargar los bloques: resultado vacío o inesperado");
        // }

        // if (isset($blocks[0]['error'])) {
        //     throw new Exception("❌ Error SQL: " . $blocks[0]['error']);
        // }

        // foreach ($blocks as $row) {
        //     if (!isset($row['BlockID'], $row['Timestamp'], $row['PreviousHash'])) {
        //         continue;
        //     }

        //     $block = new Block(
        //         (int)$row['BlockID'],
        //         strtotime($row['Timestamp']),
        //         [],
        //         $row['PreviousHash']
        //     );
        // }
      
        if (empty($this->chain)) {
            $genesis = $this->createGenesisBlock();

            $genesis->savePDO($dbCommand);
            $this->chain[] = $genesis;
        }
      
         
    }


    public function getLatestBlock(): Block
    {
        if (empty($this->chain)) {
            return $this->createGenesisBlock();
        }

        return $this->chain[count($this->chain) - 1];
    }

    public function isChainValid(): bool
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

    public function __toString(): string
    {
        return json_encode($this->chain);
    }

    public function getChain(): array
    {
        return $this->chain;
    }
}
