# 🚀 Complete Deployment & Running Guide: DREAMERS PCB

This comprehensive guideline sheet provides step-by-step instructions for running this Laravel e-commerce application on **Localhost (Development)** and deploying it to a **Live Server via cPanel (Production)**.

---

## 📋 System Requirements

Ensure the server or local machine meets these minimum requirements:

| Component | Minimum Version | Recommended Version |
| :--- | :--- | :--- |
| **PHP** | 8.2.0 or higher | PHP 8.2 or 8.3 |
| **Composer** | 2.5+ | Latest 2.x |
| **Database** | MySQL 5.7+ / MariaDB 10.3+ | MySQL 8.0+ / MariaDB 10.5+ |
| **Web Server** | Apache (with `mod_rewrite`) or Nginx | Apache with mod_rewrite |
| **Required PHP Extensions** | `BCMath`, `Ctype`, `Fileinfo`, `JSON`, `Mbstring`, `OpenSSL`, `PDO`, `pdo_mysql`, `Tokenizer`, `XML`, `GD` / `Imagick`, `Zip` | All enabled |

---

## 💻 PART 1: Running on Localhost (Windows / macOS / Linux)

### Step 1: Clone the Repository
Open your terminal or PowerShell and clone the codebase from GitHub:

```bash
git clone https://github.com/freelancersalman30/ecomdev.git
cd ecomdev
```

---

### Step 2: Install Composer Dependencies
Install all required PHP backend packages:

```bash
composer install
```

*(Optional for frontend assets if modifying Tailwind/Vite)*:
```bash
npm install
```

---

### Step 3: Configure the Environment File (`.env`)
Duplicate `.env.example` to create your local `.env`:

**Windows (PowerShell):**
```powershell
Copy-Item .env.example .env
```

**macOS / Linux:**
```bash
cp .env.example .env
```

Open `.env` in your code editor and configure your local database connection:

```ini
APP_NAME="DREAMERS PCB"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecomdev
DB_USERNAME=root
DB_PASSWORD=
```

> **Note:** Make sure MySQL is running (e.g., via XAMPP, Laragon, or native MySQL server) and create a blank database named `ecomdev`.

---

### Step 4: Generate Application Encryption Key
Generate the unique `APP_KEY` for sessions and password hashing:

```bash
php artisan key:generate
```

---

### Step 5: Run Migrations & Seed Sample Data
Run migrations and populate the database with categories, brands, products, demo accounts, and site settings:

```bash
php artisan migrate --seed
```

---

### Step 6: Create Storage Symlink
Link the `storage/app/public` directory to `public/storage` so user uploads, product images, and site logos display properly:

```bash
php artisan storage:link
```

---

### Step 7: Start the Local Development Server
Run the built-in development server:

```bash
php artisan serve
```

Your terminal will output:
```
INFO  Server running on [http://127.0.0.1:8000].
```

---

### Step 8: Access the Application & Demo Credentials

