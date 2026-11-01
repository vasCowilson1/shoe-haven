# Shoe Haven database

The Laravel migrations in `database/migrations` are the **authoritative database schema** for this application.

## Setup

```bash
php artisan migrate
php artisan db:seed
```

For a fresh development database:

```bash
php artisan migrate:fresh --seed
```

The SQL files under `database/legacy/` are retained only for reference and should not be used to initialise the current application because they belong to an older schema version.
