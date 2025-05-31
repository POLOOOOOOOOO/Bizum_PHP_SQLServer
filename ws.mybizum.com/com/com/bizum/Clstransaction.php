<?php
class Transaction
{
    public string $sender;
    public string $receiver;
    public float $amount;

    public function __construct(string $sender, string $receiver, float $amount)
    {
        $this->sender = $sender;
        $this->receiver = $receiver;
        $this->amount = $amount;
    }

    public function savePDO($dbCommand, int $blockID): int
{
    $result = $dbCommand->execute('AddTransaction', [
        $this->sender,
        $this->receiver,



        
        $this->amount,
        $blockID
    ]);

    if (!is_array($result) || !isset($result[0]['TransactionID'])) {
        throw new Exception("❌ Error al guardar la transacción o recuperar su ID.");
    }

    return (int) $result[0]['TransactionID'];
}
}
