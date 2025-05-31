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

        try {
            $placeholders = implode(',', array_fill(0, count($params), '?'));
            $sql = "EXEC $procedureName $placeholders";

            $stmt = $this->pdo->prepare($sql);

            foreach ($params as $index => $value) {
                $stmt->bindValue($index + 1, $value);
            }

            $stmt->execute();
            $result = $stmt->fetchColumn();
            

            return is_array($result) ? $result : [];
        } catch (PDOException $e) {
            return [['error' => $e->getMessage()]];
        }
    }

    public function getPDO()
    {
        return $this->pdo;
    }
}
