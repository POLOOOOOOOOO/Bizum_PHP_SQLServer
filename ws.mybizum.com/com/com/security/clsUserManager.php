<?php

class UserManager
{

    private $dbCommand;

    public function __construct($dbCommand)
    {
        $this->dbCommand = $dbCommand;
    }

    public function register($username, $name, $lastname, $password, $email)
    {
        if (empty($username) || empty($name) || empty($lastname) || empty($password) || empty($email)) {
            header('Content-Type: text/xml');
            echo "<Response><Error>Todos los campos son obligatorios.</Error></Response>";
            exit();
        }

        try {
            $result = $this->dbCommand->execute('sp_user_register', [
                $username,
                $name,
                $lastname,
                $password,
                $email
            ]);

            $xml = simplexml_load_string($result);

            // Si el SP devuelve éxito
            if ($xml && isset($xml->Success->StatusCode) && (string)$xml->Success->StatusCode === "0") {
                // Obtener el código de verificación
                $register_code_result = $this->dbCommand->execute('sp_wdev_get_registercode', [$username, 0]);
                $register_code_xml = simplexml_load_string($register_code_result);

                if ($register_code_xml && isset($register_code_xml->RegisterCode)) {
                    $register_code = (string)$register_code_xml->RegisterCode;

                    // Aquí podrías enviar el correo si lo necesitas
                    /*
                $url = 'https://script.google.com/macros/s/AKfycby-y5lGOOsvUgxkOkpmd9Iq_Il8s2UzNFJzul7M7iK8iAcwq57czUG3umj5-UAHU4eO/exec';
                $asunto = 'Código de registro';
                $cuerpo = "$name, su código de verificación es $register_code";
                enviarCorreo($url, $email, $asunto, $cuerpo, null);
                */
                }

                header('Content-Type: text/xml');
                echo $result;
            } else {
                header('Content-Type: text/xml');
                echo $result; // Devuelve el XML con el error (usuario duplicado, etc.)
            }
        } catch (PDOException $e) {
            header('Content-Type: text/xml');
            echo "<Response><Error>Excepción SQL: " . htmlspecialchars($e->getMessage()) . "</Error></Response>";
        }
        exit();
    }

    public function login($username, $password)
    {
        header("Content-Type: text/xml");

        try {
            $result = $this->dbCommand->execute('sp_user_login', [$username, $password]);
            echo $result;
            exit();
        } catch (PDOException $e) {
            http_response_code(500);
            echo "<error>Database error</error>";
            exit();
        }
    }


    public function logout($username): void {
            try {
                if (!empty($username)) { 
                    
                    $result = $this->dbCommand->execute('sp_user_logout', array($username));

                    
                    header('Content-Type: text/xml');

                    
                    echo $result;
                }
            } catch (PDOException $e) {
                echo 'Error: ' . $e->getMessage();
            }
        }




    public function changePassword($username, $password, $newpassword)
    {
        if (empty($username) || empty($password) || empty($newpassword)) {
            echo "<Response><Error>Todos los campos son obligatorios.</Error></Response>";
            exit();
        }

        try {
            $result = $this->dbCommand->execute('sp_user_change_password', [$username, $password, $newpassword]);

            header('Content-Type: text/xml');
            echo $result;
            exit();
        } catch (PDOException $e) {
            header('Content-Type: text/xml');
            echo "<Response><Error>" . htmlspecialchars($e->getMessage()) . "</Error></Response>";
            exit();
        }
    }

    public function accountValidate($username, $code)
    {
        if (empty($username) || empty($code)) {
            echo "<Response><Error>Todos los campos son obligatorios.</Error></Response>";
            exit();
        }

        try {
            $result = $this->dbCommand->execute('sp_user_accountvalidate', [$username, $code]);

            header('Content-Type: text/xml');
            echo $result;
            exit();
        } catch (PDOException $e) {
            header('Content-Type: text/xml');
            echo "<Response><Error>" . htmlspecialchars($e->getMessage()) . "</Error></Response>";
            exit();
        }
    }

    public function listusers($ssid)
    {
        if (empty($ssid)) {
            echo "<Response><Error>SSID vacío.</Error></Response>";
            exit();
        }

        try {
            $result = $this->dbCommand->execute('sp_list_users2', [$ssid]);

            header('Content-Type: text/xml');
            echo $result;
            exit();
        } catch (PDOException $e) {
            header('Content-Type: text/xml');
            echo "<Response><Error>" . htmlspecialchars($e->getMessage()) . "</Error></Response>";
            exit();
        }
    }

    public function checkpassword($password)
    {
        try {
            $result = $this->dbCommand->execute('sp_check_password', [$password]);

            header('Content-Type: text/xml');
            echo $result;
            exit();
        } catch (PDOException $e) {
            header('Content-Type: text/xml');
            echo "<Response><Error>" . htmlspecialchars($e->getMessage()) . "</Error></Response>";
            exit();
        }
    }
}
