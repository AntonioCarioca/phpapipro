## [1.0.0] - 2026-05-04



### Added


- Add arquivo de conexão ao banco de dados

- Add arquivo para Reponse da api

- Add arquivo Model de user para persistir dados

- Add arquivo UserController

- Add Entrypoint da api

- Add comentação da API usando Swagger ui

- Add classe para gerenciamento de variáveis de ambiente a partir de arquivo .env

- Add AuthMiddleware para autentica a presença de um token Bearer

- Add funcionalidade de Request

- Add funcionalidade de Roteamento

- Add arquivo de rotas



### Changed


- Adpatando a classe Database para usar variáveis de ambiente

- Update classe User para permitir paginação e correção dos métodos para uso de PDO

- Update UserController para use de validações de lógicas de segurança

- Adpatação da classe UserController para receber request

- Atulização do frontcontroller



### Chores


- Add script para gerar releases automaticos

- Add script para gerar changelos automaticos



### Documentation


- Add composer.json

- Add composer.lock no .gitignore

- Add create users schema

- Add database.php (con)

- Add .env ao .gitignore

- Add .env.example

- Add comentários a Classe Response

- Add autenticação bearer no arquivo de config do swegger ui



### Fixed


- Database.php

- Correçao no namespace de Middleware para Middlewares

- Mudar arquivos Database.php, Request.php e Response.php da raiz /app para /app/core

- Mudanção no use para classes de Database, Resquest e Response

- Remove de importação de namespace de Request e Response da classe Router agora fazem parte do mesmo



