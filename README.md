# classify

A classifieds marketplace CRUD project built with PHP, Bootstrap and MySQL. Users can create accounts, post ads with images, search listings, and trade.

## Features

- Register/Login with basic session-based authentication
- Create, edit, delete classifieds (ads) with title, description, price and image
- Listings organized by category and listing type (OFFER/WANTED/EXCHANGE)
- Image uploads with main image support
- Responsive UI built with Bootstrap 5
- My Account page to manage listings
- Search capabilities for titles/descriptions

## Technology / Frameworks

- PHP (7.4+ recommended)
- MySQL / MariaDB
- Bootstrap 5.3
- Font Awesome for icons
- PDO for database access

## Installation (Local Development, macOS/MAMP)

Prerequisites:
- MAMP or a LAMP stack (Apache, PHP, MySQL)
- PHP with PDO MySQL extension
- MySQL / MariaDB server

Steps:
1. Clone the repository into your web server root (or `htdocs` for MAMP). Example with MAMP:

    git clone <your-repository-url> /Applications/MAMP/htdocs/classify

2. Ensure the `uploads/` folder exists and is writable by the web server process:

    cd /Applications/MAMP/htdocs/classify
    mkdir -p uploads
    chmod 755 uploads

3. Ensure `includes/db_connect.php` has credentials that match your environment. The file tries several defaults but you may need to update it manually.

4. Start MAMP (or your LAMP stack).

5. Import the database schema:
- Using phpMyAdmin: open http://localhost:8888/phpmyadmin (default MAMP path) and import `sql/schema.sql`.
- Or using CLI (port may vary). On MAMP defaults:

    mysql -u root -p -h 127.0.0.1 -P 8889 < /Applications/MAMP/htdocs/classify/sql/schema.sql

6. (Optional) Seed sample data if you want to test quickly.

7. Open the site in your browser (MAMP default port may be 8888):

    http://localhost:8888/classify/

## Database Setup

1. Create database: `classify_db` (schema.sql uses this name by default).
2. Import `sql/schema.sql` provided in the repo.
3. `includes/db_connect.php` uses auto-detection. Verify or update the credentials under the `$credentials_to_try` array.
4. The `images` table stores the filenames for uploaded photos. Files themselves are stored under `uploads/`.

## User Guide

### Posting an ad
1. Login or Register with a new account.
2. Navigate to "Post Ad".
3. Fill in the form: select Listing Type (OFFER/WANTED/EXCHANGE), choose category, add title, price, description and one image.
4. Submit. You will see a success message and can view the newly posted ad.

### Browsing listings
- The homepage shows latest active listings. Click "View Details" to see the ad page with description and contact options.

### My Account
- Visit "My Trade Profile" (My Account) to manage your own listings (edit/remove). Images aren't required, but recommended for better visibility.
