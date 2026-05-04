<?php

namespace App;

/**
 * Abstração de Requisição HTTP.
 * 
 * Esta classe centraliza o acesso aos dados enviados pelo cliente (URI, Método, 
 * Parâmetros e Corpo da requisição), facilitando o manuseio de diferentes
 * tipos de entrada (POST, GET, JSON).
 * 
 * @package App
 * @author XxZeroxX
 * @version 1.0.0
 */
class Request
{
    /**
     * Retorna o método HTTP da requisição (GET, POST, PUT, DELETE, etc.).
     * 
     * @return string O verbo HTTP em maiúsculas.
     */
    public function method(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Retorna a URI da requisição, limpa de parâmetros de query string.
     * 
     * Exemplo: De '/usuarios?id=1' retorna apenas '/usuarios'.
     * 
     * @return string O caminho da URL processado.
     */
    public function uri(): string
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    /**
     * Captura os dados enviados no corpo da requisição.
     * 
     * O método tenta decodificar um corpo JSON (comum em APIs) e, caso não exista,
     * retorna os dados de um formulário POST convencional.
     * 
     * @return array Conjunto de dados recebidos no corpo da requisição.
     */
    public function input(): array
    {
        // Lê o fluxo de entrada bruto (raw input stream)
        $content = file_get_contents('php://input');
        
        // Tenta transformar o JSON em um array associativo
        $json = json_decode($content, true);

        /**
         * Operador de coalescência: se $json for nulo (não é uma requisição JSON),
         * retorna o conteúdo da superglobal $_POST.
         */
        return $json ?? $_POST;
    }

    /**
     * Retorna os parâmetros enviados via Query String (URL).
     * 
     * @return array Dados contidos na superglobal $_GET.
     */
    public function query(): array
    {
        return $_GET;
    }
}
