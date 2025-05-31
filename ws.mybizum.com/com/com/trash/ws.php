<?php

header("Access-Control-Allow-Origin: http://mybizum.com:8080");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

session_start();

require_once 'utils/dbo/daoConnection.php';
require_once 'utils/dbo/daoCommand.php';
require_once 'utils/mailtools/mail_sender.php';
require_once 'security/clsUserManager.php';
require_once 'utils/dbo/daoManager.php';

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
            echo "<Response><Error>Faltan datos.</Error></Response>";
            exit();
        }

        $userManager->login($_GET['username'], $_GET['password']);
        break;


    case "logout":
        $userManager->logout($_GET['username']);
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

        // $userManager->checkpassword($_GET['password']);
        // break;

    case "enviar":
        require_once './bizum/clsbizum.php'; 

        if (empty($_GET['from']) || empty($_GET['to']) || empty($_GET['amount'])) {
            echo json_encode(["status" => "error", "message" => "Faltan datos para enviar el Bizum."]);
            exit;
        }

        $from = $_GET['from'];
        $to = $_GET['to'];
        $amount = floatval($_GET['amount']);

        try {
            $transaction = ClsBizum::enviar($from, $to, $amount, $dbCommand);

            echo json_encode([
                "status" => "success",
                "message" => "Bizum enviado correctamente.",
                "transaction" => [
                    "from" => $transaction->sender,
                    "to" => $transaction->receiver,
                    "amount" => $transaction->amount
                ]
            ]);
        } catch (Exception $e) {
            echo json_encode([
                "status" => "error",
                "message" => $e->getMessage()
            ]);
        }
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Acción no válida."]);
        break;
}
?>


// Register: OK
// localhost:40080/gen-web/gen-web/PHP/index.php?action=register&username=polrabascall&name=Pol&lastname=Rabascall&password=Test12345!!&email=polrabascall@gmail.com&gender=Hombre&def_lang=es
// http://localhost:40080/gen-web/PHP/index.php?action=register&username=PauAllendee&name=Pau&lastname=Allende&password=C0ntraseña2004!!&email=pauallendeherraiz@gmail.com&gender=Hombre&def_lang=es
//?action=register&username=aribas&name=Alex&lastname=Rab&password=Test12345!!&email=aribas@gmail.com
// NUEVA URL
// http://localhost:40080/gen-web/PHP/index.php?action=register&username=aribas&name=Alex&lastname=Rab&password=Test12345!!&email=aribas@gmail.com&gender=Hombre&def_lang=es


// Account Validate: OK
// http://localhost:40080/gen-web/gen-web/PHP/index.php?action=accvalidate&username=polrabascall&code=
// http://localhost:40080/gen-web/PHP/index.php?action=accvalidate&username=PauAllendee&code=40381

// Login: OK
// localhost:40080/gen-web/gen-web/PHP/index.php?action=login&username=polrabascall&password=Test12345!!
// http://localhost:40080/gen-web/PHP/index.php?action=login&username=PauAllendee&password=C0ntraseña2004!!


// Logout: OK
// localhost:40080/gen-web/gen-web/PHP/index.php?action=logout
// http://localhost:40080/gen-web/PHP/index.php?action=logout

// Change Password: OK
// localhost:40080/gen-web/gen-web/PHP/index.php?action=changepass&username=polrabascall&password=Test12345!!&newpassword=NewPassword12345!!
// http://localhost:40080/gen-web/PHP/index.php?action=changepass&username=PauAllendee&password=C0ntraseña2004!!&newpassword=NewPassword12345!!

// View Active Connections: OK
// localhost:40080/gen-web/gen-web/PHP/index.php?action=viewcon
// http://localhost:40080/gen-web/PHP/index.php?action=viewcon

// View Historical Connections: OK
//localhost:40080/gen-web/gen-web/PHP/index.php?action=viewconhist
// http://localhost:40080/gen-web/PHP/index.php?action=viewconhist