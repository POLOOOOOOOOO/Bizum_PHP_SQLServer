<?php

// require_once '../../daoCommand.php';

class DBManager
{
    private $dbCommand;

    public function __construct($dbCommand)
    {
        $this->dbCommand = $dbCommand;
    }

    public function viewConnections()
    {
        try {
            $result = $this->dbCommand->execute('sp_list_connections');

            // Establecer el encabezado para XML
            header('Content-Type: text/xml');
            // Mostrar la respuesta XML
            echo $result;
            exit();
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function viewHistoricConnections()
    {
        try {
            $result = $this->dbCommand->execute('sp_list_historic_connections');

            // Establecer el encabezado para XML
            header('Content-Type: text/xml');

            // Mostrar la respuesta XML
            echo $result;
            exit();
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }
}
