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

## Laravel Cloud deployment

Laravel Cloud provides a native PHP runtime for this application; Docker is not required. Create a Laravel Cloud application connected to this GitHub repository, select PHP 8.2 or newer, and choose a region close to the Neon database.

Configure the environment build command as:

```bash
composer install --no-dev && npm ci --audit false && npm run build && php artisan optimize
```

Configure the deploy command as:

```bash
php artisan migrate --force
```

Set production environment variables in Laravel Cloud, including a unique `APP_KEY`, `APP_ENV=production`, `APP_DEBUG=false`, the public `APP_URL`, `DB_CONNECTION=pgsql`, `DB_SSLMODE=require`, and the secret Neon `DATABASE_URL`. Set `SESSION_DRIVER=database`, `CACHE_STORE=database`, `QUEUE_CONNECTION=sync`, and `LOG_CHANNEL=stderr`. Never commit `.env` or paste secret values into source control.

The Neon schema and sample catalog are already provisioned. The deploy command applies any pending migrations; do not run `migrate:fresh` against the hosted database. Laravel Cloud filesystems are ephemeral, so attach object storage before relying on admin product or hero image uploads. Configure Google and Pesapal production credentials and callback URLs separately before enabling those integrations.

## Tests

```bash
php artisan test
```

The test suite uses a fake Pesapal service; it does not make live payment requests.

## Production checklist

- Set `APP_ENV=production` and `APP_DEBUG=false`.
- Configure a production database, mail provider, and HTTPS.
- Configure and verify production Pesapal callback/IPN URLs and credentials.
- Configure persistent object storage for admin uploads.
- Run `npm run build`.
- Change any seeded administrator password before deployment.
