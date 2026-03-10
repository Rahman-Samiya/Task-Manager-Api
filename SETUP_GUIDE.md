# Setup & Installation Guide

## Smart Task Manager API - Complete Implementation

This guide provides detailed installation and setup instructions for the Smart Task Manager API.

---

## System Requirements

- **PHP**: 8.2 or higher
- **MySQL**: 5.7 or higher (8.0 recommended)
- **Node.js**: 18+ (for frontend development)
- **Composer**: Latest version

---

## Installation Steps

### Step 1: Clone or Setup Repository

```bash
cd your-project-directory
```

### Step 2: Install PHP Dependencies

```bash
composer install
```

### Step 3: Environment Configuration

#### Copy environment file
```bash
cp .env.example .env
```

#### Generate application key
```bash
php artisan key:generate
```

#### Update database credentials in `.env`
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_manager
DB_USERNAME=root
DB_PASSWORD=your_password
```

#### Optional: Configure token expiration (in `.env`)
```env
SANCTUM_TOKEN_PREFIX=your_prefix
```

### Step 4: Run Database Migrations

```bash
php artisan migrate
```

This will create the following tables:
- `users` - User accounts
- `tasks` - Task records
- `personal_access_tokens` - API tokens (Sanctum)

### Step 5: Start Development Server

```bash
php artisan serve
```

The API will be available at: `http://localhost:8000`

---

## File Structure

```
task-manager-api/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       ├── Auth/
│   │   │       │   └── AuthController.php
│   │   │       └── TaskController.php
│   │   ├── Requests/
│   │   │   ├── Auth/
│   │   │   │   ├── RegisterRequest.php
│   │   │   │   └── LoginRequest.php
│   │   │   └── Task/
│   │   │       ├── StoreTaskRequest.php
│   │   │       └── UpdateTaskRequest.php
│   │   └── Resources/
│   │       ├── UserResource.php
│   │       └── TaskResource.php
│   ├── Models/
│   │   ├── User.php
│   │   └── Task.php
│   ├── Services/
│   │   ├── AuthService.php
│   │   └── TaskService.php
│   └── Traits/
│       └── ApiResponse.php
├── config/
│   ├── cors.php
│   └── sanctum.php
├── database/
│   ├── migrations/
│   │   └── 2024_02_28_000001_create_tasks_table.php
│   └── factories/
├── routes/
│   └── api.php
├── bootstrap/
│   └── app.php
├── API_DOCUMENTATION.md
├── SETUP_GUIDE.md
└── API_CLIENT_EXAMPLE.js
```

---

## Key Features Implemented

### Authentication & Security
✅ User registration with validation
✅ Login with API token generation
✅ Logout with token revocation
✅ Multi-device logout capability
✅ Password hashing using Laravel's Hash facade
✅ CORS configured for Next.js frontend (localhost:3000)
✅ Sanctum token-based authentication
✅ Protected routes using auth:sanctum middleware

### Task Management
✅ Full CRUD operations (Create, Read, Update, Delete)
✅ Task fields:
   - id (Primary Key)
   - user_id (Foreign Key with CASCADE delete)
   - title (Required, max 255 characters)
   - description (Nullable)
   - priority (Enum: low, medium, high; default: medium)
   - is_completed (Boolean; default: false)
   - deadline (Nullable date)
   - timestamps (created_at, updated_at)

### Data Validation
✅ Form Request classes for validation
✅ Custom error messages
✅ Request data sanitization

### API Response Format
✅ Standardized JSON responses: {status, message, data}
✅ Appropriate HTTP status codes
✅ API Resources for response formatting
✅ Consistent error handling

### Advanced Filtering
✅ Get all tasks with pagination
✅ Get completed tasks
✅ Get pending tasks
✅ Get overdue tasks
✅ Filter tasks by priority (low, medium, high)
✅ Toggle task completion status

### Dashboard Analytics
✅ Total tasks count
✅ Completed tasks count
✅ Pending tasks count
✅ Overdue tasks count

### Code Architecture
✅ Controllers with dependency injection
✅ Service classes for business logic
✅ Clean route organization
✅ Separation of concerns
✅ Trait for common response formatting
✅ Proper database indexing

---

## API Endpoints Summary

### Authentication
- `POST /api/v1/auth/register` - Register new user
- `POST /api/v1/auth/login` - Login user
- `GET /api/v1/auth/me` - Get current user
- `POST /api/v1/auth/logout` - Logout user
- `POST /api/v1/auth/logout-all-devices` - Logout from all devices

### Tasks
- `GET /api/v1/tasks` - Get all tasks (paginated)
- `POST /api/v1/tasks` - Create task
- `GET /api/v1/tasks/{id}` - Get single task
- `PUT /api/v1/tasks/{id}` - Update task
- `DELETE /api/v1/tasks/{id}` - Delete task
- `POST /api/v1/tasks/{id}/toggle` - Toggle task completion

### Filtering & Analytics
- `GET /api/v1/tasks/filters/completed` - Get completed tasks
- `GET /api/v1/tasks/filters/pending` - Get pending tasks
- `GET /api/v1/tasks/filters/overdue` - Get overdue tasks
- `GET /api/v1/tasks/filters/by-priority?priority=high` - Get by priority
- `GET /api/v1/tasks/dashboard/stats` - Get dashboard statistics

