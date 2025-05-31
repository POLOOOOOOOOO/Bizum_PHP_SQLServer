<?php


class DBCommand
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function execute($procedureName, $params = array())
    {
        $placeholders = implode(',', array_fill(0, count($params), '?'));
        $sql = "EXEC $procedureName $placeholders";

        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $index => $value) {
            $stmt->bindValue($index + 1, $value);
        }

        $stmt->execute();

        if ($stmt->columnCount() > 0) {
            $result = $stmt->fetchColumn();
            return $result !== false ? $result : null;
        }

        return null;
    }



    private function isXML($string)
    {
        // Verificar si el resultado es un arreglo
        if (is_array($string)) {
            // Convertir el arreglo a una cadena
            $string = implode("", $string);
        }

        // Check if the string contains XML tags
        return preg_match('/<\?xml/', $string) === 1;
    }


    private function parseXML($xmlString)
    {
        // Parse the XML string
        $xml = simplexml_load_string($xmlString);
        return $xml;
    }
}
