# Laravel Auth API

API REST de autenticação com JWT construída em **Laravel 11** e **PHP 8.3**, com foco em segurança.

## Stack

- PHP 8.3 / Laravel 11
- SQLite (dev) — qualquer banco suportado pelo Eloquent em produção
- JWT via `tymon/jwt-auth`
- CORS via `fruitcake/php-cors`

## Funcionalidades

### Autenticação
- Registro com validação e envio de e-mail de verificação
- Login com e-mail e senha, retorna JWT
- Logout com invalidação do token (blacklist)
- Refresh de token JWT

### Segurança
- **Token versioning** — cada token carrega `token_version`; ao trocar a senha o campo é incrementado no banco, revogando todos os JWTs anteriores automaticamente, mesmo dentro do TTL
- **Verificação de e-mail obrigatória** para rotas sensíveis (`/me`, `/profile`, `/password`)
- **UUID público** — o campo `id` exposto nas respostas é um UUID, nunca o ID numérico do banco (mitiga IDOR)
- **Rate limiting** por rota (registro/login: 10 req/min; forgot/reset: 5 req/min; rotas autenticadas: 60 req/min)
- **CORS restrito** — origens configuradas via variável de ambiente
- **Security headers** em todas as respostas (`X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`, `Permissions-Policy`, `HSTS`)
- **CSP `default-src 'none'`** nas rotas `/api/*` (respostas JSON não devem carregar recursos externos)
- **Cache-Control `no-store`** nas respostas da API (impede cache de tokens e dados do usuário)
- Senhas com `bcrypt` via `Hash::make()`

### Conta
- Atualização de nome e e-mail (`/profile`)
- Troca de senha com validação da senha atual (`/password`) — revoga todos os tokens ativos
- Reenvio de e-mail de verificação (`/email/resend`)
- Fluxo completo de esqueci/redefinir senha com token assinado

## Instalação

```bash
# 1. Clonar o repositório
git clone https://github.com/bernardocm10/laravel-auth-api.git
cd laravel-auth-api

# 2. Instalar dependências
composer install

# 3. Copiar e configurar variáveis de ambiente
cp .env.example .env
# Editar .env — ver seção "Variáveis de ambiente"

# 4. Gerar chave da aplicação
php artisan key:generate

# 5. Gerar secret JWT
php artisan jwt:secret

# 6. Executar migrations
php artisan migrate

# 7. Iniciar servidor
php artisan serve
```

> Em desenvolvimento, e-mails (verificação e reset de senha) são gravados em `storage/logs/laravel.log` — configure `MAIL_MAILER=log` no `.env`.

## Endpoints

### Públicos

| Método | Rota                        | Throttle   | Descrição                          |
|--------|-----------------------------|------------|------------------------------------|
| POST   | /api/auth/register          | 10 req/min | Registrar usuário                  |
| POST   | /api/auth/login             | 10 req/min | Login — retorna JWT                |
| POST   | /api/auth/forgot-password   | 5 req/min  | Solicitar link de reset de senha   |
| POST   | /api/auth/reset-password    | 5 req/min  | Redefinir senha com token do e-mail|
| GET    | /api/auth/email/verify/{id}/{hash} | —   | Verificar e-mail (link do e-mail)  |

### Autenticados (requerem `Authorization: Bearer <token>`)

| Método | Rota                     | E-mail verificado | Throttle   | Descrição                          |
|--------|--------------------------|-------------------|------------|------------------------------------|
| POST   | /api/auth/logout         | Não               | 60 req/min | Logout — invalida o token          |
| POST   | /api/auth/refresh        | Não               | 10 req/min | Renovar token JWT                  |
| POST   | /api/auth/email/resend   | Não               | 6 req/min  | Reenviar e-mail de verificação     |
| GET    | /api/auth/me             | **Sim**           | 60 req/min | Dados do usuário autenticado       |
| PUT    | /api/auth/profile        | **Sim**           | 60 req/min | Atualizar nome e e-mail            |
| PUT    | /api/auth/password       | **Sim**           | 10 req/min | Trocar senha — revoga todos os JWTs|

## Exemplos com curl

### Registrar
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "João Silva",
    "email": "joao@example.com",
    "password": "Senha@1234",
    "password_confirmation": "Senha@1234"
  }'
```

### Login
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email": "joao@example.com", "password": "Senha@1234"}'
```

### Dados do usuário
```bash
curl http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer SEU_TOKEN"
```

### Refresh
```bash
curl -X POST http://localhost:8000/api/auth/refresh \
  -H "Authorization: Bearer SEU_TOKEN"
```

### Logout
```bash
curl -X POST http://localhost:8000/api/auth/logout \
  -H "Authorization: Bearer SEU_TOKEN"
```

### Esqueci a senha
```bash
curl -X POST http://localhost:8000/api/auth/forgot-password \
  -H "Content-Type: application/json" \
  -d '{"email": "joao@example.com"}'
# Token aparece em storage/logs/laravel.log
```

### Redefinir senha
```bash
curl -X POST http://localhost:8000/api/auth/reset-password \
  -H "Content-Type: application/json" \
  -d '{
    "email": "joao@example.com",
    "token": "TOKEN_DO_LOG",
    "password": "NovaSenha@2",
    "password_confirmation": "NovaSenha@2"
  }'
```

## Interface visual (dev)

Com `APP_ENV=local`, acesse **http://localhost:8000/tester** para testar todos os endpoints via interface gráfica — sem precisar de curl ou Postman. A rota não existe em outros ambientes.

## Variáveis de ambiente relevantes

```env
# Aplicação
APP_ENV=local
APP_KEY=             # gerado via php artisan key:generate

# Banco de dados
DB_CONNECTION=sqlite
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_DATABASE=laravel

# JWT
JWT_SECRET=          # gerado via php artisan jwt:secret
JWT_TTL=60           # expiração do token em minutos (padrão: 60)
JWT_REFRESH_TTL=20160  # janela de refresh em minutos (padrão: 14 dias)
JWT_ALGO=HS256

# CORS — origens permitidas separadas por vírgula
CORS_ALLOWED_ORIGINS=http://localhost:3000

# E-mail (dev: escreve no log)
MAIL_MAILER=log
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="Auth API"
```

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
