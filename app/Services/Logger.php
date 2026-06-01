<?php

namespace App\Services;

/**
 * Serviço de Log de Aplicação.
 *
 * Registra eventos importantes da API (INFO, WARNING, ERROR) em formato JSON,
 * capturando contexto da requisição para facilitar o debug.
 *
 * @package App\Services
 * @author XxZeroxX
 * @version 1.0.0
 */
class Logger
{
    // Define o caminho do arquivo de log.
    private const LOG_PATH = __DIR__ . '/../../storage/logs/app.log';

    public static function info(string $message, array $context = []): void
    {
        self::write('INFO', $message, $context);
    }

    public static function warning(string $message, array $context = []): void
    {
        self::write('WARNING', $message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        self::write('ERROR', $message, $context);
    }

    /**
     * Formata e escreve a mensagem de log no arquivo.
     *
     * @param string $level O nível de severidade (INFO, WARNING, ERROR).
     * @param string $message Descrição do evento.
     * @param array $context Dados extras (ex: ID do usuário, erros de banco).
     */
    private static function write(string $level, string $message, array $context = []): void
    {
        // Monta a estrutura do log com metadados da requisição atual
        $log = [
            'timestamp' => gmdate('Y-m-d H:i:s'),
            'level'     => $level,
            'message'   => $message,
            'ip'        => $_SERVER['REMOTE_ADDR'] ?? 'CLI', // 'CLI' se rodar via terminal
            'method'    => $_SERVER['REQUEST_METHOD'] ?? null,
            'endpoint'  => $_SERVER['REQUEST_URI'] ?? null,
            'context'   => $context
        ];

        // Garante que o diretório exista antes de escrever
        $dir = dirname(self::LOG_PATH);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        // Escreve o JSON em uma única linha no arquivo (JSON Lines format)
        file_put_contents(
            self::LOG_PATH,
            json_encode($log, JSON_UNESCAPED_UNICODE) . PHP_EOL,
            FILE_APPEND
        );
    }
}
