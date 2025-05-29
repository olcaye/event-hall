# Event Hall - Event Booking System

This is a Laravel 12 application for managing events and bookings, built as part of a recruitment assignment. It includes event creation, booking management, attendee listings and more.

---

## Setup Instructions (Using Laravel Sail - Docker)

### Requirements

- [Docker Desktop](https://www.docker.com/products/docker-desktop)
- [Composer](https://getcomposer.org)

---

### Installation Steps

#### 1. Clone the repository
```
git clone https://github.com/olcaye/event-hall.git
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

#### 5. Run database migrations and seeders
```
./vendor/bin/sail artisan migrate --seed
```

#### 6. Compile frontend assets
```
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

#### 7. Access the app

Open in your browser:

- http://localhost:8080

---

## Default Credentials (if applicable)

```text
Email: test@example.com
Password: password
```

---

## Google Maps Integration

To enable map features, provide your own Google Maps API key in `.env`:

```dotenv
GOOGLE_MAPS_KEY=your_google_maps_key_here
```

---



