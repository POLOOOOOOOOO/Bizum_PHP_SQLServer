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
            return $result;
        } else {
            return;
        }
    
        // return null;
    }
}
?>