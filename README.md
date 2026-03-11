# Smart Task Manager API

> A production-ready RESTful API for task management with user authentication, advanced filtering, and comprehensive documentation.

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat-square&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat-square&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=flat-square&logo=mysql)
![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)

## 🎯 Overview

Smart Task Manager API is a fully-featured, production-ready REST API built with Laravel 12 that provides:

- **Secure Authentication** - Token-based authentication with Laravel Sanctum
- **Complete Task Management** - Full CRUD operations for tasks
- **Advanced Filtering** - Filter by status, priority, deadline
- **Dashboard Analytics** - Real-time statistics for task overview
- **Clean Architecture** - Service layer, Form Requests, Resources
- **Comprehensive Documentation** - API docs, setup guide, JavaScript client
- **Database Optimization** - Proper indexing and relationship management
- **CORS Ready** - Configured for Next.js frontend on localhost:3000

## ✨ Key Features

### Authentication & Security
- User registration with email validation
- Secure login with API token generation
- Logout with token revocation
- Multi-device logout capability
- Password hashing with bcrypt (12 rounds)
- Protected routes with Sanctum middleware

### Task Management
- Create, read, update, delete tasks
- Toggle task completion status
- Filter by status, priority, deadline
- Dashboard statistics
- Pagination support

## 🚀 Quick Start

```bash
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

**See [QUICK_START.md](QUICK_START.md) for detailed setup.**

## 📚 Documentation

- [QUICK_START.md](QUICK_START.md) - 5-minute setup
- [API_DOCUMENTATION.md](API_DOCUMENTATION.md) - Complete API reference
- [SETUP_GUIDE.md](SETUP_GUIDE.md) - Installation & integration
- [REQUIREMENTS.md](REQUIREMENTS.md) - Project requirements
- [API_CLIENT_EXAMPLE.js](API_CLIENT_EXAMPLE.js) - JavaScript client

## 🔌 API Endpoints (19 total)

**Authentication**: register, login, me, logout, logout-all-devices
**Tasks**: CRUD operations, toggle, filters, statistics
**Health**: API health check

## 📊 Database

- Users table with Sanctum authentication
- Tasks table with user relationship
- Proper indexing for performance

## 🔐 Security

- Token-based authentication (Sanctum)
- Password hashing (bcrypt)
- Request validation
- CORS configuration
- User authorization checks


