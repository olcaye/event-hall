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
### Developer Panel

This project includes a hidden developer panel to assist with testing and debugging.  
It is only available when the environment is set to `developer`.

http://localhost:8080/developer/panel

Also can be accessable via navbar.

#### Access

Once the application is running with `APP_ENV=developer`, visit:

#### Available Actions

- **Refresh Migrations**  
  Drops all tables and re-runs all migrations using `php artisan migrate:refresh`.

- **Refresh Migrations & Seed**  
  Same as above, but also re-runs all seeders using `php artisan migrate:refresh --seed`.

- **Run Seeders**  
  Executes the application's seeders manually with `php artisan db:seed`.

- **Clear Sessions**  
  Empties the session table using Laravel’s session driver.

- **Clear Cache**  
  Runs `php artisan optimize:clear` to clear config, route, view, and event caches.

- **Login as User**  
  Allows the developer to instantly authenticate as any seeded user.  
  This will log out the current user and log in as the selected one.

#### Security

This panel is **only accessible** when the following condition is met in your `.env` file:

```env
APP_ENV=developer
```
---

## Google Maps Integration

To enable map features, provide your own Google Maps API key in `.env`:

```dotenv
GOOGLE_MAPS_KEY=your_google_maps_key_here
```

---



