<?php
$connection = new DBConnection('172.17.0.3,1433', 'BlockchainDB', 'sa', 'MiP@ssw0rd2024');
$pdoObject = $connection->getPDOObject();
