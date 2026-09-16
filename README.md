# Flame Crown Grill

A full-stack restaurant ordering website built with **PHP** and **MySQL**. Customers can browse the menu, search for items, add them to a cart and place orders. Staff use a password-protected admin panel to manage the menu, orders, images, customer messages and the site's color theme.

![Home page](screenshots/home.png)

## Features

### Customer site

- **Home page** with an image slider, menu categories and featured items
- **Menu by category**, with a dropdown in the navigation bar that updates as categories change
- **Search** across all active menu items
- **Product detail pages**, including a drink choice for combo meals
- **Shopping cart** stored in the session, with a live item count in the navigation bar
- **Checkout** with pickup or delivery ($5.99), a coupon code (`SAVE10` for 10% off), 6.5% sales tax and an order confirmation page
- **Contact form** that saves messages for the admin team

### Admin panel

- **Secure login** using hashed passwords (`password_hash` / `password_verify`) and session checks on every admin page
- **Dashboard** with counts of categories, products and messages
- **Categories and products:** add, edit, delete, and set items as Active or Inactive
- **Orders:** search and filter by status, view order details and line items, and move orders through *New → Preparing → Ready → Completed / Cancelled*
- **Images:** upload files (JPG, PNG, WEBP, GIF) or link to an image URL, and attach images to products or categories, with search and pagination
- **Site settings:** change heading, text, header, body and footer colors from the browser, without editing code
- **Messages:** read and delete contact form submissions

## Screenshots

| Menu | Cart & Checkout |
|------|-----------------|
| ![Menu](screenshots/menu.png) | ![Checkout](screenshots/checkout.png) |

| Admin Dashboard | Order Management |
|-----------------|------------------|
| ![Admin dashboard](screenshots/admin-dashboard.png) | ![Orders](screenshots/admin-orders.png) |

## Tech Stack

- **Backend:** PHP (MySQLi, prepared statements, sessions)
- **Database:** MySQL / MariaDB
- **Frontend:** HTML, CSS, Bootstrap 5

## Project Structure

```
├── admin/          # Admin panel (login, dashboard, products, orders, images, settings, messages)
├── assets/
│   ├── css/        # Custom styles
│   ├── images/     # Menu photos and logo
│   └── js/
├── config/
│   └── dbcon.php   # Database connection (add your own credentials)
└── public/         # Customer-facing site (home, menu, search, cart, checkout, contact)
```

## Database

The site uses these tables:

| Table | Purpose |
|-------|---------|
| `Categories_JE20936` | Menu categories |
| `Products_JE20936` | Menu items, prices and images |
| `Images_JE20936` | Uploaded or linked images |
| `Orders_JE20936` | Customer orders and totals |
| `OrderItems_JE20936` | Items in each order |
| `ContactMessages_JE20936` | Contact form submissions |
| `AdminUsers_JE20936` | Admin accounts (hashed passwords) |
| `SiteSettings_JE20936` | Site color theme |

## Running Locally

1. Install a local PHP and MySQL environment such as [XAMPP](https://www.apachefriends.org/).
2. Copy this project into your web server folder (for example `xampp/htdocs/flame-crown-grill`).
3. Create a MySQL database and the tables listed above.
4. Open `config/dbcon.php` and enter your database details:
   ```php
   $servername = "localhost";
   $username = "YOUR_DB_USERNAME";
   $password = "YOUR_DB_PASSWORD";
   $dbname = "YOUR_DB_NAME";
   ```
5. Go to `http://localhost/flame-crown-grill/public/home_xjolly1.php` in your browser.
6. For the admin panel, go to `/admin/login.php`.

## Author

**Your Name**
[LinkedIn]([https://www.linkedin.com/in/your-profile](https://www.linkedin.com/in/xavier-jolly-80017320a/)) · [Email]xmjolly@gmail.com)
