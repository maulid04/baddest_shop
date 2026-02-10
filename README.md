# Baddest Shop Inventory Management System

A complete PHP, MySQL, HTML5, CSS3, and JavaScript-based inventory management system for SMEs.

## Features

- User registration and login with role-based access (Customer and Admin)
- Product browsing and ordering for customers
- Admin panel for managing products, inventory, users, and sales reports
- Responsive design
- Secure password hashing
- MySQL database with normalized schema

## Installation

1. Install XAMPP and start Apache and MySQL.

2. Create a database named `baddest_inventory` in phpMyAdmin.

3. Import the `db/database.sql` file into the database.

4. Place the entire `baddest-inventory-system` folder in `htdocs` (e.g., `C:\xampp\htdocs\baddest-inventory-system`).

5. Open your browser and go to `http://localhost/baddest-inventory-system`.

## Default Accounts

- Admin: username `admin`, password `password`
- Customer: username `customer1`, password `password`

## Folder Structure

```
baddest-inventory-system/
├── index.php
├── get_products.php
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── scripts.js
│   └── images/
│       ├── product1.jpg
│       ├── product2.jpg
│       ├── product3.jpg
│       ├── product4.jpg
│       └── product5.jpg
├── customer/
│   ├── login.php
│   ├── register.php
│   ├── dashboard.php
│   ├── orders.php
│   ├── profile.php
│   ├── logout.php
│   ├── get_orders.php
│   └── get_profile.php
├── admin/
│   ├── login.php
│   ├── dashboard.php
│   ├── manage_products.php
│   ├── manage_inventory.php
│   ├── manage_users.php
│   ├── sales_reports.php
│   ├── logout.php
│   ├── get_products.php
│   ├── get_inventory.php
│   ├── get_users.php
│   └── get_reports.php
└── db/
    ├── connection.php
    └── database.sql
```

## Technologies Used

- PHP 7+
- MySQL
- HTML5
- CSS3
- Vanilla JavaScript
- PDO for database interactions

## Security Notes

- Passwords are hashed using PASSWORD_DEFAULT.
- Role-based access control implemented.
- Input validation on forms.
- Prepared statements used to prevent SQL injection.

## Future Enhancements

- Shopping cart functionality
- Payment integration
- Email notifications
- Advanced reporting
- Image upload improvements