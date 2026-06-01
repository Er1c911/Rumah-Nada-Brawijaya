# Render Deployment Guide

This repository is configured for deployment on Render using `render.yaml` and a `Dockerfile` for a PHP environment.

## What was added

- `render.yaml` — Render service definition for a Laravel app using Docker.
- `Dockerfile` — Docker image for PHP, Composer, Node, and Laravel build/runtime.
- `.dockerignore` — files to exclude from Docker context.

## How to deploy

1. Push your changes to GitHub (branch `main`).
2. Go to https://dashboard.render.com and sign in.
3. Click `New` → `Web Service`.
4. Choose `Connect account` and authorize GitHub if needed.
5. Select the repository `Er1c911/studio-musik`.
6. Render should detect `render.yaml` and use it to configure the service.

## Render service settings

If Render asks for a build or start command, use these values:

- Build command:
  ```bash
  composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction
  npm ci
  npm run build
  php -r "file_exists('.env') || copy('.env.example', '.env');"
  php artisan key:generate --force
  ```
- Start command:
  ```bash
  bash -lc "php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=\$PORT"
  ```

## Environment variables

Set these variables in Render:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://YOUR_RENDER_URL.onrender.com`
- `DB_CONNECTION=sqlite`
- `DB_DATABASE=database/database.sqlite`
- `SESSION_DRIVER=database`
- `CACHE_STORE=database`
- `FILESYSTEM_DISK=local`

If you want a managed database instead of SQLite, create a Render PostgreSQL database and update these variables accordingly.

## Notes

- The first deploy will run migrations automatically.
- If you use PostgreSQL/MySQL later, update `.env` values in Render and set the appropriate database credentials.
- Do not commit your local `.env` file; keep using `.env.example`.
