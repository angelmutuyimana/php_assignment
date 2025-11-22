# AngelBookStore (PHP + MySQL)

Beginner-friendly bookstore web application using procedural PHP, MySQL for data storage, and simple HTML/CSS.

## Features
- Home page with featured books and categories
- Role-based signup/login (Admin or Buyer) with sessions
- Admin Dashboard: stats (books/users/purchases) & recent purchases
- Admin book & user management (CRUD + role changes)
- Buyer Dashboard: available books & purchased list
- Book detail page with simulated purchase
- Responsive layout without frameworks

## Requirements
- PHP 7.4+ (XAMPP/WAMP/LAMP etc.)
- MySQL/MariaDB (included with XAMPP)
- Web server pointing to this folder (`c:/xampp/htdocs/abc`)

## Getting Started (XAMPP on Windows)
1. Copy the project folder into `c:/xampp/htdocs/abc` (already here if you are reading this).
2. Start Apache and MySQL in XAMPP Control Panel.
3. Create the database and tables by importing `db.sql`:
  - phpMyAdmin: open `http://localhost/phpmyadmin`, click Import, choose `db.sql`, and run.
  - Or via CLI (adjust path if needed):

```powershell
"C:\xampp\mysql\bin\mysql.exe" -u root < "C:\xampp\htdocs\abc\db.sql"
```

4. If your MySQL credentials differ, edit `includes/db.php` (host, user, pass, db).
5. Open browser: `http://localhost/abc/`
6. Signup for a new account; login and explore dashboard; purchase books via the book detail page.

## Roles
- `admin`: access to admin dashboard, manage books, manage users, view recent purchases.
- `buyer`: browse and purchase books, view own purchases.

Select role during signup. To change later, an admin can switch roles via the Users management page.

## Data Storage (MySQL)
- Database: `bookstore`
- Tables: `users` (id, name, email, password, role, created_at)
- `books` (id, title, author, price, category, image, featured, description)
- `purchases` (id, user_id, book_id, purchase_date)

Books added via Admin page use a placeholder image path `assets/images/placeholder.jpg` — you can create that directory and add images.

## Folder Structure
```
abc/
  index.php
  signup.php
  login.php
  logout.php
  dashboard.php (Buyer dashboard)
  admin_dashboard.php (Admin overview)
  profile.php
  book.php
  admin.php
  admin_users.php
  includes/
    header.php
    nav.php
    footer.php
    data.php
  db.sql
  assets/
    css/
      style.css
```

## Customization
- Update colors in `assets/css/style.css` root variables.
- Add more books via Admin page or by inserting rows into `books`.
- Replace images in `assets/images/` and adjust `image` paths accordingly.

## Notes
- Fully MySQL-backed (JSON no longer used).
- Passwords hashed with `password_hash()`, verified with `password_verify()`.
- Purchases simulated (no payment integration).

## Troubleshooting
- If sessions fail, ensure `session.save_path` is writable in PHP config.
- If changes to JSON are not saving, verify file permissions.

Enjoy learning PHP! :)
