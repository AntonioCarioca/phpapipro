<?php

namespace App\Core;

/**
 * Motor de Roteamento Avançado.
 *
 * Esta classe gerencia o ciclo de vida das rotas, comparando a requisição atual
 * com as rotas registradas e despachando para o Controller responsável,
 * injetando automaticamente o objeto Request e parâmetros da URL.
 *
 * @package App\Core
 * @author XxZeroxX
 * @version 2.0.0
 */
class Router
{
    /** @var array Lista de rotas registradas na aplicação. */
    private array $routes = [];

    /**
     * @param array $routes Conjunto de rotas (Geralmente carregadas do web.php).
     */
    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    /**
     * Resolve a rota baseada no objeto Request e executa o handler.
     *
     * @param Request $request Instância da requisição atual contendo método e URI.
     * @return mixed O retorno do método executado no Controller.
     */
    public function dispatch(Request $request)
    {
        $method = $request->method();
        $uri = $request->uri();

        foreach ($this->routes as $route) {
            [$routeMethod, $routeUri, $handler] = $route;

            // Ignora se o método HTTP (GET, POST...) for diferente
            if ($method !== $routeMethod) {
                continue;
            }

            // Tenta validar a URI e capturar parâmetros dinâmicos (ex: {id})
            $params = $this->matchRoute($routeUri, $uri);

            if ($params !== false) {
                return $this->execute($handler, $request, $params);
            }
        }

        /**
         * Fallback: Caso nenhuma rota coincida, utiliza o helper de
         * Response para retornar um erro JSON padronizado.
         */
        Response::json(['error' => 'Not Found'], 404);
    }

    /**
     * Compara a URI da rota com a URI atual usando Expressões Regulares.
     *
     * @param string $routeUri Padrão da rota (ex: /user/{id}).
     * @param string $currentUri URI vinda do navegador.
     * @return array|false Retorna os valores dos parâmetros ou false.
     */
    private function matchRoute(string $routeUri, string $currentUri): array|false
    {
        // Converte marcações {param} em grupos de captura Regex
        $pattern = preg_replace('/\{(\w+)\}/', '([^/]+)', $routeUri);
        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $currentUri, $matches)) {
            // Remove a primeira correspondência (string completa) para sobrar apenas os valores
            array_shift($matches);
            return $matches;
        }

        return false;
    }

    /**
     * Instancia o Controller e invoca o método passando Request e Parâmetros.
     *
     * @param array $handler Array com [ClasseController, NomeMetodo].
     * @param Request $request Objeto de requisição para injeção de dependência.
     * @param array $params Argumentos extraídos da URL.
     * @return mixed
     */
    private function execute(array $handler, Request $request, array $params = [])
    {
        [$controller, $method] = $handler;

        // Cria dinamicamente a instância do Controller
        $instance = new $controller();

        /**
         * Execução Dinâmica:
         * Une o objeto $request com os demais parâmetros capturados.
         * O método no Controller receberá: function index(Request $request, $id, $slug...)
         */
        return call_user_func_array(
            [$instance, $method],
            array_merge([$request], $params)
        );
    }
}
