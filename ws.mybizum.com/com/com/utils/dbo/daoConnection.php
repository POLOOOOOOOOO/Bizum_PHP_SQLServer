<?php
class DBConnection
{

    private $password;
    private $user;
    private $databaseName;
    private $host;
    // private $port;
    private $db;

    public function __construct($host, $databaseName, $user, $password)
    {
        $this->host = $host;
        $this->databaseName = $databaseName;
        $this->user = $user;
        $this->password = $password;
        $this->connectON();
    }

    private function connectON()
    {
        try {   
            $this->db = new PDO("sqlsrv:Server=$this->host;Database=$this->databaseName", "$this->user", "$this->password");
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //    $this -> consulta(); 
        } catch (Exception $error) {
            echo "No se ha podido conectar a la bd: " . $error->getMessage();
        }
    }

    public function getPDOObject()
    {
        return $this->db;
    }
}
