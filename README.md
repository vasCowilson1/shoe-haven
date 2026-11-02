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
- SQLite or MySQL

## Local setup

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
php artisan storage:link
php artisan serve
```

Open `http://127.0.0.1:8000`.

The database migrations are the source of truth. Use `php artisan migrate:fresh --seed` to reset a local development database.

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
