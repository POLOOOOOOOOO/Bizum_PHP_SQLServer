<?php
class Block
{
    public int $index;
    public int $timestamp;
    public array $transactions;
    public string $previousHash;
    public string $hash;

    public function __construct(int $index, int $timestamp, array $transactions, string $previousHash = '')
    {
        $this->index = $index;
        $this->timestamp = $timestamp;
        $this->transactions = $transactions;
        $this->previousHash = $previousHash;
        $this->hash = $this->calculateHash();
    }

    public function calculateHash(): string
    {
        return md5($this->index . $this->timestamp . json_encode($this->transactions) . $this->previousHash);
    }

    public function savePDO($dbCommand): int
    {

//AQUI ES NULL???
        $result = $dbCommand->execute('AddBlock', [
            date('Y-m-d H:i:s', $this->timestamp),
            $this->hash,
            $this->previousHash
        ]);
       
    
        // if (!is_array($result) || !isset($result[0]['BlockID'])) {
        //     throw new Exception("\u274c Error al obtener el ID del bloque desde AddBlock");
        // }
        // var_dump($result);
        //
   
        // if (!is_array($result) || !isset($result[0]['BlockID'])) {
        //     throw new Exception("\u274c Error al obtener el ID del bloque desde AddBlock");
        // }
        var_dump($result);
    die();
        return (int)$result[0]['BlockID'];
    }
}
