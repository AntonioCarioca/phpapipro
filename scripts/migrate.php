<?php

use App\Core\Env;

require __DIR__ . '/../vendor/autoload.php';

Env::load(__DIR__ . '/../.env');

// Configurações do Banco de Dados
$connection = $_ENV['DB_CONNECTION'] ?? 'mysql';
$host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$port = $_ENV['DB_PORT'] ?? '3306';
$db = $_ENV['DB_DATABASE'] ?? 'secureauth';
$user = $_ENV['DB_USERNAME'] ?? 'root';
$pass = $_ENV['DB_PASSWORD'] ?? '';
$charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';

$dirMigrations = __DIR__ . '/../database/migrations';

try {
    // 1. Conexão com o Banco de Dados
    $dsn = "{$connection}:host={$host};port={$port};dbname={$db};charset={$charset}";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "• Conectado ao banco de dados com sucesso!\n";

    // 2. Cria a tabela de controle de migrations se ela não existir
    $pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        migration VARCHAR(255) NOT NULL,
        executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=INNODB;");

    // 3. Busca as migrations que já foram executadas
    $stmt = $pdo->query("SELECT migration FROM migrations");
    $executedMigrations = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // 4. Escaneia a pasta de migrations procurando arquivos .sql
    if (!is_dir($dirMigrations)) {
        throw new Exception("A pasta de migrations não foi encontrada em: $dirMigrations");
    }

    // Escaneia e ordena os arquivos (garante a ordem cronológica/alfabética)
    $files = scandir($dirMigrations);
    $migrationsInFolder = array_filter($files, function ($file) {
        return pathinfo($file, PATHINFO_EXTENSION) === 'sql';
    });
    sort($migrationsInFolder);

    echo "• Verificando migrações...\n\n";
    $count = 0;

    // 5. Executa as novas migrations
    foreach ($migrationsInFolder as $migration) {
        // Se a migration já foi executada, pula para a próxima
        if (in_array($migration, $executedMigrations)) {
            continue;
        }

        echo "=> Executando: $migration...\n";

        // Lê o conteúdo do arquivo SQL
        $sql = file_get_contents($dirMigrations . '/' . $migration);

        if (trim($sql) === '') {
            echo "   [Aviso] Arquivo vazio, pulando.\n";
            continue;
        }

        try {
            $pdo->exec($sql);

            // Registra que a migration foi executada
            $stmtInsert = $pdo->prepare("INSERT INTO migrations (migration) VALUES (?)");
            $stmtInsert->execute([$migration]);

            echo "   [Sucesso] $migration aplicada.\n";
            $count++;
        } catch (Exception $e) {
            echo "   [Erro] Falha ao aplicar a migração: " . $migration . "\n";
            echo "   Motivo: " . $e->getMessage() . "\n";
            echo "   Processo interrompido.\n";
            exit(1);
        }
    }

    if ($count === 0) {
        echo "• Tudo limpo! Nada para migrar.\n";
    } else {
        echo "\n• Migrações finalizadas com sucesso! Total aplicadas: $count\n";
    }
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
