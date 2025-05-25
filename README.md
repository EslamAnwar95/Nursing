# 🏥 Nursing Platform – Laravel 11 API

A fully featured, modular, and scalable nursing services platform built using Laravel 11.  
Designed to connect patients with healthcare providers (e.g., nurses, blood banks, labs) efficiently and securely.

---

## 🚀 Features

- 🧑‍⚕️ Multi-provider architecture (Nurses, Labs, Blood Banks)
- 🧾 Advanced Order & Transaction management
- 💳 Flexible Payment System (Cash, Credit Card, Wallet via Paymob)
- 📱 FCM Push Notifications (Firebase Integration)
- ⭐ Ratings & Favorites for providers
- 🔐 Secure multi-authentication for Patients and Providers
- 🎯 Strategy Design Pattern for Payment Flow
- 🌐 RESTful API with clean structure & DTOs
- 🗂️ Soft deletes, polymorphic relationships, and full activity logging

---

## 📦 Tech Stack

- **Backend:** Laravel 11
- **Auth:** Laravel Sanctum
- **Database:** MySQL
- **Notifications:** Firebase Cloud Messaging (FCM)
- **Payments:** Paymob Gateway Integration
- **Pattern Used:** Strategy Pattern, Service Layers, Morph Relations
- **Queue System:** Laravel Queues with Jobs
- **File Storage:** Spatie MediaLibrary

---

## 🔧 Setup Instructions

```bash
git clone https://github.com/your-username/nursing-platform.git
cd nursing-platform

composer install
cp .env.example .env
php artisan key:generate

# Set your DB, Firebase, and Paymob credentials in .env

php artisan migrate --seed
php artisan storage:link
php artisan serve
