# Task Manager

A Laravel + Vue.js task management application with features like:

- Task creation, editing and deletion
- Task filtering and sorting
- Pagination
- Form validation

## Requirements

- PHP 8.1+
- Composer
- Node.js 16+

## Installation

1. Clone the repository
2. Run `composer install`
3. Run `npm install`
4. Copy `.env.example` to `.env` and configure your database
5. Run `php artisan key:generate`
6. Run migrations: `php artisan migrate`

## Docker Setup

1. Build and start containers:

```bash
docker-compose up -d
```

2. Generate application key:

```bash
php artisan key:generate
```

3. Update .env with PostgreSQL credentials:

```makefile
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=task_manager
DB_USERNAME=postgres
DB_PASSWORD=secret
```

4. Run migrations:

```bash
php artisan migrate
```

## Development

- Start Laravel server: `php artisan serve`
- Start Vite dev server: `npm run dev`

## API Documentation

After starting the Laravel server, you can access the API documentation at <http://localhost:8000/api/documentation>.

## Production Build

- Run `npm run build`

## Testing

Run PHPUnit tests:

```bash
php artisan test
```
