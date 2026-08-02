# Sistem Persetujuan Dokumen Pemerintah — Backend

Backend API untuk sistem pengajuan dan persetujuan dokumen kelayakan, dibangun dengan Laravel 11, PostgreSQL, dan Laravel Sanctum.

## Tech Stack
- PHP 8.2+
- Laravel 11
- PostgreSQL 16 (via Docker)
- Laravel Sanctum (authentication)
- Spatie Laravel Permission (role & permission)
- Maatwebsite Excel (export)
- Scribe (API documentation)

## Setup

1. Clone the repository
```bash
git clone https://gitlab.com/blaque-group/gov-doc-permit-system.git
cd gov-doc-permit-system
```

2. Install dependencies
```bash
composer install
```

3. Copy environment file
```bash
cp .env.example .env
php artisan key:generate
```

4. Set up PostgreSQL via Docker
```bash
docker run --name gov-doc-postgres -e POSTGRES_PASSWORD=yourpassword -e POSTGRES_DB=gov_doc_db -p 5432:5432 -d postgres:16
```

5. Configure `.env` database connection
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=gov_doc_db
DB_USERNAME=postgres
DB_PASSWORD=password

6. Run migrations and seeders
```bash
php artisan migrate --seed
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=PengujiSeeder
```

7. (Optional) Seed a large dataset for performance testing (~10,000 document requests, ~2000 users)
```bash
php artisan db:seed --class=LargeDatasetSeeder
```

8. Create the storage symlink (for file uploads)
```bash
php artisan storage:link
```

9. Run the server
```bash
php artisan serve
```
API available at `http://127.0.0.1:8000/api`

## Test Accounts

| Role | Email | Password |
|---|---|---|
| Penguji | penguji@test.com | password |
| Pemohon | *(register via `/api/register`)* | — |

## API Documentation

Interactive API docs (Scribe) available at `http://127.0.0.1:8000/docs` after running the server.