### Health Check
- `GET /api/health` - API health check

---

## Testing with cURL

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

### 2. Login
```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "SecurePassword123!"
  }'
```

### 3. Create Task (save token from login response)
```bash
curl -X POST http://localhost:8000/api/v1/tasks \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Complete project report",
    "description": "Finish quarterly report",
    "priority": "high",
    "deadline": "2024-03-15"
  }'
```

### 4. Get All Tasks
```bash
curl -X GET http://localhost:8000/api/v1/tasks \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json"
```

### 5. Update Task
```bash
curl -X PUT http://localhost:8000/api/v1/tasks/1 \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "priority": "medium",
    "is_completed": true
  }'
```

---

## Using the JavaScript Client

See `API_CLIENT_EXAMPLE.js` for a complete JavaScript/Next.js integration example.

### Quick Start
```javascript
import api from './API_CLIENT_EXAMPLE';

// Register
const regResult = await api.register(
  'John Doe',
  'john@example.com',
  'SecurePassword123!',
  'SecurePassword123!'
);

// Login
const loginResult = await api.login('john@example.com', 'SecurePassword123!');

// Create task
const taskResult = await api.createTask(
  'My Task',
  'Description',
  'high',
  '2024-03-15'
);

// Get dashboard stats
const statsResult = await api.getDashboardStats();

// Logout
await api.logout();
```

---

## Frontend Integration (Next.js)

### Environment Configuration
Add to `.env.local`:
```
NEXT_PUBLIC_API_URL=http://localhost:8000/api/v1
NEXT_PUBLIC_API_HEALTH=http://localhost:8000/api/health
```

### Example React Hook
```typescript
'use client';

import { useState, useEffect } from 'react';
import api from '@/lib/api-client';

export function useAuth() {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchUser = async () => {
      const result = await api.getMe();
      if (result.status) {
        setUser(result.data);
      }
      setLoading(false);
    };

    if (api.token) {
      fetchUser();
    } else {
      setLoading(false);
    }
  }, []);

  return { user, loading };
}
```

---

## Database Schema

### Users Table
```sql
CREATE TABLE users (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,
  email_verified_at TIMESTAMP NULL,
  password VARCHAR(255) NOT NULL,
  remember_token VARCHAR(100) NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  INDEX (email),
  INDEX (created_at)
);
```

### Tasks Table
```sql
CREATE TABLE tasks (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  title VARCHAR(255) NOT NULL,
  description TEXT NULL,
  priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
  is_completed BOOLEAN DEFAULT FALSE,
  deadline DATE NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX (user_id),
  INDEX (is_completed),
  INDEX (deadline),
  INDEX (priority),
  INDEX (created_at)
);
```

---

## Security Considerations

1. **Never commit tokens to version control**
   - Use environment variables
   - Store tokens securely on client

2. **Use HTTPS in production**
   - Configure SSL/TLS certificates
   - Update CORS allowed origins

3. **Implement rate limiting**
   - Add Laravel rate limiting middleware
   - Protect login/register endpoints

4. **Token expiration**
   - Configure SANCTUM_EXPIRATION if needed
   - Implement refresh token mechanism

5. **Input validation**
   - All endpoints validate input
   - Use prepared statements for database queries

6. **CORS configuration**
   - Currently allows localhost:3000
   - Update in production with actual domain

---

## Troubleshooting

### 1. "SQLSTATE[HY000]: General error"
- Run `php artisan config:clear`
- Run migrations again: `php artisan migrate`

### 2. "Unauthorized" on protected routes
- Ensure `Authorization: Bearer TOKEN` header is sent
- Check token is valid and not expired

### 3. CORS issues
- Verify Next.js frontend is on localhost:3000
- Check config/cors.php allowed origins

### 4. Database connection error
- Verify database credentials in `.env`
- Ensure MySQL is running
- Check database exists: `CREATE DATABASE task_manager;`

---

## Performance Optimization

### Database Indexing
All frequently queried columns are indexed:
- `users.email` - Used for login queries
- `tasks.user_id` - Used for filtering user tasks
- `tasks.is_completed` - Used for filtering completion status
- `tasks.deadline` - Used for deadline sorting
- `tasks.priority` - Used for priority filtering
- `tasks.created_at` - Used for sorting

### Pagination
- Default pagination: 15 items per page
- Customizable via `per_page` query parameter
- Uses simple pagination for better performance

### Query Optimization
- Eager loaded relationships where applicable
- Indexed columns used in WHERE clauses
- Optimized queries in service layer

---

## Future Enhancement Roadmap

- [ ] Role-based access control (RBAC)
- [ ] Task sharing and collaboration
- [ ] Real-time notifications via WebSocket
- [ ] Advanced task analytics and reports
- [ ] File attachments for tasks
- [ ] Calendar integration
- [ ] Mobile app support
- [ ] Two-factor authentication (2FA)
- [ ] Task templates and automation
- [ ] Integration with third-party services

---

## Support

For detailed API documentation, see `API_DOCUMENTATION.md`

For JavaScript/Next.js client example, see `API_CLIENT_EXAMPLE.js`

---

## License

MIT License - Feel free to use this project for learning and development.
