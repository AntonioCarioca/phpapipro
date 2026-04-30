<?php
namespace App;

use PDO;

class Database
{
    public static function connect(): PDO
    {
        $config = require __DIR__ . '/../config/database.php';
        $dsn = "{$config['connection']}:host={$config['host']};port={$config['port']};
                dbname={$config['database']};charset={$config['charset']}";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        return new PDO($dsn, $config['username'], $config['password'], $options);
    }
}
