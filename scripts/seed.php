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

$dsn = "{$connection}:host={$host};port={$port};dbname={$db};charset={$charset}";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // 2. Conexão com o Banco de Dados
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "🌱 Conectado ao banco de dados com sucesso!\n";

    // 3. Caminho da pasta de seeders
    $seedersDir = __DIR__ . '/../database/seeders';

    if (!is_dir($seedersDir)) {
        die("❌ Erro: O diretório '$seedersDir' não existe.\n");
    }

    // 4. Escaneia a pasta e filtra apenas arquivos .sql
    $files = scandir($seedersDir);
    $sqlFiles = array_filter($files, function ($file) {
        return pathinfo($file, PATHINFO_EXTENSION) === 'sql';
    });

    if (empty($sqlFiles)) {
        die("⚠️ Nenhum arquivo .sql encontrado em $seedersDir\n");
    }

    // 5. Ordena os arquivos (ex: 01_usuarios.sql roda antes de 02_perfis.sql)
    sort($sqlFiles);

    echo "--- Iniciando o Seeding ---\n";

    // 6. Executa cada arquivo SQL
    foreach ($sqlFiles as $file) {
        $filePath = $seedersDir . '/' . $file;
        echo "⏳ Executando seeder: $file ... ";

        // Lê o conteúdo do arquivo .sql
        $sql = file_get_contents($filePath);

        if (trim($sql) === '') {
            echo "pulado (arquivo vazio).\n";
            continue;
        }

        // Executa o SQL no banco de dados
        $pdo->exec($sql);
        echo "✅ Concluído!\n";
    }

    echo "---\n🚀 Todos os seeders foram executados com sucesso!\n";
} catch (\PDOException $e) {
    echo "\n❌ ERRO durante o processo: " . $e->getMessage() . "\n";
    exit(1);
}
