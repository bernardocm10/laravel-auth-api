# Laravel Auth API

API REST de autenticação com JWT construída em **Laravel 11** e **PHP 8.3**.

## Stack

- PHP 8.3
- Laravel 11
- MySQL
- JWT via `tymon/jwt-auth`

## Funcionalidades

- Registro de usuário com validação
- Login com e-mail e senha
- Logout com invalidação do token
- Refresh de token JWT
- Rota protegida (`/api/auth/me`)
- Rate limiting nas rotas públicas (10 req/min)
- Senhas com `bcrypt` via `Hash::make()`
- Proteção contra mass assignment com `$fillable`

## Instalação

```bash
# 1. Clonar o repositório
git clone https://github.com/seu-usuario/laravel-auth-api.git
cd laravel-auth-api

# 2. Instalar dependências
composer install

# 3. Copiar e configurar variáveis de ambiente
cp .env.example .env
# Editar .env com suas credenciais de banco

# 4. Gerar chave da aplicação
php artisan key:generate

# 5. Gerar secret JWT
php artisan jwt:secret

# 6. Executar migrations
php artisan migrate

# 7. Iniciar servidor
php artisan serve
```

## Endpoints

| Método | Rota               | Auth | Descrição               |
|--------|--------------------|------|-------------------------|
| POST   | /api/auth/register | Não  | Registrar usuário       |
| POST   | /api/auth/login    | Não  | Login                   |
| GET    | /api/auth/me       | Sim  | Dados do usuário logado |
| POST   | /api/auth/refresh  | Sim  | Renovar token           |
| POST   | /api/auth/logout   | Sim  | Logout                  |

## Exemplos com curl

### Registrar
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "João Silva",
    "email": "joao@example.com",
    "password": "senha123",
    "password_confirmation": "senha123"
  }'
```

### Login
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email": "joao@example.com", "password": "senha123"}'
```

### Dados do usuário (requer token)
```bash
curl -X GET http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer SEU_TOKEN_AQUI"
```

### Refresh
```bash
curl -X POST http://localhost:8000/api/auth/refresh \
  -H "Authorization: Bearer SEU_TOKEN_AQUI"
```

### Logout
```bash
curl -X POST http://localhost:8000/api/auth/logout \
  -H "Authorization: Bearer SEU_TOKEN_AQUI"
```

## Variáveis de ambiente relevantes

```env
JWT_SECRET=       # gerado via php artisan jwt:secret
JWT_TTL=60        # expiração do token em minutos
JWT_REFRESH_TTL=20160  # expiração do refresh em minutos (14 dias)
JWT_ALGO=HS256    # algoritmo de assinatura
```


## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
