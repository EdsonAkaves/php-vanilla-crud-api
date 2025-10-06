# 🚀 API REST CRUD - PHP Vanilla

API RESTful completa desenvolvida em PHP puro para gerenciamento de clientes, com autenticação JWT, reset de senha e operações CRUD seguras.

## 📋 Sobre o Projeto

Esta API foi desenvolvida como projeto de estudos para demonstrar conhecimentos em:
- Desenvolvimento de APIs REST
- Autenticação e autorização com JWT
- Operações CRUD (Create, Read, Update, Delete)
- Segurança em aplicações web
- Boas práticas de programação em PHP

## ✨ Funcionalidades

### 🔐 Autenticação
- **Login** com geração de token JWT
- **Autenticação Bearer Token** em rotas protegidas
- **Expiração automática** de tokens (1 hora)

### 👥 Gerenciamento de Clientes
- ✅ **Criar** novo cliente (cadastro)
- ✅ **Listar** todos os clientes
- ✅ **Buscar** cliente específico por ID
- ✅ **Atualizar** dados do próprio perfil
- ✅ **Deletar** própria conta (com cascata em pedidos)

### 🔑 Recuperação de Senha
- **Solicitar reset** de senha via email
- **Resetar senha** com token de segurança
- Token com **validade de 15 minutos**

## 🛠️ Tecnologias Utilizadas

- **PHP 8.2+** - Linguagem principal
- **PDO** - Conexão segura com banco de dados
- **MySQL** - Banco de dados relacional
- **JWT (Firebase PHP-JWT)** - Autenticação via tokens
- **Carbon** - Manipulação de datas
- **Composer** - Gerenciador de dependências
- **phpdotenv** - Gerenciamento de variáveis de ambiente

## 📦 Instalação

### 1. Clone o repositório

```bash
git clone https://github.com/EdsonAkaves/php-vanilla-crud-api.git
cd php-vanilla-crud-api
```

### 2. Instale as dependências

```bash
composer install
```

### 3. Configure o banco de dados

Crie um banco de dados MySQL e execute o script SQL:

```sql
CREATE DATABASE api_clientes;

USE api_clientes;

CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    reset_token VARCHAR(255) NULL,
    reset_token_expira_em DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT NOT NULL,
    descricao TEXT,
    valor DECIMAL(10,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_cliente) REFERENCES clientes(id) ON DELETE CASCADE
);
```

### 4. Configure as variáveis de ambiente

Crie um arquivo `.env` na raiz do projeto:

```env
# Banco de Dados
DB_HOST=localhost
DB_NAME=api_clientes
DB_USER=root
DB_PASS=

# JWT
JWT_SECRET_KEY=sua_chave_secreta_super_segura_aqui
```

### 5. Inicie o servidor

```bash
# Usando o servidor embutido do PHP
php -S localhost:8000

# Ou configure no Apache/Nginx
# Coloque o projeto na pasta htdocs/www
```

## 📚 Documentação da API

### Base URL
```
http://localhost:8000/api
```

### Endpoints

#### 🔓 Endpoints Públicos

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| POST | `/criar_cliente.php` | Cadastrar novo cliente |
| POST | `/login.php` | Fazer login e obter token |
| GET | `/clientes.php` | Listar todos os clientes |
| GET | `/cliente.php?id={id}` | Buscar cliente por ID |
| POST | `/solicitar_reset_senha.php` | Solicitar reset de senha |
| POST | `/resetar_senha.php` | Resetar senha com token |

#### 🔒 Endpoints Protegidos (Requerem Token)

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| PUT | `/atualizar_cliente.php?id={id}` | Atualizar próprio perfil |
| DELETE | `/deletar_cliente.php?id={id}` | Deletar própria conta |

---

### 📝 Exemplos de Requisições

#### 1. Criar Cliente

```bash
POST /api/criar_cliente.php
Content-Type: application/json

{
  "nome": "João Silva",
  "email": "joao@exemplo.com",
  "senha": "senha123"
}
```

**Resposta (201):**
```json
{
  "Sucesso": "Cliente criado."
}
```

---

#### 2. Login

```bash
POST /api/login.php
Content-Type: application/json

{
  "email": "joao@exemplo.com",
  "senha": "senha123"
}
```

