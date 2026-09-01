# 🌐 Live Server Panel Deployment Guide (Subdomain Setup)

This guide walks you through deploying this project to any live hosting panel (**cPanel**, **DirectAdmin**, **Plesk**, or **CyberPanel**) under a subdomain (e.g. `shop.yourdomain.com`, `dev.yourdomain.com`, or `pos.yourdomain.com`).

---

## ⚡ Summary of What Has Been Prepared

1. **Pre-Compiled Production Assets (`public/build/`)**:
   - `npm run build` has been executed.
   - All CSS, JavaScript, fonts, and the Vite `manifest.json` are pre-compiled and tracked.
   - You **do NOT need Node.js or NPM on your live hosting server**.

2. **Root `.htaccess` Bridge**:
   - A secure root `.htaccess` file has been added to the root directory.
   - Even if your control panel does not allow setting the subdomain document root to `/public`, all visitors visiting `subdomain.yourdomain.com` will automatically and cleanly load through `/public` without showing `/public` in the URL.
   - Direct web access to `.env`, `.git`, and application source code is blocked.

3. **SSL & Reverse Proxy Support**:
   - `$middleware->trustProxies(at: '*');` is configured in `bootstrap/app.php`.
   - Free cPanel AutoSSL, Let's Encrypt, and Cloudflare HTTPS will work without mixed-content errors or redirect loops.

4. **Production Template (`.env.production.example`)**:
   - A clean production `.env` template with MySQL settings, secure session cookies, and optimized logging is ready to use.

---

## 🚀 Step-by-Step Deployment (cPanel Example)

### Step 1: Create the Subdomain in cPanel
1. Log into your **cPanel**.
2. Under **Domains**, click **Domains** (or **Subdomains**).
3. Click **Create A New Domain** (or **Create Subdomain**):
   - **Domain**: `shop.yourdomain.com` (replace with your desired subdomain)
   - **Document Root**:
     - **Option A (Recommended)**: Set it to `shop.yourdomain.com/public` (or `public_html/shop/public`).
     - **Option B (If your cPanel locks the path)**: Leave it as `shop.yourdomain.com` or `public_html/shop` — our root `.htaccess` handles this automatically!
4. Click **Submit**.

---

### Step 2: Create MySQL Database & User
1. In cPanel, go to **Databases** &rarr; **MySQL Database Wizard**.
2. **Database Name**: Enter a name (e.g., `ecomdev` &rarr; full name becomes `cpaneluser_ecomdev`).
3. **Database User**: Enter a user (e.g., `ecomuser` &rarr; full name becomes `cpaneluser_ecomuser`) and a strong password.
4. **Permissions**: Check **ALL PRIVILEGES** and click **Make Changes**.
5. Note down:
   - **Database Name**: `cpaneluser_ecomdev`
   - **Database Username**: `cpaneluser_ecomuser`
   - **Database Password**: Your chosen password
   - **Host**: `127.0.0.1` (or `localhost`)

---

### Step 3: Upload the Files to the Server

Choose either **Method A** (Git) or **Method B** (Zip Upload):

#### Method A: Git Version Control (Fastest for Updates)
1. In cPanel, open **Git™ Version Control** &rarr; **Create**.
2. Enter Clone URL: `https://github.com/freelancersalman30/ecomdev.git`
3. Repository Path: Your subdomain directory (e.g. `shop.yourdomain.com` or `public_html/shop`).
4. Click **Create**.

#### Method B: Zip & Upload via File Manager
1. On your computer, zip the project files.
   > **Note**: You can include `vendor/` if your cPanel does not have Terminal/SSH, or exclude `vendor/` and run `composer install` via Terminal.
2. In cPanel, open **File Manager**.
3. Open your subdomain's folder (e.g. `public_html/shop/` or `shop.yourdomain.com/`).
4. Upload the `.zip` file and click **Extract**.

---

### Step 4: Configure the Environment File (`.env`)
1. In cPanel File Manager, ensure **"Show Hidden Files (dotfiles)"** is enabled in Settings (top right).
2. Inside your subdomain folder, copy `.env.production.example` to `.env`:
   - Or edit `.env` directly in File Manager.
3. Update the following values:

```ini
APP_NAME="DREAMERS PCB"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://shop.yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cpaneluser_ecomdev
DB_USERNAME=cpaneluser_ecomuser
DB_PASSWORD=your_database_password

SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
```

---

### Step 5: Run Terminal / SSH Commands (or use phpMyAdmin)

If you have **Terminal** access in cPanel:
Open **Terminal** and `cd` into your subdomain directory:

```bash
cd public_html/shop   # (or cd shop.yourdomain.com)

# 1. Generate unique application key
php artisan key:generate

# 2. Run database migrations and seed default data
php artisan migrate --seed --force

# 3. Create storage symlink for uploaded product images
php artisan storage:link

# 4. Optimize and cache routes and configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### If you DO NOT have Terminal / SSH Access in cPanel:
1. **APP_KEY**: Copy the `APP_KEY` from your local `.env` file into your live `.env`.
2. **Database Import**:
   - In your local environment or phpMyAdmin, export your database tables to a `.sql` file.
   - In cPanel, open **phpMyAdmin**, select your new database (`cpaneluser_ecomdev`), click **Import**, and upload the `.sql` file.
3. **Storage Symlink without Terminal**:
   - Since `public/uploads` already contains product images directly in the web path, uploaded catalog products will display immediately.
   - For user uploads, create a route in `routes/web.php` temporarily or run a simple 1-line script:
     ```php
     symlink('/home/youruser/subdomain/storage/app/public', '/home/youruser/subdomain/public/storage');
     ```

---

### Step 6: Verify Permissions
In cPanel File Manager, ensure the following folders have **775** or **755** permissions and are writable:
- `storage/` (and all subfolders: `storage/framework/`, `storage/logs/`, `storage/app/`)
- `bootstrap/cache/`

---

### Step 7: Verify Subdomain Online
Open your browser and navigate to:
- **Storefront**: `https://shop.yourdomain.com`
- **Warranty Verification**: `https://shop.yourdomain.com/warranty/verify`
- **Customer Portal**: `https://shop.yourdomain.com/login`
- **Admin Dashboard**: `https://shop.yourdomain.com/admin/login`
  - Default Admin: `admin@dreamerspcb.com`
  - Default Password: `password`

---

## 🛡️ Quick Troubleshooting

| Issue | Solution |
| :--- | :--- |
| **Blank White Screen or 500 Error** | Check `storage/logs/laravel.log`. Ensure `.env` exists and `APP_KEY` is not empty. |
| **Assets / CSS Not Loading** | Check that `APP_URL` in `.env` matches your exact subdomain with `https://`. Our `public/build/` is already compiled. |
| **Mixed Content Warning (HTTP/HTTPS)** | In `.env`, ensure `APP_URL` uses `https://`. `$middleware->trustProxies(at: '*')` is enabled. |
| **"Access Denied for user" DB Error** | Verify DB credentials in `.env` and verify user was added to database with "ALL PRIVILEGES" in cPanel. |
| **404 on Sub-Pages (e.g. /shop, /admin)** | Make sure `mod_rewrite` is enabled on Apache and `.htaccess` is present in both root and `public/`. |
