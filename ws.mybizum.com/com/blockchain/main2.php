<?php
require_once 'DBConnection.php';
require_once 'Clsblockchain.php';
require_once 'Clstransaction.php';
require_once 'Clsblock.php';
require_once 'DBCommand.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_GET['action']) && $_GET['action'] === 'sendbizum') {
    $sender = $_GET['from'] ?? null;
    $receiver = $_GET['to'] ?? null;
    $amount = $_GET['amount'] ?? null;

    if ($sender && $receiver && $amount && is_numeric($amount)) {
        $connection = new DBConnection('172.17.0.3,1433', 'BlockchainDB', 'sa', 'MiP@ssw0rd2024');
        $pdo = $connection->getPDOObject();

        if (!$pdo) {
            die("❌ Error de conexión a la base de datos.");
        }

        $dbCommand = new DBCommand($pdo);
        $blockchain = new Blockchain();

        $transaction = new Transaction($sender, $receiver, floatval($amount));

        $latestBlock = $blockchain->getLatestBlock();
        $newBlock = new Block($latestBlock->index + 1, time(), [$transaction], $latestBlock->hash);


        try {
            $blockID = $newBlock->savePDO($dbCommand);
        } catch (Exception $e) {
            die($e->getMessage());
        }


        $transaction->savePDO($dbCommand, $blockID);

        $blockchain->addBlock($newBlock);

        echo "<h3>✅ Bizum enviado correctamente.</h3>";
        echo "<pre>" . print_r($transaction, true) . "</pre>";
        echo "<p>Número de bloque:" . $blockID . "</p>";
    } else {
        echo "❌ Faltan datos o la cantidad no es válida.";
    }
} else {
    echo "❌ Acción no válida.testeo";
}
