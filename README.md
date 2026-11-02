# Shoe Haven

Shoe Haven is a Laravel 12 footwear e-commerce application with a customer storefront and an admin panel.

## Features

- Product catalog, categories, brands, search, product details, and reviews
- Cart, checkout, order history, wishlists, and recently viewed products
- Customer profiles and delivery locations
- Admin dashboard for products, categories, inventory, orders, users, messages, and newsletter subscribers
- Google authentication
- Pesapal checkout, payment callbacks, IPN notifications, and payment retries

## Requirements

- PHP 8.2+
- Composer
- Node.js 20+
- PostgreSQL 13+

## Local setup

```bash
composer install
copy .env.example .env
php artisan key:generate
```

Create a PostgreSQL database named `shoe_haven` (or update `DB_DATABASE` and the connection settings in `.env`). For Neon, run `neon init` from the repository root; Laravel reads the `DATABASE_URL` populated by the Neon CLI and derives the endpoint ID needed by older PostgreSQL client libraries. For another PostgreSQL host, set `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` instead. Then run:

```bash
php artisan migrate --seed
npm install
npm run build
php artisan storage:link
php artisan serve
```

Open `http://127.0.0.1:8000`.

The database migrations are the source of truth. Use `php artisan migrate:fresh --seed` to reset a local development database. The automated test suite uses an in-memory SQLite database and does not require PostgreSQL.

## Pesapal configuration

Set the Pesapal environment and credentials in `.env`:

```dotenv
PESAPAL_ENV=sandbox
PESAPAL_CONSUMER_KEY=
PESAPAL_CONSUMER_SECRET=
PESAPAL_CURRENCY=UGX
PESAPAL_IPN_ID=
PESAPAL_CALLBACK_URL=
PESAPAL_IPN_URL=
PESAPAL_SSL_VERIFY=true
```

The checkout redirects customers to Pesapal. Configure public callback and IPN URLs for the deployed application and register the IPN with `php artisan pesapal:register-ipn`. Never commit real payment credentials. Use the Pesapal sandbox before enabling production payments.

## Frontend

Bootstrap 5 and Shoe Haven styles are built with Vite. Source CSS and JavaScript are in `resources/css` and `resources/js`; product and storefront images are in `public/images`.

## Vercel deployment

The root `Dockerfile`, `Caddyfile`, and `vercel.json` deploy Laravel with FrankenPHP on Vercel's container runtime. Link the project with `vercel link` and connect the GitHub repository with `vercel git connect https://github.com/vasCowilson1/shoe-haven`.

Configure these Vercel environment variables before deploying:

- `APP_KEY` (generate a unique key with `php artisan key:generate --show`)
- `APP_ENV=production`, `APP_DEBUG=false`, and `APP_URL` (the deployed HTTPS origin)
- `DATABASE_URL` (the Neon connection string; keep it secret)
- `DB_CONNECTION=pgsql`, `SESSION_DRIVER=database`, `CACHE_STORE=database`, `QUEUE_CONNECTION=sync`, and `LOG_CHANNEL=stderr`

Run database migrations as a release step with `php artisan migrate --force`; do not run migrations when each container starts. Vercel's container filesystem is not durable, so configure an S3-compatible object store before relying on admin product or hero image uploads. Set up separate database credentials for preview deployments rather than pointing previews at production data.

## Tests

```bash
php artisan test
```

The test suite uses a fake Pesapal service; it does not make live payment requests.

## Production checklist

- Set `APP_ENV=production` and `APP_DEBUG=false`.
- Configure a production database, mail provider, and HTTPS.
- Configure and verify production Pesapal callback/IPN URLs and credentials.
- Run `npm run build` and `php artisan storage:link`.
- Change any seeded administrator password before deployment.
