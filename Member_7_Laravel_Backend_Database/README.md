# Member 7: Laravel Backend & Database Setup
**Owner:** Member 7 (Backend Engineer & Database Administrator)
**Role:** Complete backend foundation, SQLite/MySQL database migrations, footwear product seeders, and REST API/web routing.

---

## 🛠️ Step-by-Step Setup Instructions

```bash
# 1. Install dependencies
composer install

# 2. Configure environment file
cp .env.example .env

# 3. Generate app encryption key
php artisan key:generate

# 4. Run database migrations with realistic shoe seeders
php artisan migrate:fresh --seed

# 5. Start the local development server
php artisan serve
```

The application will be live at: `http://127.0.0.1:8000`

---

## 🚀 How Member 7 Pushes to GitHub

```bash
cd Member_7_Laravel_Backend_Database
git init
git remote add origin https://github.com/vasCowilson1/shoe-haven.git
git checkout -b feature/member-7-backend-database
git add .
git commit -m "feat(backend): configure Laravel environment, sqlite migrations, and shoe seeders"
git push -u origin feature/member-7-backend-database
```

Then open a Pull Request into `main` on GitHub!
