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
 * @author XxZeroxX
 * @version 1.0.1
 */
class User
{
    /**
     * Recupera uma lista paginada de usuários.
     *
     * @param int $page O número da página atual (inicia em 1).
     * @param int $perPage Quantidade de registros por página.
     * @return array Lista de usuários como arrays associativos.
     */
    public static function all(int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;
        $pdo = Database::connect();

        $stmt = $pdo->prepare('SELECT id, name, email FROM users ORDER BY id DESC LIMIT :limit OFFSET :offset');

        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca um usuário específico pelo ID.
     *
     * @param int $id Identificador primário do usuário.
     * @return array|null Dados do usuário (sem senha) ou null.
     */
    public static function find(int $id): ?array
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('SELECT id, name, email FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    /**
     * Localiza um usuário pelo endereço de e-mail.
     *
     * Útil para validação de login e checagem de e-mails duplicados.
     *
     * @param string $email E-mail do usuário.
     * @return array|null Retorna os dados do usuário ou null.
     */
    public static function findByEmail(string $email): ?array
    {
        $pdo = Database::connect();
        // Nota: Para o login, talvez você precise selecionar a 'password' aqui também.
        $stmt = $pdo->prepare('SELECT id, name, email, password, role FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    /**
     * Insere um novo usuário e criptografa a senha automaticamente.
     *
     * @param array $data Mapa contendo 'name', 'email' e 'password'.
     * @return bool Sucesso ou falha na inserção.
     */
    public static function create(array $data): bool
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (:name, :email, :password)');

        return $stmt->execute([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT)
        ]);
    }

    /**
     * Atualiza os dados de nome e e-mail de um usuário.
     *
     * @param int $id ID do usuário.
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
     * Remove um usuário permanentemente.
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
