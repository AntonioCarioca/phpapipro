<?php

namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * Modelo de Usuário para operações de API e Listagem.
 * 
 * Esta classe fornece métodos estáticos para manipulação da tabela 'users',
 * incluindo lógica de paginação, busca individual, criação, atualização e remoção.
 * 
 * @package App\Models
 * @author Seu Nome
 * @version 1.0.0
 */
class User
{
    /**
     * Recupera uma lista paginada de usuários.
     * 
     * Ordena os resultados pelo ID de forma decrescente para exibir os 
     * registros mais recentes primeiro.
     * 
     * @param int $page O número da página atual (inicia em 1).
     * @param int $perPage Quantidade de registros por página.
     * @return array Lista de usuários como arrays associativos.
     */
    public static function all(int $page = 1, int $perPage = 10): array
    {
        /**
         * Cálculo do Deslocamento (Offset):
         * Se page=1 e perPage=10, offset = (1-1)*10 = 0.
         * Se page=2 e perPage=10, offset = (2-1)*10 = 10.
         */
        $offset = ($page - 1) * $perPage;
        
        $pdo = Database::connect();
        
        // Usamos Prepared Statements para evitar ataques de injeção
        $stmt = $pdo->prepare('SELECT * FROM users ORDER BY id DESC LIMIT :limit OFFSET :offset');
        
        /**
         * Importante: LIMIT e OFFSET precisam ser inteiros.
         * O PDO::PARAM_INT garante que o banco trate o valor corretamente.
         */
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca um usuário específico pelo ID.
     * 
     * @param int $id Identificador primário do usuário.
     * @return array|null Dados do usuário ou null caso não seja encontrado.
     */
    public static function find(int $id): ?array
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    /**
     * Insere um novo usuário no banco de dados.
     * 
     * @param array $data Mapa contendo as chaves 'name' e 'email'.
     * @return bool Sucesso ou falha na inserção.
     */
    public static function create(array $data): bool
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('INSERT INTO users (name, email) VALUES (:name, :email)');
        
        return $stmt->execute([
            'name'  => $data['name'], 
            'email' => $data['email']
        ]);
    }

    /**
     * Atualiza os dados de um usuário existente.
     * 
     * @param int $id ID do usuário a ser editado.
     * @param array $data Novos dados ('name' e 'email').
     * @return bool Sucesso ou falha na atualização.
     */
    public static function update(int $id, array $data): bool
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('UPDATE users SET name = :name, email = :email WHERE id = :id');
        
        return $stmt->execute([
            'id'    => $id, 
            'name'  => $data['name'], 
            'email' => $data['email']
        ]);
    }

    /**
     * Remove um usuário permanentemente do sistema.
     * 
     * @param int $id ID do registro.
     * @return bool Sucesso ou falha na exclusão.
     */
    public static function delete(int $id): bool
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('DELETE FROM users WHERE id = :id');
        
        return $stmt->execute(['id' => $id]);
    }
}
