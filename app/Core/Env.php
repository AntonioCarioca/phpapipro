<?php

namespace App\Core;

/**
 * Gerencia o carregamento de variáveis de ambiente a partir de arquivos externos.
 * Esta classe fornece métodos estáticos para ler arquivos de configuração (geralmente .env)
 * e injetá-los na superglobal $_ENV do PHP.
 * @package antonio/secureauth-php
 * @author XxZeroxX
 * @version 1.0.0
 */
class Env
{	
	/**
     * Carrega e processa um arquivo .env para a superglobal $_ENV.
     * O método ignora linhas de comentário (iniciadas com #), linhas vazias 
     * e garante que as aspas ao redor dos valores sejam removidas.
     * @param string $file O caminho completo para o arquivo .env.
     * @return void
     * @throws \RuntimeException Opcional: caso queira lançar erro se o arquivo não existir.
     */
	public static function load(string $file): void
	{
		if (!file_exists($file)) {
			return;
		}

		// Lê o arquivo ignorando quebras de linha e linhas vazias
		$lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

		foreach ($lines as $line) {
			$line = trim($line);
			// Pula comentários ou linhas que não seguem o padrão chave=valor
			if (str_starts_with($line, '#') || !str_contains($line, '=')) {
				continue;
			}
			// Divide a linha apenas no primeiro sinal de '='
			[$key, $value] = explode('=', $line, 2);
			// Define a variável de ambiente limpando espaços e aspas duplas
			$_ENV[trim($key)] = trim($value, " \t\n\r\0\x0B\"");
		}
	}
}
