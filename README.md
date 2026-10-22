# Student Assignment Tracker

A web app for tracking assignments across units, built with Vue 3, Vite, Bootstrap, PHP and MySQL.
(COS30043 Interface Design and Development project)

## Tech stack
- Frontend: Vue 3, Vue Router, Vite, Bootstrap 5
- Backend: PHP REST API (PDO)
- Database: MySQL

## Folder structure
```
frontend/   Vue app
backend/
  api/      PHP API (index.php, config.example.php)
  schema.sql  database tables and sample units
```

## Setup for new team members

### 1. Install the tools
- Git, Node.js (LTS), PHP 8+ with the `pdo_mysql` extension, and MySQL
- Mac (Homebrew): `brew install node php mysql` then `brew services start mysql`
- Optional: TablePlus or phpMyAdmin to view the database

### 2. Clone the repo
```bash
git clone https://github.com/YOUR-USERNAME/student-assignment-tracker.git
cd student-assignment-tracker
```

### 3. Create the database
```bash
mysql -u root -e "CREATE DATABASE IF NOT EXISTS assignment_tracker CHARACTER SET utf8mb4;"
mysql -u root assignment_tracker < backend/schema.sql
```
Add `-p` after `root` if your MySQL root user has a password.

### 4. Configure the backend
```bash
cp backend/api/config.example.php backend/api/config.php
```
Open `backend/api/config.php` and set your own database name, username and password.
This file is ignored by Git, so never commit it.

### 5. Configure the frontend
```bash
cd frontend
cp .env.example .env
```
Make sure `.env` contains:
```
VITE_API_URL=http://localhost:8000/index.php
```
Restart `npm run dev` whenever you change `.env`.

### 6. Run the app (two terminals)
Terminal 1, from the project root:
```bash
php -S localhost:8000 -t backend/api
```
Terminal 2:
```bash
cd frontend
npm install
npm run dev
```
Open http://localhost:5173

Test the API at http://localhost:8000/index.php?route=units (it should list the units).

### 7. Make yourself an admin (optional)
Register an account, then run:
```sql
UPDATE users SET role = 'admin' WHERE email = 'your@email.com';
```
Log out and in again.

## Troubleshooting
| Problem | Fix |
|---|---|
| Register or login shows 404 | `frontend/.env` is missing or wrong. Restart `npm run dev`. |
| API shows "Access denied" | Wrong details in `backend/api/config.php`. |
| API shows "Server error" | Check that MySQL is running and the tables exist. |
| `could not find driver` | Enable `pdo_mysql` for PHP (`php -m | grep pdo_mysql`). |
| No database selected | Run `schema.sql` against `assignment_tracker`, as in step 3. |

## Git workflow
1. `git pull` before you start work.
2. Create a branch: `git checkout -b feature/your-feature`
3. Commit small and often, with clear messages.
4. Push the branch and open a pull request. A teammate reviews it, then merge into `main`.
5. Never commit `.env`, `config.php` or `node_modules`.

## Deployment (Mercury)
1. Set `VITE_API_URL` in `frontend/.env` to the Mercury API address.
2. `cd frontend && npm run build`, then upload the contents of `dist`.
3. Upload `backend/api`, import `schema.sql`, and create `config.php` with the Mercury database details.
