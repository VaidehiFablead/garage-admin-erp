 # Fablead Garage ERP System

A Laravel-based garage ERP for inventory, billing and workshop management.

## Highlights

- Inventory, sales and purchase management
- Point-of-sale (POS) / Sales invoices (PDF export)
- Purchase invoices and returns
- Custom invoices and credit/debit notes
- Job cards, appointments and workshop management
- Payroll, attendance, leave management and salary processing
- Advance payments, payment tracking and EMI support
- Reporting: GST, sales, purchase, expense, inventory and profit reports
- Role/module based permissions (modules table + user permissions)
- Multi-branch / sub-branch support
- Excel and PDF exports, barcode support
- Face-login API endpoint and mobile/API support (Laravel Passport)

## Modules (major)

- Products & Inventory (products, product_inventory, units, brands, categories)
- Sales & POS (sales, invoices, orders, order_items, discounts)
- Purchases (purchase_invoice, purchases, purchases_autocare)
- Customers & Vendors (users with customer/vendor roles)
- Job Cards & Workshop (job_cards, labour_services, appointments)
- Payments (payment_store, advance_payments, payment methods, EMIs)
- Payroll & HR (payroll, attendance, leave_type, leaves, salaries)
- Reports (gst_sales_report, sales.report, purchases.report, inventory.report)
- Expenses (expenses, expense_store, expense_types)
- Quotations, Returns (sales/purchase returns), Credit/Debit notes
- Settings, Company Rules, Sub-branches and Modules/Permissions

## Requirements

- PHP 8.1+
- Composer
- MySQL (or compatible DB)
- PHP extensions required by Laravel (pdo, mbstring, openssl, etc.)

## Quick Installation

1. Clone this repository and enter the project folder:

```bash
git clone https://github.com/rajsingh10/garage-erp.git
cd garage-erp
```

2. Install PHP dependencies and frontend packages:

```bash
composer install
```

3. Environment and key:

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and set at minimum:

- `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `APP_URL` (e.g. http://127.0.0.1:8000)
- `ImagePath` (set to your APP_URL and end with `/`)

4. Database, Passport and seeders:

```bash
php artisan migrate
php artisan passport:install --force
php artisan db:seed
```

5. Create storage symlink and start the server:

```bash
php artisan storage:link
php artisan serve
```

Open the app at `APP_URL` in your browser.

## APIs & Authentication

- API endpoints use Laravel Passport for token-based auth. Run `php artisan passport:install` after migrations.
- There is a face-login endpoint used for biometric/face-based login (`/face-login`).

## Default Login (after seeding)

- Email: admin@gmail.com
- Password: 12345678

Change the default password after first login.

## Useful Commands

```bash
php artisan migrate
php artisan db:seed
php artisan migrate:fresh --seed
php artisan passport:install --force
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Troubleshooting

- No styling / broken assets: check `APP_URL` and `ImagePath` in `.env`, then run `npm run build`.
- Auth or API issues: re-run `php artisan passport:install --force` and clear config cache.
- If migrations fail, ensure MySQL user and database exist and `.env` DB credentials are correct.

## Notes for Developers

- Routes and modules: web routes are defined in [routes/web.php](routes/web.php) and map to many admin controllers (ProductController, SalesController, PurchaseController, JobCardController, PayrollController, etc.).
- Modules and permission system: there is a `modules` table and `user_permissions` table to grant view/add/edit/delete per module.
- Key models live under `app/Models` (Product, Order, JobCard, Payroll, UserPermission, PaymentStore, etc.).

## License

This project is proprietary software developed by Fablead Developers Technolab. All rights reserved.

---

If you want, I can further tailor the README (add screenshots, example env, or a Getting Started for contributors). Tell me what to include next.
   ```

   Open `http://127.0.0.1:8000` in your browser.

## Default Login

Use these credentials after seeding the database:

```text
Email: admin@gmail.com
Password: 12345678
```

Change the default password after your first login.

## Common Commands

```bash
php artisan migrate
php artisan db:seed
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

For a fresh local database setup:

```bash
php artisan migrate:fresh --seed
php artisan passport:install --force
```

## Troubleshooting

- If the page has no styling, check `APP_URL` and `ImagePath` in `.env`.
- If login or save actions fail, run `php artisan passport:install --force` and log in again.
- Use only one local URL while testing, such as `127.0.0.1` or `localhost`.
- If cached settings cause issues, run the clear commands listed above.

## License

This project is proprietary software developed by **Fablead Developers Technolab**. All rights reserved.
