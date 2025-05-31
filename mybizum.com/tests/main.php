<?php
require_once 'DBConnection.php';
require_once 'config.php';
require_once 'Clsblockchain.php';
require_once 'Clstransaction.php';
require_once 'Clsblock.php';
require_once 'DBCommand.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

$connection = new DBConnection('172.17.0.3,1433', 'BlockchainDB', 'sa', 'MiP@ssw0rd2024');
$pdoObject = $connection->getPDOObject();

// Verificar la conexión a la base de datos
if (!$pdoObject) {
    echo "Error al conectar a la base de datos.<br>";
    exit();
} else {
    echo "Conexión exitosa a la base de datos.<br>";
}

$myBlockchain = new Blockchain();
$dbCommand = new DBCommand($pdoObject);

// Crear transacciones
$tx1 = new Transaction('Alice', 'Bob', 50);
$tx2 = new Transaction('Bob', 'Charlie', 30);

// Crear bloque 1 con la transacción tx1
$block1 = new Block(1, time(), [$tx1]);
$myBlockchain->addBlock($block1);

// Crear bloque 2 con la transacción tx2
$block2 = new Block(2, time(), [$tx2]);
$myBlockchain->addBlock($block2);

// Aquí se guarda directamente la transacción y el bloque en la base de datos
//if ($myBlockchain->isChainValid()) {
// try {

$tx1->savePDO($dbCommand, $block1->index);

$block1->savePDO($pdoObject);

echo "Bloque 1 y transacción 1 guardados con éxito.<br>";

// Guardar transacción tx2
$tx2->savePDO($dbCommand, $block2->index);

$block2->savePDO($pdoObject);

echo "Bloque 2 y transacción 2 guardados con éxito.<br>";
//} catch (PDOException $e) {
//    echo "Error guardando transacción o bloque: " . $e->getMessage() . "<br>";
//}
//}

echo '<pre>';
print_r($myBlockchain);
echo '</pre>';


echo $myBlockchain->isChainValid();