| Area | URL | Demo Email / Phone | Password |
| :--- | :--- | :--- | :--- |
| **Storefront** | [http://localhost:8000](http://localhost:8000) | Public Access | N/A |
| **Admin Panel Login** | [http://localhost:8000/admin/login](http://localhost:8000/admin/login) | `admin@dreamerspcb.com` | `password` |
| **Customer Portal Login** | [http://localhost:8000/login](http://localhost:8000/login) | `01711223344` | `password` |
| **Site Settings Hub** | [http://localhost:8000/admin/settings/general](http://localhost:8000/admin/settings/general) | (Requires Admin Login) | N/A |
| **Live Product Feeds** | [http://localhost:8000/feeds/facebook-catalog.xml](http://localhost:8000/feeds/facebook-catalog.xml) | Public XML Feed | N/A |

---

## 🌐 PART 2: Deploying to a Live Server via cPanel

There are two primary methods to deploy on cPanel:
- **Method A: cPanel Git Version Control (Recommended & Easiest for updates)**
- **Method B: Manual Zip Upload via File Manager**

---

### Step 1: Verify & Set PHP Version in cPanel
1. Log into your cPanel dashboard.
2. Under the **Software** section, click **Select PHP Version** or **MultiPHP Manager**.
3. Choose **PHP 8.2** or **PHP 8.3** for your domain.
4. Click **Extensions** and ensure the following are checked:
   - `bcmath`, `curl`, `fileinfo`, `gd`, `json`, `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`, `zip`.

---

### Step 2: Create MySQL Database & User in cPanel
1. In cPanel, go to **Databases** &rarr; **MySQL Database Wizard**.
2. **Step 1: Create a Database**: Enter a name (e.g. `youruser_ecomdev`). Click *Next Step*.
3. **Step 2: Create Database User**: Enter a username (e.g. `youruser_dbuser`) and generate a strong password. Click *Create User*.
4. **Step 3: Add User to Database**: Check **ALL PRIVILEGES** and click *Make Changes*.
5. Save your database credentials securely:
   - **Database Name**: `youruser_ecomdev`
   - **Database Username**: `youruser_dbuser`
   - **Database Password**: `your_strong_password`
   - **Database Host**: `localhost` (or `127.0.0.1`)

---

### Step 3: Deploy Project Files to cPanel

#### Option A: Using cPanel Git Version Control (Recommended)
1. In cPanel, navigate to **Files** &rarr; **Git™ Version Control**.
2. Click **Create**.
3. Fill in the details:
   - **Clone URL**: `https://github.com/freelancersalman30/ecomdev.git`
   - **Repository Path**: `ecomdev` (this creates `/home/youruser/ecomdev` **outside** `public_html`).
   - **Repository Name**: `ecomdev`
4. Click **Create**.
5. Wait for the clone to complete. Whenever you make updates on GitHub, just open Git Version Control and click **Pull or Deploy**!

#### Option B: Upload via cPanel File Manager (Zip Archive)
1. On your local machine, zip the project files (exclude `/vendor`, `/node_modules`, and `.env`).
2. In cPanel, open **File Manager**.
3. In your home directory (`/home/youruser/`), create a new folder named `ecomdev`.
4. Upload your zip file into `/home/youruser/ecomdev/` and extract it.

---

### Step 4: Configure the Public Web Root (Crucial Architecture Step)

For security, Laravel's core application files (`app`, `bootstrap`, `config`, `.env`) should reside **outside** the public web root (`public_html`), while only the `public` folder contents are accessible to visitors.

Choose **One** of the two standard cPanel setups below:

#### Setup 1: Domain Document Root Method (Best & Cleanest)
If your cPanel allows modifying the document root for your primary domain or subdomain:
1. Go to **Domains** &rarr; **Domains** in cPanel.
2. Locate your domain and edit its **Document Root**.
3. Set the Document Root path to:
   ```
   /home/youruser/ecomdev/public
   ```
4. Save changes. Your live domain will now serve directly from the secure `public/` directory!

#### Setup 2: Traditional `public_html` Symlink or Move Method
If you must use standard `public_html`:
1. In cPanel **File Manager**, open `/home/youruser/ecomdev/public/`.
2. Move all files and folders inside `public/` (`index.php`, `.htaccess`, `favicon.ico`, `uploads/`, etc.) directly into `/home/youruser/public_html/`.
3. Edit `/home/youruser/public_html/index.php` and update the bootstrap paths (around line 14 & 20):
   ```php
   // Change from:
   require __DIR__.'/../vendor/autoload.php';
   $app = require_once __DIR__.'/../bootstrap/app.php';

   // Change to:
   require __DIR__.'/../ecomdev/vendor/autoload.php';
   $app = require_once __DIR__.'/../ecomdev/bootstrap/app.php';
   ```

---

### Step 5: Configure Production `.env` on Live Server
1. In cPanel File Manager, ensure **Show Hidden Files (dotfiles)** is enabled in Settings (top-right gear icon).
2. Inside `/home/youruser/ecomdev/`, create or edit `.env`.
3. Update the following production variables:

```ini
APP_NAME="DREAMERS PCB"
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_KEY_HERE
APP_DEBUG=false
APP_URL=https://yourdomain.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=youruser_ecomdev
DB_USERNAME=youruser_dbuser
DB_PASSWORD=your_strong_password

SESSION_DRIVER=database
SESSION_LIFETIME=120

CACHE_STORE=database
QUEUE_CONNECTION=database
```

---

### Step 6: Install Composer Dependencies & Run Migrations

#### If you have cPanel Terminal Access (SSH):
1. In cPanel, click **Terminal** under the **Advanced** section.
2. Navigate to your project directory:
   ```bash
   cd ~/ecomdev
   ```
3. Install production dependencies:
   ```bash
   composer install --no-dev --optimize-autoloader
   ```
4. Generate the application key (if not set):
   ```bash
   php artisan key:generate
   ```
5. Run migrations and database seeding:
   ```bash
   php artisan migrate --seed --force
   ```
6. Link public storage:
   ```bash
   php artisan storage:link
   ```

#### If you DO NOT have Terminal Access (Shared Hosting without SSH):
1. **Vendor Folder**: Upload the `/vendor` directory from your local machine via FTP or File Manager (compress as zip, upload, and extract).
2. **Database Import**:
   - On localhost, export your populated database using phpMyAdmin (`localhost/phpmyadmin`) as an `.sql` file.
   - On cPanel, open **phpMyAdmin**, select `youruser_ecomdev`, click **Import**, and upload the `.sql` file.
3. **Storage Symlink via PHP**:
   - Create a file named `link.php` inside `public_html` with:
     ```php
     <?php
     symlink('/home/youruser/ecomdev/storage/app/public', '/home/youruser/public_html/storage');
     echo "Storage link created successfully!";
     ```
   - Visit `https://yourdomain.com/link.php` in your browser once, then delete `link.php`.

---

### Step 7: Set File & Folder Permissions
In cPanel File Manager or Terminal, ensure proper read/write permissions:

```bash
chmod -R 755 /home/youruser/ecomdev
chmod -R 775 /home/youruser/ecomdev/storage
chmod -R 775 /home/youruser/ecomdev/bootstrap/cache
```

Also ensure the public upload directory is writable:
```bash
chmod -R 775 /home/youruser/ecomdev/public/uploads
```

---

### Step 8: Cache Configuration & Optimize for Live Production
Run these optimization commands in cPanel Terminal to drastically improve response times:

```bash
cd ~/ecomdev
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

> **Note:** Whenever you modify `.env` on live server, remember to clear and rebuild the configuration cache:
> ```bash
> php artisan config:clear
> php artisan config:cache
> ```

---

### Step 9: Configure Automatic SSL (HTTPS)
1. In cPanel, navigate to **Security** &rarr; **SSL/TLS Status**.
2. Select your domain name and click **Run AutoSSL**.
3. Wait 1-2 minutes until a green lock badge appears showing the free cPanel Let's Encrypt / Sectigo SSL is active.
4. To enforce HTTPS across the website, ensure `.htaccess` in your public root has HTTPS rewrite rules:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>
```

---

### Step 10: Set Up Laravel Cron Job (Task Scheduler)
Laravel's task scheduler handles database cleanups, recurring notifications, and background processing.

1. In cPanel, search for **Cron Jobs** under the **Advanced** section.
2. In **Add New Cron Job**:
   - **Common Settings**: Select *Once Per Minute* (`* * * * *`).
   - **Command**:
     ```bash
     /usr/local/bin/php /home/youruser/ecomdev/artisan schedule:run >> /dev/null 2>&1
     ```
     *(Adjust the PHP binary path if your host uses `/usr/bin/php` or `/opt/cpanel/ea-php82/root/usr/bin/php`)*.
3. Click **Add New Cron Job**.

---

## 🛠️ PART 3: Post-Deployment Verification Checklist

Verify these key operational features on your live domain:

- [ ] **Home Storefront**: Open `https://yourdomain.com/` and confirm banner, products, and categories render with full styling.
- [ ] **Admin Login**: Go to `https://yourdomain.com/admin/login` and log in with your admin credentials.
- [ ] **Customer Authentication**: Visit `https://yourdomain.com/login` and test customer login/registration.
- [ ] **Fast Checkout**: Add a product to cart and complete an order via Cash on Delivery (`/checkout`).
- [ ] **Site Settings**: Open `/admin/settings/general` and verify:
  - Tab 1: Logo & branding assets upload and display correctly.
  - Tab 2: Changing website colors and Google font families updates live storefront.
  - Tab 8: Copying XML catalog feeds (`/feeds/facebook-catalog.xml` and `/feeds/google-merchant.xml`) returns valid XML.
- [ ] **Order Success & Receipts**: View an order invoice (`/admin/orders/{id}/invoice`) and POS thermal receipt (`/admin/orders/{id}/receipt`).

---

## 🔍 PART 4: Common Troubleshooting & Solutions

### 1. "HTTP 500 Internal Server Error"
- **Cause**: Missing `.env`, file permission issue, or missing database table.
- **Solution**:
  1. Open cPanel File Manager & check `/home/youruser/ecomdev/storage/logs/laravel.log`. The exact error will be logged with a timestamp.
  2. Run `chmod -R 775 storage bootstrap/cache`.
  3. Ensure `APP_KEY` is present in `.env`.

### 2. "SQLSTATE[HY000] [1045] Access denied for user"
- **Cause**: Incorrect database username, password, or database name in `.env`.
- **Solution**: Re-verify credentials in cPanel **MySQL Databases**. Remember to run `php artisan config:clear` after editing `.env`.

### 3. Missing Images / 404 on Uploads
- **Cause**: Storage symlink is missing or pointing to the wrong path.
- **Solution**:
  - Run `php artisan storage:link` in terminal.
  - Check that `/public/uploads/` has `775` permissions.

### 4. Styles or CSS Not Updating After Changes
- **Cause**: Live caching of views and configurations.
- **Solution**: Run this one-line command to refresh all caches:
  ```bash
  php artisan optimize:clear
  ```

### 5. Cannot See `.env` File in cPanel
- **Cause**: Linux dotfiles are hidden by default in cPanel.
- **Solution**: Click the **Settings** button in the top-right corner of cPanel File Manager, check **Show Hidden Files (dotfiles)**, and click Save.
