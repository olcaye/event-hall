# 🎟Event Hall - Booking System

This is a Laravel 12 application for managing events and bookings, built as part of a recruitment assignment. It includes event creation, booking management, attendee listings, queue-based email notifications, and more.

---

## 🚀 Setup Instructions (Using Laravel Sail - Docker)

### ✅ Requirements

- [Docker Desktop](https://www.docker.com/products/docker-desktop)
- [Composer](https://getcomposer.org)

---

### 📦 Installation Steps

#### 1. Clone the repository
```
git clone https://github.com/your-username/event-hall.git
cd event-hall
```

#### 2. Install PHP dependencies
```
composer install
```

#### 3. Create `.env` file
```
cp .env.example .env
php artisan key:generate
```

#### 4. Start Laravel Sail (Docker containers)
```
./vendor/bin/sail up -d
```

> The first time may take a few minutes to pull and build Docker images.

#### 5. Run database migrations and seeders
```
./vendor/bin/sail artisan migrate --seed
```

#### 6. (Optional) Compile frontend assets
```
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

#### 7. Access the app

Open in your browser:

- http://localhost
- or http://booking.test (if using Laravel Valet or DNS-based resolution)

---

## 📌 Default Credentials (if applicable)

```text
Email: test@example.com
Password: password
```

---

## ⚙️ Services & Tools Used

- Laravel 12
- Laravel Sail (Docker)
- MySQL
- Mailhog (SMTP testing)
- Redis (Queue management)
- Bootstrap 5
- Dropzone.js (image upload)
- Google Maps API

---

## 📬 Email Queue System

- Booking confirmation emails are queued to be sent to both the event owner and the user.
- Reminder emails are automatically dispatched 1 day before the event to all booked users.
- To run the queue worker:
```
./vendor/bin/sail artisan queue:work
```

---

## 🗺️ Google Maps Integration

To enable map features, provide your own Google Maps API key in `.env`:

```dotenv
GOOGLE_MAPS_KEY=your_google_maps_key_here
```

---



