# travel-expense

A responsive trip expense calculator built with PHP and MySQL. Add costs, split the total equally between team members, and keep a saved history that loads instantly and works offline.

## Features

- **Expense calculator:** category, description, quantity and unit cost per line, with live totals.
- **Equal split:** total, per person, per day and per person per day. Add team member names to see each person's exact share (paisa-accurate, shares always add up to the total).
- **Fuel helper:** work out fuel cost from distance, mileage (km/l), fuel price and one-way or round trip.
- **Trip history in MySQL:** save, reopen, edit and delete trips.
- **History caching:** history is cached in the browser and shown instantly, then checked against a server version hash so unchanged data is never re-downloaded.
- **Offline saving:** if you save while offline, the trip is kept on the device and syncs automatically when you are back online (no duplicates).
- **Responsive UI:** works on phone, tablet and desktop, with a sticky summary bar on mobile.

## Tech stack

PHP 8+ (PDO), MySQL / MariaDB, vanilla JavaScript, HTML and CSS. No framework, no build step.

## Project structure

```
travel-expense/
├── index.php           # UI (HTML, CSS, JS)
├── api.php             # JSON API: list, save, delete
├── config.example.php  # Copy to config.php and add your database details
├── schema.sql          # Database tables
└── .gitignore          # Keeps config.php (credentials) out of Git
```

## Run locally with XAMPP

1. Install and start **Apache** and **MySQL** in XAMPP.
2. Copy this folder to `C:\xampp\htdocs\travel-expense`.
3. Open `http://localhost/phpmyadmin`, create a database (for example `travel_expense`), choose **Import**, and import `schema.sql`.
4. Copy `config.example.php` to `config.php` and set your details:
   ```php
   const DB_HOST = 'localhost';
   const DB_NAME = 'travel_expense';
   const DB_USER = 'root';
   const DB_PASS = '';
   ```
5. Open `http://localhost/travel-expense/`.

## Deploy on cPanel hosting

1. In **MySQL Databases**, create a database and a user, and give the user **All Privileges**. cPanel adds your account prefix to both names.
2. In **phpMyAdmin**, select the database and import `schema.sql`.
3. Copy `config.example.php` to `config.php` and enter the live database name, user and password. Keep `DB_HOST` as `localhost`.
4. Upload `index.php`, `api.php` and `config.php` to your domain or subdomain folder (for example `public_html/travel`).
5. In **MultiPHP Manager**, set PHP 8.0 or newer, and make sure AutoSSL is active.
6. Open the site, add an expense and save. The trip should appear in History.

## Updating an older install

If you installed before team members were added, run this once in phpMyAdmin:

```sql
ALTER TABLE trips ADD COLUMN members TEXT NULL AFTER days;
```

## How the history cache works

1. The page loads the saved history from `localStorage` and shows it immediately.
2. It then calls `api.php?a=list&v=<version>`. The server builds a version hash from the trip count, newest id and last update time.
3. If the hash matches, the server replies `{"unchanged": true}`. Otherwise it returns the full list and the cache is refreshed.
4. Failed saves are queued in `localStorage` and retried when the browser comes back online.

## API

| Action | Method | Description |
|---|---|---|
| `api.php?a=list&v=<version>` | GET | Returns trips with items, or `unchanged` if the version matches |
| `api.php?a=save` | POST (JSON) | Creates or updates a trip by `client_uid` |
| `api.php?a=delete` | POST (JSON) | Deletes a trip by `client_uid` |

## Security notes

- There is **no login**. Anyone who can open the URL can view and delete trips. Before sharing the link, protect the folder with cPanel **Directory Privacy** or add authentication (and a `user_id` column on `trips`).
- Never commit `config.php`. It is listed in `.gitignore`.
- All database queries use PDO prepared statements, and user text is escaped before it is shown.

## Roadmap

- Who paid and who owes whom
- CSV and PDF export
- Currency switcher
- User login and per-user history
- Dark mode

## License

Choose a license (for example MIT) and add a `LICENSE` file before publishing.
