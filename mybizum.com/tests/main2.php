<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$includes = [
    'clsBlockchain.php',
    'Clsblock.php',
    'Clstransaction.php',
    'DBCommand.php',
    'DBConnection.php'
];

foreach ($includes as $file) {
    if (file_exists($file)) {
        include_once $file;
    } else {
        echo "❌ No se encontró el archivo requerido: $file<br>";
        exit();
    }
}

echo "✅ Archivos cargados correctamente.<br>";

// Recoger datos del formulario GET
if (!isset($_GET['from'], $_GET['to'], $_GET['amount'])) {
    exit("❌ Faltan datos del formulario.");
}

$sender = $_GET['from'];
$receiver = $_GET['to'];
$amount = floatval($_GET['amount']);

if ($amount <= 0) {
    exit("❌ La cantidad debe ser mayor que cero.");
}

// Conectar a la base de datos
$connection = new DBConnection('172.17.0.3,1433', 'BlockchainDB', 'sa', 'MiP@ssw0rd2024');
$pdoObject = $connection->getPDOObject();

if (!$pdoObject) {
    exit("❌ Error al conectar a la base de datos.");
}
echo "✅ Conexión a la base de datos correcta.<br>";

$dbCommand = new DBCommand($pdoObject);
$myBlockchain = new Blockchain($dbCommand);

// Crear la transacción desde el formulario
$tx = new Transaction($sender, $receiver, $amount);

// Obtener índice y hash previo
$index = count($myBlockchain->chain);
$previousHash = $index === 0 ? '0' : $myBlockchain->chain[$index - 1]->hash;


$block = new Block($index, time(), [$tx], $previousHash);

// Añadir a la blockchain en memoria
$myBlockchain->addBlock($block);

// Guardar en base de datos (solo llamamos a saveSQL del bloque)
try {
    global $dbCommand;
    $GLOBALS['dbCommand'] = $dbCommand; // por si tu clase usa global

    $block->saveSQL(); // guarda bloque y transacciones desde dentro

    echo "<h2>✅ Transacción guardada correctamente</h2>";
    echo "<p>De: <strong>$sender</strong> → A: <strong>$receiver</strong> | Cantidad: <strong>$amount €</strong></p>";
    echo "<pre>" . json_encode($myBlockchain, JSON_PRETTY_PRINT) . "</pre>";
} catch (Exception $e) {
    echo "❌ Error al guardar en base de datos: " . $e->getMessage();
}
