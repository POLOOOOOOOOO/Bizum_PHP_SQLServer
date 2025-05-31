<?php

class ClsBizum
{
    private DBCommand $dbCommand;

    public function __construct(DBCommand $dbCommand)
    {
        $this->dbCommand = $dbCommand;
    }

    public function enviar(string $from, string $to, float $amount): void
    {
        $lastBlockXml = $this->dbCommand->execute('GetLastBlockId');
        $previousHash = "0";
        $timestamp = time();

        if ($lastBlockXml) {
            $xml = simplexml_load_string($lastBlockXml);
            $previousHash = (string)$xml->Hash;
        }

        $rawData = $timestamp . $previousHash . $from . $to . $amount;
        $hash = md5($rawData);

        $blockResult = $this->dbCommand->execute('AddBlock', [
            date('Y-m-d H:i:s', $timestamp),
            $hash,
            $previousHash
        ]);

        if ($blockResult === null) {
            header("Content-Type: text/plain");
            echo "❌ AddBlock devolvió null";
            exit;
        }

        $blockID = (int)$blockResult;

        $this->dbCommand->execute('AddTransaction', [
            $from,
            $to,
            $amount,
            $blockID
        ]);

        header("Content-Type: text/xml");
        $response = new SimpleXMLElement("<Response></Response>");
        $response->addChild('Status', 'success');
        $response->addChild('BlockID', $blockID);
        $response->addChild('Hash', $hash);
        $response->addChild('PreviousHash', $previousHash);
        $response->addChild('Timestamp', date('Y-m-d H:i:s', $timestamp));

        $tx = $response->addChild('Transaction');
        $tx->addChild('From', htmlspecialchars($from));
        $tx->addChild('To', htmlspecialchars($to));
        $tx->addChild('Amount', $amount);

        echo $response->asXML();
    }
}
