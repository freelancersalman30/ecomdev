# ⚡ DREAMERS PCB - Enterprise E-Commerce & Hardware Marketplace

<p align="center">
  <strong>Bangladesh's #1 Electronic Components, Development Boards & PCB Hardware Marketplace</strong>
</p>

---

## 📖 Complete Setup & Deployment Guide
> 📘 **Looking for comprehensive deployment instructions?**  
> Check out the [**Deployment & Server Guide (DEPLOYMENT_GUIDE.md)**](DEPLOYMENT_GUIDE.md) for full localhost commands, cPanel shared hosting setup, folder architecture, SSL, cron jobs, and troubleshooting.

---

## ⚡ Quick Start on Localhost

```bash
# 1. Clone the repository
git clone https://github.com/freelancersalman30/ecomdev.git
cd ecomdev

# 2. Install PHP backend dependencies
composer install

# 3. Environment configuration
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Run database migrations & seeders
php artisan migrate --seed

# 6. Create public storage symlink
php artisan storage:link

# 7. Start local server
php artisan serve
```

Open [http://localhost:8000](http://localhost:8000) in your browser.

---

## 🔐 Demo Credentials

| Role | Login URL | Credentials |
| :--- | :--- | :--- |
| **Super Admin** | [`/admin/login`](http://localhost:8000/admin/login) | **Email:** `admin@dreamerspcb.com`<br>**Password:** `password` |
| **Customer** | [`/login`](http://localhost:8000/login) | **Phone:** `01711223344`<br>**Password:** `password` |

---

## 🌟 Key Platform Features

- **Enterprise Dual Authentication**: Fully isolated Admin (`web`) and Customer (`customer`) guards with rate limiting and automated session regeneration.
- **Enterprise Site Settings Hub**: 9-tab control suite for store identity, logos, shipping, invoices, notices, and social links.
- **Typography & Font Family Studio**: Real-time Google Font selectors (`Inter`, `Plus Jakarta Sans`, `Outfit`, `Hind Siliguri`, `Space Grotesk`, `JetBrains Mono`) with dynamic runtime font loading.
- **14-Token Complete Website Color Matrix**: Fine-tune primary, hover, secondary, canvas background, card surfaces, price tags, badges, and headers/footers with 6 one-click curated presets.
- **Ad Setup & Multi-Channel Marketing Connect**:
  - **Facebook & Instagram Shop XML Feed**: [`/feeds/facebook-catalog.xml`](http://localhost:8000/feeds/facebook-catalog.xml)
  - **Google Merchant Center Shopping Feed**: [`/feeds/google-merchant.xml`](http://localhost:8000/feeds/google-merchant.xml)
  - **Google Ads & AdSense**: Conversion tracking tag with purchase event triggers and AdSense monetization.
  - **Social Pixels**: Meta Pixel (with Conversions API CAPI token & domain verification) and TikTok Pixel.
- **POS & Billing Module**: Printable branded invoices and 80mm thermal receipts with dynamic site logo and tax numbers.
- **Fast One-Page Checkout**: Cash on Delivery (COD) and automated delivery calculations for Inside Dhaka (৳80) and Outside Dhaka (৳150).

---

## 📄 License
Open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

