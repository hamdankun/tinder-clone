# 🔥 Tinder Clone - Full Stack Application

A modern **dual-platform Tinder clone** built with **Laravel 12 backend** and **React Native 0.82 mobile app**. Features real-time swipe animations, user discovery, like/dislike functionality, and administrative cronjob notifications.

---

## 📋 Table of Contents

1. [Project Overview](#-project-overview)
2. [Architecture](#️-architecture)
3. [Tech Stack](#-tech-stack)
4. [Prerequisites](#-prerequisites)
5. [Backend Setup](#-backend-setup)
6. [Mobile App Setup](#-mobile-app-setup)
7. [Running the Application](#-running-the-application)
8. [Important: Local Development & ngrok Tunnel](#️-important-local-development--ngrok-tunnel)
9. [API Documentation](#-api-documentation)
10. [Database & Seeding](#️-database--seeding)
11. [Cronjob Configuration](#-cronjob-configuration)
12. [Project Structure](#-project-structure)
13. [Troubleshooting](#-troubleshooting)
14. [Development Workflows](#-development-workflows)

---

## 🎯 Project Overview

This Tinder clone provides a complete dating platform with:

### Core Features

✅ **User Authentication** - Session-based auth with registration and login  
✅ **User Profiles** - Profile pictures, location, age, and bio  
✅ **Discovery** - Infinite scroll card-based interface for discovering people  
✅ **Like/Dislike** - Swipe right to like, left to dislike  
✅ **Matches** - View users who have liked you  
✅ **Swipe Animations** - Real-time action bar animations triggered by swipes  
✅ **Admin Notifications** - Daily cronjob for high-like-count email alerts

### Key Technologies

- **Backend**: Laravel 12, PHP 8.2+, SQLite (dev), PostgreSQL (prod)
- **Frontend**: React Native 0.82, TypeScript, React Navigation
- **State Management**: Jotai (atoms), React Query (server state)
- **Animations**: React Native Reanimated with spring physics
- **Styling**: React Native StyleSheet with custom design system
- **API**: RESTful with Swagger/OpenAPI documentation
- **Scheduling**: Laravel task scheduler with cronjobs

---

## 🏗️ Architecture

```
┌─────────────────────────────────────────┐
│      React Native Mobile App            │
│  (iOS/Android via Expo/React Native)    │
│  - Discovery Screen (Swipeable Cards)   │
│  - Liked People Screen (Matches)        │
│  - Auth Screens (Login/Register)        │
└─────────────────┬───────────────────────┘
                  │
                  │ HTTP/REST API
                  │ (Axios Client)
                  ↓
┌─────────────────────────────────────────┐
│    Laravel 12 RESTful Backend API       │
│  (/api/v1/*)                            │
│  - Authentication (POST /auth/login)    │
│  - Discovery (GET /people)              │
│  - Likes (POST /people/:id/like)        │
│  - Liked By (GET /likes/received)       │
│  - User (GET /me, PUT /me)              │
└─────────────────┬───────────────────────┘
                  │
                  ↓
┌─────────────────────────────────────────┐
│    SQLite Database (Development)        │
│    - Users table                        │
│    - Likes table                        │
│    - Pictures table                     │
│    - Sessions table                     │
│    - Cache table (duplicate prevention) │
└─────────────────────────────────────────┘
```

---

## 💻 Tech Stack

### Backend Stack

| Layer             | Technology                       | Version  |
| ----------------- | -------------------------------- | -------- |
| Framework         | Laravel                          | 12.x     |
| Language          | PHP                              | 8.2+     |
| Database          | SQLite (dev) / PostgreSQL (prod) | Latest   |
| ORM               | Eloquent                         | Built-in |
| API Documentation | Swagger/L5-Swagger               | 9.0      |
| Authentication    | Sanctum + Sessions               | Built-in |
| Task Scheduling   | Laravel Scheduler                | Built-in |

### Frontend Stack

| Layer            | Technology       | Version |
| ---------------- | ---------------- | ------- |
| Framework        | React Native     | 0.82.1  |
| Language         | TypeScript       | 5.8.3   |
| Navigation       | React Navigation | 7.x     |
| State Management | Jotai            | 2.15.1  |
| Server State     | React Query      | 5.90.10 |
| Animations       | Reanimated       | 4.1.5   |
| HTTP Client      | Axios            | 1.13.2  |
| Icons            | Lucide Icons     | 12.4.0  |

---

## 📦 Prerequisites

### System Requirements

- **macOS/Linux/Windows** (with WSL2 for Windows)
- **Node.js** ≥ 20 (for mobile app and asset build)
- **PHP** ≥ 8.2 (for Laravel backend)
- **Composer** ≥ 2.0 (for PHP dependencies)
- **Git** (for version control)

### Optional Tools

- **Docker & Docker Compose** (recommended for isolated environment)
- **ngrok** (for mobile app to access local API - see important section below)
- **Android Studio** (for Android emulator)
- **Xcode** (for iOS simulator on macOS)
- **Watchman** (for React Native file watching on macOS)

### Installation

**macOS:**

```bash
# Using Homebrew
brew install node@20 php@8.2 composer

# Optional tools
brew install watchman docker ngrok
```

**Linux (Ubuntu/Debian):**

```bash
# Node.js
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt-get install -y nodejs

# PHP and Composer
sudo apt-get install -y php8.2 php8.2-sqlite php8.2-dom composer

# Optional
sudo apt-get install -y docker.io ngrok
```

**Windows (with WSL2):**
Use Ubuntu instructions above within your WSL2 environment.

---

## 🔧 Backend Setup

### Step 1: Install PHP Dependencies

```bash
cd api

# Install Composer dependencies
composer install

# Verify installation
composer --version
```

**Expected Output:**

```
Composer version 2.7.x running with PHP 8.2.x
```

### Step 2: Environment Configuration

```bash
# Copy example env file (if not already done)
cp .env.example .env

# Generate application key
php artisan key:generate
```

**Configure .env for local development:**

Edit `api/.env` and ensure these settings:

```bash
# Application
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database (SQLite is default for dev)
DB_CONNECTION=sqlite
# (Make sure database file exists: touch database/database.sqlite)

# Cache & Session
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

# Mail (for development, logs to storage)
MAIL_MAILER=log

# Admin notifications
ADMIN_EMAIL=admin@tinder-clone.local
LIKE_THRESHOLD=50
```

### Step 3: Database Setup

```bash
# Create database file (if using SQLite)
touch database/database.sqlite

# Run migrations to create tables
php artisan migrate

# Verify migration
php artisan migrate:status
```

**Expected Output:**

```
Running migrations.
2024_01_01_000000_create_users_table ............................ [✓] Done
2024_01_01_000001_create_cache_table ............................ [✓] Done
2024_01_01_000002_create_jobs_table ............................. [✓] Done
...
```

### Step 4: Seed Sample Data

```bash
# Run seeders to populate sample users, pictures, and likes
php artisan db:seed

# Or specific seeder
php artisan db:seed --class=UserSeeder
```

**Seeded Data Includes:**

- 30 test users with full profiles
- Profile pictures for each user
- Sample likes between users
- Ready-to-test discovery feed

### Step 4.5: Create Storage Link for File Access

```bash
# Create symbolic link for file uploads (pictures, documents)
php artisan storage:link
```

**What it does:**

- Creates a symbolic link from `storage/app/public` to `public/storage`
- Allows public access to uploaded files (pictures, PDFs, etc.)
- Essential for serving user-uploaded profile pictures

**Expected Output:**

```
The [public/storage] link has been connected to [storage/app/public].
```

**If link already exists:**

```bash
# Remove existing link and create new one
rm public/storage
php artisan storage:link
```

### Step 5: Generate Swagger Doc

```bash
php artisan l5-swagger:generate
```


### Step 6: Start the Backend Server

You have 3 options to run the backend server:

#### **Option A: Simple PHP Dev Server (port 8000)**

```bash
php artisan serve
```

**Output:**

```
Starting Laravel development server: http://127.0.0.1:8000
[timestamp] Ready to accept connections
```

**Best for:** Quick local development, minimal setup

#### **Option B: Using Composer Script (Recommended)**

```bash
# Runs concurrently: PHP server + queue + logs + Vite bundler
composer run dev
```

**Runs:**

- PHP dev server on :8000
- Queue worker
- Log tailer (real-time logs)
- Vite asset bundler

**Best for:** Complete development experience with all services

#### **Option C: Using Docker (Recommended for Isolated Environment)**

**Prerequisites:**

- Docker installed: `docker --version`
- Docker Compose installed: `docker-compose --version`

**Step 1: Build the Docker image**

```bash
# From project root directory
# build the docker image
docker build api -t laravel-tinder-clone

# install dependencies
docker run -it --tty -v ./api:/app -w /app laravel-tinder-clone composer install
```

**Step 2: generate app key, run migrations and seeding inside Docker**

```bash
# generate app key
docker run -it --tty -v ./api:/app -w /app laravel-tinder-clone php artisan key:generate

# Run migrations
docker run -it --tty -v ./api:/app -w /app laravel-tinder-clone php artisan migrate

# add necessary folder for laravel boot
docker run -it --tty -v ./api:/app -w /app laravel-tinder-clone mkdir -p bootstrap/cache storage/app/public storage/framework/cache storage/framework/sessions storage/framework/views storage/logs

# Seed database
docker run -it --tty -v ./api:/app -w /app laravel-tinder-clone php artisan db:seed

# Create storage link for file uploads (pictures, etc.)
docker run -it --tty -v ./api:/app -w /app laravel-tinder-clone php artisan storage:link

# generate swagger doc
docker run -it --tty -v ./api:/app -w /app laravel-tinder-clone php artisan l5-swagger:generate

# Check artisan commands
docker run -it --tty -v ./api:/app -w /app laravel-tinder-clone php artisan list
```

**Step 3: Start the containers**

**Option 1: Start without rebuilding (faster)**

```bash
docker-compose up -d
```

**Step 2: View logs**

```bash
# View all logs
docker-compose logs -f

# View specific service logs
docker-compose logs -f tinder-clone-laravel
```

**Step 5: Access the API**

```bash
# The API will be available at
curl http://localhost:8000/api/v1/health

# Or with ngrok tunnel
ngrok http 8000
```

**Docker Advantages:**

- ✅ Isolated environment (no conflicts with system PHP)
- ✅ Consistent development across team
- ✅ Easy to switch between projects
- ✅ Production-like environment
- ✅ All services in containers (PHP, Nginx, Database)
- ✅ No need to install PHP/Composer locally
- ✅ Easy onboarding for new developers

**Docker Disadvantages:**

- ⚠️ Requires Docker installation
- ⚠️ Slightly slower than native (on Mac/Windows)
- ⚠️ Requires learning Docker basics

**Best for:** Team development, production-like environment, CI/CD integration

**Verify API is running:**

```bash
# Test the health endpoint
curl http://localhost:8000/api/v1/health

# Expected response:
{"status": "healthy", "message": "API is running"}

# Or check Swagger UI
open http://localhost:8000/api/documentation
```

---

## 📱 Mobile App Setup

### Step 1: Install Node Dependencies

```bash
cd app-mobile

# Install npm packages
npm install

# Or using yarn
yarn install
```

**Expected Output:**

```
added 250+ packages in 45s
```

### Step 2: iOS-Specific Setup (macOS Only)

```bash
# Install Ruby dependencies (first time only)
bundle install

# Install iOS native dependencies
bundle exec pod install

# If pod install fails, try:
cd ios && pod repo update && pod install && cd ..
```

### Step 3: Android-Specific Setup

```bash
# Set up Android emulator via Android Studio, or
# Connect a physical Android device with USB debugging enabled

# Verify Android setup
adb devices  # Should list your emulator/device
```

### Step 4: Configure API Connection

Edit `app-mobile/src/config/constants.ts`:

```typescript
export const API_CONFIG = {
  baseURL: "<YOUR_URL_API>",
  assetURL: "<YOUR_URL_API>",
};
```

### Step 5: Verify Setup

```bash
# Lint code for issues
npm run lint

# Run TypeScript type check
npx tsc --noEmit

# Expected: No errors or warnings
```

---

## 🚀 Running the Application

### Starting Backend

**Option 1: Using Composer Script (Recommended)**

**Terminal 1 - Backend API:**

```bash
cd api

# Start development server with all services
composer run dev

# This runs concurrently:
# - PHP dev server on :8000
# - Queue worker
# - Log tailer (real-time logs)
# - Vite asset bundler
```

**Option 2: Using Simple PHP Server**

**Terminal 1 - Backend API:**

```bash
cd api
php artisan serve

# Output: Starting Laravel development server: http://127.0.0.1:8000
```

### Starting Mobile App

**Terminal 2 - Metro Bundler:**

```bash
cd app-mobile

yarn install

# Start Metro bundler
yarn start

# Output:
# ┏━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┓
# ┃ Metro Bundler ready.                          ┃
# ┃ ...                                             ┃
# ┗━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┛
```

**Terminal 3 - Run on Device:**

iOS (macOS only):

```bash
cd app-mobile
npm run ios

# Or with specific simulator
npx react-native run-ios --simulator="iPhone 15 Pro"
```

Android:

```bash
cd app-mobile
npm run android

# Requires Android emulator to be running first
```

### Verification

**Check Backend is Running:**

```bash
# Health check endpoint
curl http://localhost:8000/api/v1/health

# Expected response:
{"status": "healthy", "message": "API is running"}

# Access Swagger UI (if backend is running)
open http://localhost:8000/api/documentation
```

## ⚠️ IMPORTANT: Local Development & ngrok Tunnel

### ❌ The Problem

When developing locally, the **mobile app cannot access** `http://localhost:8000` because:

1. **Mobile emulator/device** runs in a different network context
2. **Localhost on emulator** points to the emulator's own OS, not your machine
3. **Same-network access** (192.168.x.x) only works if on same WiFi
4. **Production URLs** don't work in development

```
Your Machine (localhost:8000) ≠ Emulator Network (localhost:8000)
```

### ✅ The Solution: ngrok HTTP Tunnel

**ngrok** creates a public HTTPS tunnel to your local API, making it accessible from anywhere.

#### Setup ngrok

**Install:**

```bash
# macOS
brew install ngrok

# Or download from: https://ngrok.com/download
```

**Authenticate (One-time):**

```bash
# Get your auth token from: https://dashboard.ngrok.com/auth
ngrok config add-authtoken YOUR_AUTH_TOKEN
```

**Start Tunnel (Terminal 3):**

```bash
# Expose local API on a tunnel
ngrok http 8000

# Output:
# Session Status                online
# Account                       yourname
# Version                       3.0.0
# Region                        us-central (US)
# Forwarding                    https://abc123def456.ngrok.io -> http://localhost:8000
# Connections                   ttl    opn    dl    in    out
# [...]                         0      0      0     0     0
```

**Copy the Forwarding URL** (e.g., `https://abc123def456.ngrok.io`)

#### Update Mobile App Config

Edit `app-mobile/src/config/constants.ts`:

```typescript
// Before (won't work):
export const API_BASE_URL = "http://localhost:8000/api/v1";

// After (works!):
export const API_BASE_URL = "https://abc123def456.ngrok.io/api/v1";
```

#### Restart Mobile App

```bash
# Metro will hot reload
# Or manually reload:
# - iOS: Press R in simulator
# - Android: Press R in terminal
```

### 📋 Tunnel Setup Checklist

✅ Backend running: `php artisan serve` (port 8000)  
✅ ngrok installed: `ngrok --version`  
✅ ngrok authenticated: `ngrok config add-authtoken TOKEN`  
✅ ngrok tunnel active: `ngrok http 8000`  
✅ Copy forwarding URL to `API_BASE_URL`  
✅ Restart Metro bundler  
✅ Reload mobile app

### 🆓 ngrok Free Tier Limitations

- ✅ Public URLs (HTTPS tunnel)
- ✅ Up to 2 concurrent sessions
- ⚠️ URL changes every 2 hours (restart ngrok)
- ⚠️ Limited bandwidth

**Pro Tip:** For longer development sessions, use ngrok Pro ($5/month) for stable URLs.

---

## 📚 API Documentation

### Swagger UI

Once backend is running, access interactive API documentation:

```
http://localhost:8000/api/documentation
```

### Key Endpoints

**Authentication:**

- `POST /api/v1/auth/register` - Register new user
- `POST /api/v1/auth/login` - Login user
- `POST /api/v1/auth/logout` - Logout (requires auth)

**User Profile:**

- `GET /api/v1/me` - Get current user
- `PUT /api/v1/me` - Update user profile
- `POST /api/v1/me/pictures` - Upload pictures

**Discovery:**

- `GET /api/v1/people?page=1` - Get people to discover (paginated)
- `GET /api/v1/people/{id}` - Get single person details

**Likes:**

- `POST /api/v1/people/{id}/like` - Like a person
- `POST /api/v1/people/{id}/dislike` - Dislike a person
- `GET /api/v1/likes/received` - Get users who liked you (matches)

**Full Reference:** See `/api/routes/web.php` and Swagger UI

---

## 🗄️ Database & Seeding

### Database Schema

```sql
-- Users: Profiles, location, age
CREATE TABLE users (
  id INTEGER PRIMARY KEY,
  name TEXT NOT NULL,
  email TEXT UNIQUE NOT NULL,
  age INTEGER,
  location TEXT,
  bio TEXT,
  email_verified_at TIMESTAMP,
  password TEXT NOT NULL,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);

-- Pictures: User profile images
CREATE TABLE pictures (
  id INTEGER PRIMARY KEY,
  user_id INTEGER NOT NULL FOREIGN KEY,
  url TEXT NOT NULL,
  is_primary BOOLEAN,
  created_at TIMESTAMP
);

-- Likes: Track who liked whom
CREATE TABLE likes (
  id INTEGER PRIMARY KEY,
  user_id INTEGER NOT NULL FOREIGN KEY (from),
  liked_user_id INTEGER NOT NULL FOREIGN KEY (to),
  created_at TIMESTAMP,
  UNIQUE(user_id, liked_user_id)
);

-- Sessions: User authentication
CREATE TABLE sessions (
  id TEXT PRIMARY KEY,
  user_id INTEGER FOREIGN KEY,
  ip_address TEXT,
  user_agent TEXT,
  last_activity INTEGER
);

-- Cache: Duplicate prevention for notifications
CREATE TABLE cache (
  key TEXT PRIMARY KEY,
  value TEXT NOT NULL,
  expiration INTEGER
);
```

### Running Migrations

```bash
cd api

# Run all pending migrations
php artisan migrate

# Show status
php artisan migrate:status

# Rollback last batch
php artisan migrate:rollback

# Reset entire database
php artisan migrate:reset
```

### Seeding Data

```bash
# Seed sample data
php artisan db:seed

# Run specific seeder
php artisan db:seed --class=UserSeeder

# Refresh DB (reset + migrate + seed)
php artisan migrate:fresh --seed
```

**Seeded Data:**

- 30 test users (John, Sarah, Emily, etc.)
- Profile pictures for each user
- Cross-likes between users
- Ready-to-test discovery feed

### Database Backup

```bash
# Backup SQLite database
cp database/database.sqlite database/database.sqlite.backup

# Restore backup
cp database/database.sqlite.backup database/database.sqlite
```

---

## ⏰ Cronjob Configuration

### High Like Count Notifications

The system automatically sends daily emails to admin when users exceed 50+ likes.

#### Setup Instructions

**1. Update Environment Variables** (`api/.env`):

```bash
# Admin email to receive notifications
ADMIN_EMAIL=your-admin-email@company.com

# Like threshold that triggers notification
LIKE_THRESHOLD=50
```

**2. Configure Mail Provider**

Edit `api/.env` for your mail service:

**Option A: Mailtrap (Development)**

```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=hello@example.com
```

**Option B: SendGrid (Production)**

```bash
MAIL_MAILER=sendgrid
SENDGRID_API_KEY=your_sendgrid_api_key
MAIL_FROM_ADDRESS=noreply@yourapp.com
```

**3. Test Cronjob**

```bash
# Run command manually
php artisan likes:check-high-count

# Should output:
# Checking for users with 50+ likes...
# Found 2 users. Sending notifications...
```

**4. Deploy System Cron** (Production)

Add to your server's crontab:

```bash
# Every minute, check if Laravel scheduler needs to run
* * * * * cd /path/to/api && php artisan schedule:run >> /dev/null 2>&1
```

---

## 📂 Project Structure

```
tinder-clone/
├── api/                              # Laravel Backend
│   ├── app/
│   │   ├── Console/
│   │   │   └── Commands/
│   │   │       └── CheckHighLikeCount.php
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   │   └── Api/V1/
│   │   │   │       ├── Auth/
│   │   │   │       ├── Discovery/
│   │   │   │       └── Likes/
│   │   │   └── Middleware/
│   │   ├── Models/
│   │   │   ├── User.php
│   │   │   ├── Picture.php
│   │   │   └── Like.php
│   │   ├── Services/
│   │   ├── Repositories/
│   │   ├── Events/
│   │   ├── Listeners/
│   │   ├── Exceptions/
│   │   ├── DTOs/
│   │   └── Mail/
│   │       └── HighLikeCountNotification.php
│   ├── database/
│   │   ├── migrations/
│   │   ├── seeders/
│   │   └── factories/
│   ├── resources/
│   │   ├── views/
│   │   │   └── emails/
│   │   │       └── high-like-count.blade.php
│   │   └── css/
│   ├── routes/
│   │   └── web.php
│   ├── config/
│   │   ├── app.php
│   │   ├── database.php
│   │   └── services.php
│   ├── .env                          # Environment configuration
│   ├── .env.example                  # Example env file
│   ├── artisan                       # Laravel CLI
│   ├── composer.json                 # PHP dependencies
│   └── README.md
│
├── app-mobile/                       # React Native Mobile App
│   ├── src/
│   │   ├── config/
│   │   │   └── constants.ts          # API URL configuration
│   │   ├── atoms/
│   │   │   ├── cardStack.ts
│   │   │   └── auth.ts
│   │   ├── services/
│   │   │   ├── api.ts
│   │   │   ├── auth.service.ts
│   │   │   ├── discovery.service.ts
│   │   │   └── likes.service.ts
│   │   ├── hooks/
│   │   │   ├── usePeopleList.ts
│   │   │   ├── useLikedPeople.ts
│   │   │   ├── useLikePerson.ts
│   │   │   └── useAuth.ts
│   │   ├── components/
│   │   │   ├── atoms/
│   │   │   ├── molecules/
│   │   │   └── organisms/
│   │   ├── screens/
│   │   │   ├── DiscoveryScreen.tsx
│   │   │   ├── LikedPeopleScreen.tsx
│   │   │   ├── AuthScreen.tsx
│   │   │   └── ProfileScreen.tsx
│   │   ├── templates/
│   │   ├── App.tsx
│   │   └── Navigation.tsx
│   ├── ios/                          # iOS native code
│   ├── android/                      # Android native code
│   ├── package.json                  # JS dependencies
│   ├── tsconfig.json                 # TypeScript configuration
│   ├── babel.config.js
│   ├── metro.config.js
│   ├── jest.config.js
│   └── README.md
│
├── docker-compose.yml                # Docker setup
├── package.json                      # Root package file
├── README.md                         # This file
```

---

## 🐛 Troubleshooting

### Backend Issues

#### Laravel Server Won't Start

```bash
# Clear configuration cache
php artisan config:clear

# Check for port conflicts
lsof -i :8000

# Try different port
php artisan serve --port=8001
```

#### Database Errors

```bash
# Check database exists
ls -la database/database.sqlite

# Create if missing
touch database/database.sqlite

# Check permissions
chmod 664 database/database.sqlite
chmod 775 database/

# Reset and reseed
php artisan migrate:fresh --seed
```

#### Migrations Failed

```bash
# Check migration status
php artisan migrate:status

# Rollback and try again
php artisan migrate:rollback
php artisan migrate
```

### Mobile App Issues

#### Metro Bundler Won't Start

```bash
# Clear Metro cache
npm start -- --reset-cache

# Kill any lingering processes
lsof -i :8081
kill -9 <PID>

# Try again
npm start
```

#### Module Not Found

```bash
# Clear node_modules and reinstall
rm -rf node_modules package-lock.json
npm install

# Clear build cache
npm start -- --reset-cache
```

#### Emulator Connection Issues

```bash
# Verify emulator is running
adb devices

# For iOS, verify simulator is open
xcrun simctl list devices

# Rebuild and try again
npm run android
# or
npm run ios
```

#### API Connection Errors (404, Connection Refused)

```bash
# Check backend is running
curl http://localhost:8000/api/v1/health

# Verify ngrok tunnel is active
ngrok http 8000

# Update API_BASE_URL in constants.ts
export const API_BASE_URL = 'https://your-ngrok-url.ngrok.io/api/v1';

# Restart Metro
npm start
```

### Network Issues

#### CORS Errors

```bash
# Backend logs should show CORS issue
# Check CORS configuration in api/config/

# Verify ALLOWED_ORIGINS includes ngrok URL
ALLOWED_ORIGINS=https://abc123.ngrok.io
```

#### SSL Certificate Warnings (ngrok)

```bash
# ngrok uses valid SSL certificates
# If you see warnings, ensure latest ngrok version
ngrok update

# Or download fresh from https://ngrok.com/download
```

---

## 💡 Development Workflows

### Feature Development

**Backend (Laravel):**

1. Create migration

```bash
php artisan make:migration create_something
```

2. Create model

```bash
php artisan make:model Something
```

3. Create controller

```bash
php artisan make:controller Api/V1/SomethingController
```

4. Add route in `/api/routes/web.php`

5. Test with Swagger UI: `http://localhost:8000/api/documentation`

**Frontend (React Native):**

1. Create hook in `/src/hooks/`
2. Create component in `/src/components/`
3. Import and use in screen
4. Hot reload with Metro (save file)

### Testing Workflow

**Backend:**

```bash
cd api
php artisan test
```

**Mobile:**

```bash
cd app-mobile
npm test
```

### Debugging

**Backend:**

```bash
# Enable debug mode in .env
APP_DEBUG=true

# View logs in real-time
tail -f api/storage/logs/laravel.log

# Use Laravel Tinker
php artisan tinker
>>> User::count()
```

**Mobile:**

```bash
# View logs
npx react-native log-ios
# or
npx react-native log-android

# Use React DevTools
npm install -g react-devtools
react-devtools

# Debug with Chrome DevTools (web version)
```

---

## 📖 Additional Resources

### Documentation Files

| File                                | Purpose                    |
| ----------------------------------- | -------------------------- |
| `ARCHITECTURE.md`                   | System design and patterns |
| `CRONJOB_QUICK_START.md`            | Cronjob setup guide        |
| `CRONJOB_HIGH_LIKE_NOTIFICATION.md` | Detailed cronjob reference |
| `API_REFERENCE.md`                  | Complete API endpoint docs |
| `MOBILE_SETUP.md`                   | Detailed mobile setup      |

### External Links

- [Laravel Documentation](https://laravel.com/docs)
- [React Native Documentation](https://reactnative.dev/docs)
- [React Navigation](https://reactnavigation.org/)
- [React Query Documentation](https://tanstack.com/query/latest)
- [ngrok Documentation](https://ngrok.com/docs)

---

## 🤝 Contributing

1. Create feature branch: `git checkout -b feature/amazing-feature`
2. Commit changes: `git commit -m 'Add amazing feature'`
3. Push to branch: `git push origin feature/amazing-feature`
4. Open Pull Request

---

## 📝 License

This project is open source and available under the MIT License.

---

## 🆘 Getting Help

1. **Check Troubleshooting section** above
2. **Review documentation files** in project root
3. **Check API logs**: `tail -f api/storage/logs/laravel.log`
4. **Check mobile logs**: Metro terminal or `react-native log-*`
5. **Review Swagger UI**: `http://localhost:8000/api/documentation`

---

**Last Updated:** November 22, 2025  
**Version:** 1.0  
**Status:** Production Ready ✅
