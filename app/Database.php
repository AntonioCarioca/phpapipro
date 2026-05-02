<?php

namespace App;

use PDO;

/**
 * Gerencia a conectividade com o banco de dados da aplicação.
 *
 * Esta classe utiliza o padrão Factory para criar instâncias de conexão PDO
 * baseadas nas configurações definidas nas variáveis de ambiente.
 *
 * @package App
 * @author XxZeroxX
 * @version 2.0.0
 */
class Database
{
    /**
     * Estabelece uma conexão com o banco de dados via PDO.
     *
     * O método extrai as credenciais da superglobal $_ENV e configura o 
     * comportamento do PDO para lançar exceções em caso de erros e 
     * retornar resultados como arrays associativos por padrão.
     *
     * @return PDO Uma instância configurada do objeto PDO.
     * @throws PDOException Caso a tentativa de conexão falhe.
     */
    public static function connect(): PDO
    {   
        // Recupera as configurações das variáveis de ambiente com fallbacks seguros
        $connection = $_ENV['DB_CONNECTION'] ?? 'mysql';
        $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
        $port = $_ENV['DB_PORT'] ?? '3306';
        $db = $_ENV['DB_DATABASE'] ?? 'secureauth';
        $user = $_ENV['DB_USERNAME'] ?? 'root';
        $pass = $_ENV['DB_PASSWORD'] ?? '';
        $charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';

        // Monta a string DSN (Data Source Name)
        $dsn = "{$connection}:host={$host};port={$port};dbname={$db};charset={$charset}";

        /**
         * Configurações padrão do PDO:
         * 1. ATTR_ERRMODE: Lança exceções para que erros não passem despercebidos.
         * 2. ATTR_DEFAULT_FETCH_MODE: Define o retorno padrão como array associativo.
         */
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        // Retorna conexão PDO
        return new PDO($dsn, $user, $pass, $options);
    } 
}
