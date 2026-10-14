# FreshCart — Separate HTML, CSS, JavaScript, PHP and MySQL

## Folder structure
- `index.html`, `cart.html`, `checkout.html`: HTML pages only
- `assets/style.css`: all styling
- `js/app.js`: browser interactions and API requests
- `php/config.php`: database connection and JSON helpers
- `php/products.php`: product listing, search and category filtering
- `php/cart.php`: session-based cart API
- `php/checkout.php`: saves demo orders to MySQL
- `database.sql`: database schema and sample products

## Run with XAMPP
1. Copy the `FreshCart_Separate_Files` folder to `C:\\xampp\\htdocs\\FreshCart`.
2. Start Apache and MySQL in XAMPP.
3. Import `database.sql` through `http://localhost/phpmyadmin/`.
4. Check database credentials in `php/config.php` (default XAMPP: root with blank password).
5. Open `http://localhost/FreshCart/index.html`.

Do not open the HTML pages using `file://`; PHP APIs require Apache.
This is a demo only. It does not process real payments or deliveries.
