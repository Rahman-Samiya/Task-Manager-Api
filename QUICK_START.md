# Quick Start Guide

## Smart Task Manager API - Get Started in 5 Minutes

### ⚡ Quick Setup

```bash
# 1. Copy environment file
cp .env.example .env

# 2. Generate app key
php artisan key:generate

# 3. Configure database in .env
# DB_DATABASE=task_manager
# DB_USERNAME=root

# 4. Run migrations
php artisan migrate

# 5. Start server
php artisan serve
```

API is now running at: `http://localhost:8000/api`

---

## 📋 Test the API

### 1. Register User
```bash
curl -X POST http://localhost:8000/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "SecurePassword123!",
    "password_confirmation": "SecurePassword123!"
  }'
```

**Save the token from response!**

### 2. Create a Task
Replace `YOUR_TOKEN` with the token from registration:

```bash
curl -X POST http://localhost:8000/api/v1/tasks \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Complete project report",
    "description": "Finish quarterly report",
    "priority": "high",
    "deadline": "2024-03-15"
  }'
```

### 3. Get All Tasks
```bash
curl -X GET http://localhost:8000/api/v1/tasks \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json"
```

### 4. Get Dashboard Stats
```bash
curl -X GET http://localhost:8000/api/v1/tasks/dashboard/stats \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json"
```

---

## 🔗 Key Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/v1/auth/register` | Register user |
| POST | `/api/v1/auth/login` | Login user |
| GET | `/api/v1/auth/me` | Get current user |
| POST | `/api/v1/auth/logout` | Logout user |
| GET | `/api/v1/tasks` | Get all tasks |
| POST | `/api/v1/tasks` | Create task |
| PUT | `/api/v1/tasks/{id}` | Update task |
| DELETE | `/api/v1/tasks/{id}` | Delete task |
| POST | `/api/v1/tasks/{id}/toggle` | Toggle completion |
| GET | `/api/v1/tasks/filters/completed` | Completed tasks |
| GET | `/api/v1/tasks/filters/pending` | Pending tasks |
| GET | `/api/v1/tasks/dashboard/stats` | Statistics |

---

## 📚 Documentation

- **API_DOCUMENTATION.md** - Full API reference (100+ KB)
- **SETUP_GUIDE.md** - Complete setup and integration guide
- **REQUIREMENTS.md** - Project requirements checklist
- **API_CLIENT_EXAMPLE.js** - JavaScript client library

---

## 🎯 Architecture Overview

```
Request → Route → Controller → Service → Model → Database
   ↓        ↓        ↓         ↓       ↓
  HTTP   routes/   Controllers  Business  Database
 method   api.php   auth/tasks   Logic    Schema

          ↓ Response

       Resource → JSON Response
       (formatting) {status, message, data}
```

---

## 🔑 Authentication

All protected endpoints require:
```
Authorization: Bearer YOUR_API_TOKEN
Content-Type: application/json
```

Token format: `1|abcdef123456789...`

---

## 📁 Project Files Created

```
Controllers/
├── Api/
│   ├── Auth/AuthController.php (89 lines)
│   └── TaskController.php (195 lines)

Requests/
├── Auth/
│   ├── RegisterRequest.php (35 lines)
│   └── LoginRequest.php (30 lines)
└── Task/
    ├── StoreTaskRequest.php (40 lines)
    └── UpdateTaskRequest.php (40 lines)

Resources/
├── UserResource.php (20 lines)
└── TaskResource.php (25 lines)

Models/
├── User.php (60 lines)
└── Task.php (45 lines)

Services/
├── AuthService.php (68 lines)
└── TaskService.php (140 lines)

Traits/
└── ApiResponse.php (45 lines)

Routes/
└── api.php (58 lines)

Config/
├── cors.php (20 lines)
└── bootstrap/app.php (updated)

Migrations/
└── 2024_02_28_000001_create_tasks_table.php

Documentation/
├── API_DOCUMENTATION.md (1000+ lines)
├── SETUP_GUIDE.md (400+ lines)
├── REQUIREMENTS.md (300+ lines)
├── API_CLIENT_EXAMPLE.js (350+ lines)
└── QUICK_START.md (this file)

Total LOC: ~2,500+ lines of production-ready code
```

---

## 🚀 Next Steps

1. **Integrate with Frontend**
   - Use `API_CLIENT_EXAMPLE.js`
   - Configure CORS for your domain
   - Add your frontend URL to `config/cors.php`

2. **Add Features**
   - Implement tests
   - Add rate limiting
   - Setup logging
   - Add caching

3. **Deploy**
   - Setup production database
   - Configure SSL/TLS
   - Set environment variables
   - Run migrations on production

---

## 🐛 Troubleshooting

**API not responding?**
```bash
# Check API health
curl http://localhost:8000/api/health
```

**Database error?**
```bash
# Check migrations
php artisan migrate:status

# Run migrations
php artisan migrate
```

**Token invalid?**
- Ensure `Authorization: Bearer TOKEN` header is present
- Check token is not expired
- Verify token is from `/auth/login` or `/auth/register`

---

## 💡 Tips

1. **Use Postman** - Import the API collection for easy testing
2. **Save tokens** - Store token from response to test other endpoints
3. **Check logs** - `storage/logs/laravel.log` for debugging
4. **Read docs** - See `API_DOCUMENTATION.md` for all details

---

## 📞 Support

- Full API documentation: `API_DOCUMENTATION.md`
- Setup guide: `SETUP_GUIDE.md`
- JavaScript client: `API_CLIENT_EXAMPLE.js`
- Requirements: `REQUIREMENTS.md`

---

**Happy coding! 🎉**
