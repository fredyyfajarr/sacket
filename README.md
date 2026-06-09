```text
  _________              __           __   
 /   _____/____    ____ |  | __ _____/  |_ 
 \_____  \\__  \ _/ ___\|  |/ // __ \   __\
 /        \/ __ \\  \___|    <\  ___/|  |  
/_______  (____  /\___  >__|_ \\___  >__|  
        \/     \/     \/     \/    \/      
```

<div align="center">
  <img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel" />
  <img src="https://img.shields.io/badge/Filament-FFAA00?style=for-the-badge&logo=laravel&logoColor=white" alt="Filament" />
  <img src="https://img.shields.io/badge/Midtrans-00A9E0?style=for-the-badge&logo=midtrans&logoColor=white" alt="Midtrans" />
</div>

## 📑 Table of Contents
- [About The Project](#about-the-project)
- [Key Features](#key-features)
- [Tech Stack](#tech-stack)
- [Project Structure](#project-structure)
- [Getting Started](#getting-started)
- [Usage](#usage)
- [Contributing](#contributing)
- [License / Copyright](#license--copyright)

## 🚀 About The Project

**Sacket** is a highly capable e-commerce and transaction processing application tailored for secure ticket and item purchasing. It acts as a comprehensive end-to-end solution combining an interactive customer frontend with a powerful, dynamic admin panel for management and data analysis. Built on the rock-solid Laravel 11 framework, Sacket prioritizes secure real-time transactions and sophisticated role-based user management.

What sets Sacket apart is its deep integration with professional enterprise tools. Utilizing the Midtrans payment gateway, transactions are processed securely and dynamically. With tools like `simplesoftwareio/simple-qrcode` for instant validation, `spatie/laravel-permission` for intricate access controls, and `barryvdh/laravel-dompdf` for receipt generation, this application is built to handle commercial-level throughput effortlessly. 

## ✨ Key Features
- **Integrated Payments**: Flawless transaction handling via the Midtrans payment gateway API, supporting multiple localized payment methods.
- **Enterprise Admin Panel**: Powered by Filament 3, allowing administrators to intuitively manage inventory, transactions, users, and roles.
- **Instant QR Validation**: Automatically generate and validate secure QR codes for tickets and receipts using `simple-qrcode`.
- **Advanced Role Management**: Secure, granular access control matrices orchestrated through `spatie/laravel-permission`.
- **Comprehensive Reporting**: Export precise financial and inventory data directly into PDF and Excel formats utilizing `filament-excel` and `laravel-dompdf`.

## 🛠 Tech Stack
- **Framework:** Laravel 11.x
- **Admin Dashboard:** Filament PHP 3.x
- **Payment Gateway:** Midtrans PHP
- **Authorization:** Spatie Laravel Permission
- **Utilities:** Simple-QRCode, DOMPDF, Filament Excel
- **Frontend Assets:** Tailwind CSS, AlpineJS (compiled via Vite)

## 📂 Project Structure
```text
sacket/
├── app/                  # Application HTTP controllers, Filament resources, and logic
├── config/               # System and package configurations (including Midtrans)
├── database/             # Schema definitions, seeders, and factory logic
├── public/               # Exposed web root and Vite-compiled assets
├── resources/            # Front-end Blade views, Tailwind stylesheets
├── routes/               # Defined Web and API interface routes
├── tests/                # Feature and unit test scaffolding
└── composer.json         # PHP vendor packages
```

## 🏁 Getting Started

### Prerequisites
- **PHP**: v8.2 or newer
- **Composer**: Dependency manager
- **Node.js**: v18.x or newer
- **Database**: e.g. MySQL, PostgreSQL, SQLite

### Installation
1. Clone the repository:
   ```bash
   git clone https://github.com/fredyyfajarr/sacket.git
   ```
2. Enter the root directory:
   ```bash
   cd sacket
   ```
3. Install PHP packages:
   ```bash
   composer install
   ```
4. Install Node dependencies and compile assets:
   ```bash
   npm install && npm run build
   ```
5. Duplicate `.env.example` to `.env` and fill in your Midtrans API credentials alongside your database connection properties.
   ```bash
   cp .env.example .env
   php artisan key:generate
   php artisan migrate --seed
   ```

## 💻 Usage

To launch the integrated local development environment:

```bash
composer dev
```
*Note: This utilizes Laravel's concurrent command runner to start the server, background queues, and Vite simultaneously.*

- **Public Frontend**: `http://localhost:8000`
- **Admin Panel**: `http://localhost:8000/admin` (or the configured Filament path)

## 🤝 Contributing

Contributions are what make the open source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.
1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License / Copyright

Copyright &copy; 2026 Fredy Fajar Adi Putra. All Rights Reserved.
