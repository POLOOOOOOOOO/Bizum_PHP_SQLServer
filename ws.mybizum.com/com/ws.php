<?php

header("Access-Control-Allow-Origin: http://mybizum.com:8080");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");


session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'utils/dbo/daoConnection.php';
require_once 'utils/dbo/daoCommand.php';
require_once 'utils/mailtools/mail_sender.php';
require_once 'security/clsUserManager.php';
require_once 'utils/dbo/daoManager.php';
require_once 'bizum/clsbizum.php';
//require_once 'blockchain/clsblockchain.php';
//require_once 'blockchain/clstransaction.php';
//require_once 'blockchain/clsblock.php';

// Conexion sql pol
$connection = new DBConnection('172.17.0.3,1433', 'PP_DDBB', 'sa', 'MiP@ssw0rd2024');
$pdoObject = $connection->getPDOObject();


// Instanciar managers
$dbCommand = new DBCommand($pdoObject);
$userManager = new UserManager($dbCommand);
$dbManager = new DBManager($dbCommand);


$action = isset($_GET['action']) ? $_GET['action'] : '';

if (empty($action)) {
    echo json_encode(["status" => "error", "message" => "Acción no especificada."]);
    exit;
}

error_log("Acción recibida: " . $action);
switch ($action) {
    case "register":
        if (empty($_GET['username']) || empty($_GET['name']) || empty($_GET['lastname']) || empty($_GET['password']) || empty($_GET['email'])) {
            echo json_encode(["status" => "error", "message" => "Faltan datos para el registro."]);
            exit;
        }
        $userManager->register($_GET['username'], $_GET['name'], $_GET['lastname'], $_GET['password'], $_GET['email']);
        break;

    case "login":
        if (empty($_GET['username']) || empty($_GET['password'])) {
            header("Content-Type: text/xml");
            echo "<Response><num_error>1</num_error><message_error>Faltan datos</message_error></Response>";
            exit();
        }

        $userManager->login($_GET['username'], $_GET['password']);
        break;


    case "logout":
        $userManager->logout($_GET['ssid']);
        break;


    case "changepass":
        if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['newpassword'])) {
            echo json_encode(["status" => "error", "message" => "Faltan datos para cambiar contraseña."]);
            exit;
        }
        $userManager->changePassword($_POST['username'], $_POST['password'], $_POST['newpassword']);
        break;

    case "accvalidate":
        if (empty($_POST['username']) || empty($_POST['code'])) {
            echo json_encode(["status" => "error", "message" => "Faltan datos para validar cuenta."]);
            exit;
        }
        $userManager->accountValidate($_POST['username'], $_POST['code']);
        break;

    case "checkpassword":
        if (empty($_GET['password'])) {
            echo "<Response><Error>Falta la contraseña.</Error></Response>";
            exit;
        }

        $userManager->checkpassword($_GET['password']);
        break;

    case "enviar":
        if (!$_GET['from'] || !$_GET['to'] || !$_GET['amount']) {
            header("Content-Type: text/xml");
            echo "<Response><Status>error</Status><Message>Faltan datos</Message></Response>";
            exit;
        }

        $bizum = new ClsBizum($dbCommand);
        $bizum->enviar($_GET['from'], $_GET['to'], (float)$_GET['amount']);
        break;

    case "getbalance":
        if (empty($_GET['ssid'])) {
            echo "<Response><Status>error</Status><Message>Falta SSID</Message></Response>";
            exit;
        }

        $result = $dbCommand->execute('sp_get_balance_from_ssid', [$_GET['ssid']]);
        header("Content-Type: text/xml");
        echo $result;
        exit;



    default:
        echo json_encode(["status" => "error", "message" => "Acción no válida. eyeyeye"]);
        break;
}
