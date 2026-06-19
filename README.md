# Portfolio SaaS - Dev Setup Guide

> ⚠️ **Work in Progress** - This project is actively being built and is not feature-complete. Modules may be partially implemented, APIs may change, and some sections of this README may describe planned (not yet shipped) functionality. Not intended for production use.

A multi-tenant SaaS API built with Laravel 13 (PHP 8.4), consumed by a Nuxt 4 frontend. Includes Stripe billing, Spatie roles & permissions, S3-compatible file storage, real-time notifications via Laravel Reverb, and OpenAI integration.

## Prerequisites

- [Docker](https://docs.docker.com/get-docker/) with Docker Compose v2
- Git

---

## 1. Clone the repository

```bash
git clone https://github.com/bruzp/portfolio.git portfolio
cd portfolio
```

---

## 2. Fix Docker credential config (Linux/WSL users) (Optional)

If you're on Linux or WSL and get an `exec format error` when building, run:

```bash
echo "{}" > ~/.docker/config.json
```

---

## 3. Set up the API environment file

```bash
cp api/.env.example api/.env
```

Open `api/.env` and update the values to match Docker:

```env
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=portfolio_db
DB_USERNAME=portfolio_user
DB_PASSWORD=portfolio_password

REDIS_HOST=redis
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025

FILESYSTEM_DISK=s3
AWS_ENDPOINT=http://minio:9000
AWS_BUCKET=portfolio
AWS_ACCESS_KEY_ID=portfolio_minio
AWS_SECRET_ACCESS_KEY=portfolio_minio_secret
AWS_USE_PATH_STYLE_ENDPOINT=true

REVERB_HOST=reverb
REVERB_PORT=8080
```

---

## 4. Set up the Frontend environment file

```bash
touch web/.env
```

Make sure this is set:

```env
NUXT_PUBLIC_API_BASE=http://localhost:8000/api
NUXT_PUBLIC_REVERB_HOST=localhost
NUXT_PUBLIC_REVERB_PORT=8080
```

---

## 5. Build the Docker images

```bash
docker compose build --no-cache
```

---

## 6. Install dependencies

```bash
docker compose run --rm api composer install
```

---

## 7. Regenerate the frontend lockfile

> This step ensures `package-lock.json` is compatible with the Linux Alpine container.
> Only needed on first setup or after adding new packages.

```bash
docker compose run --rm web npm install
```

---

## 8. Start all services

```bash
docker compose up -d
```

---

## 9. Generate the Laravel app key

```bash
docker compose exec api php artisan key:generate
```

---

## 10. Run database migrations and seeder

```bash
docker compose exec api php artisan migrate --seed
```

---

## 11. Access the application

Frontend:

```text
http://localhost:3000
```

API:

```text
http://localhost:8000/api
```

MinIO Console (file storage):

```text
http://localhost:9001
```

Mailpit (catch local emails):

```text
http://localhost:8025
```

---

## 12. Sample users

The seeder creates the following users:

| Role  | Email               | Password |
| ----- | -------------------- | -------- |
| Admin | `admin@example.com`  | password |
| Member | `user@example.com`  | password |

---

## Tech Stack

**Backend:** Laravel 13, PHP 8.4, PostgreSQL 17, Redis, Laravel Reverb, Spatie Permission, Laravel Cashier (Stripe)
**Frontend:** Nuxt 4, Vue 3, Nuxt UI, Pinia
**Infra:** Docker, MinIO (S3-compatible storage), Mailpit (local email testing)