**Resposta (200):**
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
}
```

---

#### 3. Listar Clientes

```bash
GET /api/clientes.php
```

**Resposta (200):**
```json
[
  {
    "id": 1,
    "nome": "João Silva",
    "email": "joao@exemplo.com"
  },
  {
    "id": 2,
    "nome": "Maria Santos",
    "email": "maria@exemplo.com"
  }
]
```

---

#### 4. Atualizar Cliente (Protegido)

```bash
PUT /api/atualizar_cliente.php?id=1
Content-Type: application/json
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...

{
  "nome": "João Silva Junior",
  "email": "joao.junior@exemplo.com"
}
```

**Resposta (200):**
```json
{
  "Sucesso": "Cliente alterado com sucesso."
}
```

---

#### 5. Solicitar Reset de Senha

```bash
POST /api/solicitar_reset_senha.php
Content-Type: application/json

{
  "email": "joao@exemplo.com"
}
```

**Resposta (200):**
```json
{
  "mensagem": "Se o e-mail existir, um link de reset foi enviado.",
  "token_para_teste": "a1b2c3d4e5..."
}
```

---

#### 6. Resetar Senha

```bash
POST /api/resetar_senha.php
Content-Type: application/json

{
  "token": "a1b2c3d4e5...",
  "senha": "nova_senha_123"
}
```

**Resposta (200):**
```json
{
  "sucesso": "Senha redefinida com sucesso."
}
```

---

#### 7. Deletar Cliente (Protegido)

```bash
DELETE /api/deletar_cliente.php?id=1
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
```

**Resposta (200):**
```json
{
  "Sucesso": "Cliente deletado."
}
```

---

## 🔒 Segurança Implementada

- ✅ **Senhas hasheadas** com `password_hash()` (bcrypt)
- ✅ **JWT** para autenticação stateless
- ✅ **Prepared Statements** (PDO) contra SQL Injection
- ✅ **Validação de dados** em todas as requisições
- ✅ **Autorização por usuário** - usuários só podem modificar seus próprios dados
- ✅ **Tokens de reset** com expiração de 15 minutos
- ✅ **Headers de segurança** (Content-Type, métodos HTTP)
- ✅ **Variáveis de ambiente** para dados sensíveis

## 📂 Estrutura do Projeto

```
php-vanilla-crud-api/
├── api/
│   ├── atualizar_cliente.php
│   ├── auth.php
│   ├── cliente.php
│   ├── clientes.php
│   ├── criar_cliente.php
│   ├── deletar_cliente.php
│   ├── login.php
│   ├── resetar_senha.php
│   └── solicitar_reset_senha.php
├── config/
│   └── database.php
├── vendor/
├── .env
├── .gitignore
├── bootstrap.php
├── composer.json
└── README.md
```

## ⚠️ Códigos de Status HTTP

| Código | Significado |
|--------|-------------|
| 200 | Sucesso |
| 201 | Criado com sucesso |
| 400 | Requisição inválida |
| 401 | Não autenticado |
| 403 | Sem permissão |
| 404 | Não encontrado |
| 405 | Método não permitido |
| 500 | Erro no servidor |

## 🧪 Testando a API

### Com cURL

```bash
# Login
curl -X POST http://localhost:8000/api/login.php \
  -H "Content-Type: application/json" \
  -d '{"email":"joao@exemplo.com","senha":"senha123"}'

# Atualizar (com token)
curl -X PUT http://localhost:8000/api/atualizar_cliente.php?id=1 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer SEU_TOKEN_AQUI" \
  -d '{"nome":"João Atualizado"}'
```

### Com Postman/Insomnia

1. Importe a collection (ou crie manualmente)
2. Configure a variável `{{baseUrl}}` como `http://localhost:8000/api`
3. Após o login, salve o token em uma variável
4. Use o token nos endpoints protegidos

## 🚧 Melhorias Futuras

- [ ] Implementar paginação na listagem de clientes
- [ ] Adicionar filtros e ordenação
- [ ] Sistema de logs de auditoria
- [ ] Rate limiting para prevenir ataques
- [ ] Envio real de emails para reset de senha
- [ ] Documentação com Swagger/OpenAPI
- [ ] Testes automatizados (PHPUnit)
- [ ] Cache de requisições
- [ ] Versionamento da API (v1, v2)

## 📝 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## 👨‍💻 Autor

**Edson Alves**

- LinkedIn: [edsonakaves](https://www.linkedin.com/in/edsonakaves/)
- GitHub: [@EdsonAkaves](https://github.com/EdsonAkaves)
- Email: edson.akaves@gmail.com

---

<div align="center">
  <p>Desenvolvido como projeto de estudos</p>
  <p>⭐ Se este projeto te ajudou, considere dar uma estrela!</p>
</div>
