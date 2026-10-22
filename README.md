# Student Assignment Tracker

## Run locally
1. Create a MySQL database and run `backend/schema.sql`.
2. Edit `backend/api/config.php` with your database details.
3. Serve `backend/api` with PHP (e.g. `php -S localhost:8000 -t backend/api`).
4. In `frontend`: copy `.env.example` to `.env`, set `VITE_API_URL=http://localhost:8000/index.php`, then `npm install` and `npm run dev`.

## Deploy to Mercury
`npm run build`, upload `frontend/dist` and `backend/api` to your Mercury folder, import `schema.sql`, and set `VITE_API_URL` before building.
To make an admin, run: `UPDATE users SET role='admin' WHERE email='you@example.com';`
