<?php

namespace App\Core;

/**
 * Manipulador de Respostas HTTP.
 * 
 * Esta classe facilita o envio de dados para o cliente, gerenciando
 * os códigos de status HTTP e a formatação adequada do conteúdo.
 * 
 * @package App
 * @author XxZeroxX
 * @version 1.0.0
 */
class Response
{
    /**
     * Envia uma resposta no formato JSON.
     * 
     * Define o código de status da resposta, configura o cabeçalho de conteúdo
     * para JSON com codificação UTF-8 e renderiza os dados fornecidos.
     * 
     * @param array $data O conjunto de dados que será convertido em JSON.
     * @param int $status O código de status HTTP (padrão 200 OK).
     * @return void
     */
    public static function json(array $data, int $status = 200): void
    {
        // Define o código de status da resposta (Ex: 200, 404, 500)
        http_response_code($status);

        // Informa ao navegador/cliente que o conteúdo retornado é um JSON
        header('Content-Type: application/json; charset=utf-8');

        /**
         * Converte o array para uma string JSON.
         * JSON_PRETTY_PRINT: Torna o JSON legível para humanos (útil em debug).
         * JSON_UNESCAPED_UNICODE: Mantém caracteres acentuados sem codificá-los (Ex: "á" em vez de "\u00e1").
         */
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
