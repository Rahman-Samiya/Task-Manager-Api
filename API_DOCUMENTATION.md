# Smart Task Manager API - Complete Documentation

## Overview

This is a production-ready RESTful API for a Smart Task Manager built with Laravel 12, MySQL, and Laravel Sanctum for token-based authentication. The API provides complete CRUD operations for user authentication and task management with advanced filtering, pagination, and dashboard statistics.

## Technical Stack

- **Framework**: Laravel 12 (Latest Stable)
- **Database**: MySQL
- **Authentication**: Laravel Sanctum (Token-based)
- **API Format**: REST with JSON responses
- **Validation**: Form Request classes
- **Architecture**: Clean Architecture with Service classes
- **CORS**: Configured for Next.js frontend (localhost:3000)

## Architecture Overview

```
routes/api.php                 # API Route definitions
│
├── app/Http/Controllers/Api/
│   ├── Auth/AuthController   # Authentication endpoints
│   └── TaskController        # Task CRUD endpoints
│
├── app/Http/Requests/        # Form validation
│   ├── Auth/
│   │   ├── RegisterRequest
│   │   └── LoginRequest
│   └── Task/
│       ├── StoreTaskRequest
│       └── UpdateTaskRequest
│
├── app/Http/Resources/       # Response formatting
│   ├── UserResource
│   └── TaskResource
│
├── app/Services/             # Business logic
│   ├── AuthService
│   └── TaskService
│
├── app/Models/
│   ├── User                  # User model with Sanctum
│   └── Task                  # Task model
│
└── app/Traits/
    └── ApiResponse           # Standardized response formatting
```

## Standard Response Format

All API responses follow this structure:

```json
{
  "status": true,
  "message": "Success message here",
  "data": {}
}
```

### Success Response (HTTP 200)
```json
{
  "status": true,
  "message": "User logged in successfully",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "created_at": "2024-02-28T10:30:00.000Z",
      "updated_at": "2024-02-28T10:30:00.000Z"
    },
    "token": "1|abcdef123456..."
  }
}
```

### Error Response (HTTP 400/401/422)
```json
{
  "status": false,
  "message": "Error message here",
  "data": null
}
```

### Validation Error Response (HTTP 422)
```json
{
  "status": false,
  "message": "Validation failed",
  "data": {
    "email": ["Email already exists"],
    "password": ["Password must be at least 8 characters"]
  }
}
```

---

## Authentication Endpoints

### 1. User Registration

**Endpoint**: `POST /api/v1/auth/register`

**Description**: Register a new user account

**Request Headers**:
```
Content-Type: application/json
```

**Request Body**:
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "SecurePassword123!",
  "password_confirmation": "SecurePassword123!"
}
```

**Success Response** (HTTP 201):
```json
{
  "status": true,
  "message": "User registered successfully",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "email_verified_at": null,
      "created_at": "2024-02-28T10:30:00.000Z",
      "updated_at": "2024-02-28T10:30:00.000Z"
    },
    "token": "1|abcdef123456789..."
  }
}
```

**Validation Rules**:
- `name`: Required, string, max 255 characters
- `email`: Required, valid email, unique in database
- `password`: Required, confirmed, minimum 8 characters, must contain uppercase, lowercase, number, and special character

**Example CURL**:
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

---

### 2. User Login

**Endpoint**: `POST /api/v1/auth/login`

**Description**: Authenticate user and receive API token

**Request Headers**:
```
Content-Type: application/json
```

**Request Body**:
```json
{
  "email": "john@example.com",
  "password": "SecurePassword123!"
}
```

**Success Response** (HTTP 200):
```json
{
  "status": true,
  "message": "User logged in successfully",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "email_verified_at": null,
      "created_at": "2024-02-28T10:30:00.000Z",
      "updated_at": "2024-02-28T10:30:00.000Z"
    },
    "token": "1|abcdef123456789..."
  }
}
```

**Error Response** (HTTP 401):
```json
{
  "status": false,
  "message": "Invalid credentials",
  "data": null
}
```

**Example CURL**:
```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "SecurePassword123!"
  }'
```

---

### 3. Get Current User

**Endpoint**: `GET /api/v1/auth/me`

**Description**: Retrieve authenticated user details

**Request Headers**:
```
Authorization: Bearer YOUR_API_TOKEN
Content-Type: application/json
```

**Success Response** (HTTP 200):
```json
{
  "status": true,
  "message": "User details retrieved successfully",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "email_verified_at": null,
    "created_at": "2024-02-28T10:30:00.000Z",
    "updated_at": "2024-02-28T10:30:00.000Z"
  }
}
```

**Example CURL**:
```bash
curl -X GET http://localhost:8000/api/v1/auth/me \
  -H "Authorization: Bearer 1|abcdef123456789..." \
  -H "Content-Type: application/json"
```

---

### 4. User Logout

**Endpoint**: `POST /api/v1/auth/logout`

**Description**: Logout user and revoke current token

**Request Headers**:
```
Authorization: Bearer YOUR_API_TOKEN
Content-Type: application/json
```

**Success Response** (HTTP 200):
```json
{
  "status": true,
  "message": "User logged out successfully",
  "data": null
}
```

**Example CURL**:
```bash
curl -X POST http://localhost:8000/api/v1/auth/logout \
  -H "Authorization: Bearer 1|abcdef123456789..." \
  -H "Content-Type: application/json"
```

---

### 5. Logout from All Devices

**Endpoint**: `POST /api/v1/auth/logout-all-devices`

**Description**: Revoke all tokens for the user across all devices

**Request Headers**:
```
Authorization: Bearer YOUR_API_TOKEN
Content-Type: application/json
```

**Success Response** (HTTP 200):
```json
{
  "status": true,
  "message": "Logged out from all devices",
  "data": null
}
```

**Example CURL**:
```bash
curl -X POST http://localhost:8000/api/v1/auth/logout-all-devices \
  -H "Authorization: Bearer 1|abcdef123456789..." \
  -H "Content-Type: application/json"
```

---

## Task Management Endpoints

> **Note**: All task endpoints require authentication. Include the `Authorization: Bearer YOUR_API_TOKEN` header in all requests.

### 1. Get All Tasks (Paginated)

**Endpoint**: `GET /api/v1/tasks`

**Description**: Retrieve paginated list of user's tasks

**Query Parameters**:
- `per_page` (optional): Items per page (default: 15)

**Request Headers**:
```
Authorization: Bearer YOUR_API_TOKEN
Content-Type: application/json
```

**Success Response** (HTTP 200):
```json
{
  "status": true,
  "message": "Tasks retrieved successfully",
  "data": {
    "tasks": [
      {
        "id": 1,
        "user_id": 1,
        "title": "Complete project report",
        "description": "Finish the quarterly project report",
        "priority": "high",
        "is_completed": false,
        "deadline": "2024-03-15",
        "created_at": "2024-02-28T10:30:00.000Z",
        "updated_at": "2024-02-28T10:30:00.000Z"
      }
    ],
    "pagination": {
      "total": 10,
      "per_page": 15,
      "current_page": 1,
      "next_page_url": null,
      "prev_page_url": null
    }
  }
}
```

**Example CURL**:
```bash
curl -X GET "http://localhost:8000/api/v1/tasks?per_page=20" \
  -H "Authorization: Bearer 1|abcdef123456789..." \
  -H "Content-Type: application/json"
```

---

### 2. Create New Task

**Endpoint**: `POST /api/v1/tasks`

**Description**: Create a new task for the authenticated user

**Request Headers**:
```
Authorization: Bearer YOUR_API_TOKEN
Content-Type: application/json
```

**Request Body**:
```json
{
  "title": "Complete project report",
  "description": "Finish the quarterly project report with all metrics",
  "priority": "high",
  "deadline": "2024-03-15"
}
```

**Success Response** (HTTP 201):
```json
{
  "status": true,
  "message": "Task created successfully",
  "data": {
    "id": 1,
    "user_id": 1,
    "title": "Complete project report",
    "description": "Finish the quarterly project report with all metrics",
    "priority": "high",
    "is_completed": false,
    "deadline": "2024-03-15",
    "created_at": "2024-02-28T10:30:00.000Z",
    "updated_at": "2024-02-28T10:30:00.000Z"
  }
}
```

**Validation Rules**:
- `title`: Required, string, max 255 characters
- `description`: Optional, string
- `priority`: Optional, one of: low, medium, high (default: medium)
- `deadline`: Optional, valid date, must be in future

**Example CURL**:
```bash
curl -X POST http://localhost:8000/api/v1/tasks \
  -H "Authorization: Bearer 1|abcdef123456789..." \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Complete project report",
    "description": "Finish the quarterly project report",
    "priority": "high",
    "deadline": "2024-03-15"
  }'
```

---

### 3. Get Single Task

**Endpoint**: `GET /api/v1/tasks/{task_id}`

**Description**: Retrieve details of a specific task

**Request Headers**:
```
Authorization: Bearer YOUR_API_TOKEN
Content-Type: application/json
```

**Success Response** (HTTP 200):
```json
{
  "status": true,
  "message": "Task retrieved successfully",
  "data": {
    "id": 1,
    "user_id": 1,
    "title": "Complete project report",
    "description": "Finish the quarterly project report",
    "priority": "high",
    "is_completed": false,
    "deadline": "2024-03-15",
    "created_at": "2024-02-28T10:30:00.000Z",
    "updated_at": "2024-02-28T10:30:00.000Z"
  }
}
```

**Error Response** (HTTP 403):
```json
{
  "status": false,
  "message": "Unauthorized",
  "data": null
}
```

**Example CURL**:
```bash
curl -X GET http://localhost:8000/api/v1/tasks/1 \
  -H "Authorization: Bearer 1|abcdef123456789..." \
  -H "Content-Type: application/json"
```

---

### 4. Update Task

**Endpoint**: `PUT /api/v1/tasks/{task_id}`

**Description**: Update an existing task

**Request Headers**:
```
Authorization: Bearer YOUR_API_TOKEN
Content-Type: application/json
```

**Request Body** (all fields optional):
```json
{
  "title": "Updated task title",
  "description": "Updated description",
  "priority": "medium",
  "is_completed": true,
  "deadline": "2024-03-20"
}
```

**Success Response** (HTTP 200):
```json
{
  "status": true,
  "message": "Task updated successfully",
  "data": {
    "id": 1,
    "user_id": 1,
    "title": "Updated task title",
    "description": "Updated description",
    "priority": "medium",
    "is_completed": true,
    "deadline": "2024-03-20",
    "created_at": "2024-02-28T10:30:00.000Z",
    "updated_at": "2024-02-28T11:45:00.000Z"
  }
}
```

**Example CURL**:
```bash
curl -X PUT http://localhost:8000/api/v1/tasks/1 \
  -H "Authorization: Bearer 1|abcdef123456789..." \
  -H "Content-Type: application/json" \
  -d '{
    "priority": "medium",
    "is_completed": true
  }'
```

---

### 5. Delete Task

**Endpoint**: `DELETE /api/v1/tasks/{task_id}`

**Description**: Delete a specific task

**Request Headers**:
```
Authorization: Bearer YOUR_API_TOKEN
Content-Type: application/json
```

**Success Response** (HTTP 200):
```json
{
  "status": true,
  "message": "Task deleted successfully",
  "data": null
}
```

**Example CURL**:
```bash
curl -X DELETE http://localhost:8000/api/v1/tasks/1 \
  -H "Authorization: Bearer 1|abcdef123456789..." \
  -H "Content-Type: application/json"
```

---

### 6. Toggle Task Completion

**Endpoint**: `POST /api/v1/tasks/{task_id}/toggle`

**Description**: Toggle task completion status

**Request Headers**:
```
Authorization: Bearer YOUR_API_TOKEN
Content-Type: application/json
```

**Success Response** (HTTP 200):
```json
{
  "status": true,
  "message": "Task status updated successfully",
  "data": {
    "id": 1,
    "user_id": 1,
    "title": "Complete project report",
    "description": "Finish the quarterly project report",
    "priority": "high",
    "is_completed": true,
    "deadline": "2024-03-15",
    "created_at": "2024-02-28T10:30:00.000Z",
    "updated_at": "2024-02-28T11:50:00.000Z"
  }
}
```

**Example CURL**:
```bash
curl -X POST http://localhost:8000/api/v1/tasks/1/toggle \
  -H "Authorization: Bearer 1|abcdef123456789..." \
  -H "Content-Type: application/json"
```

---

## Task Filtering & Statistics Endpoints

### 1. Get Completed Tasks

**Endpoint**: `GET /api/v1/tasks/filters/completed`

**Description**: Retrieve all completed tasks for the user

**Request Headers**:
```
Authorization: Bearer YOUR_API_TOKEN
Content-Type: application/json
```

**Example CURL**:
```bash
curl -X GET http://localhost:8000/api/v1/tasks/filters/completed \
  -H "Authorization: Bearer 1|abcdef123456789..." \
  -H "Content-Type: application/json"
```

---

### 2. Get Pending Tasks

**Endpoint**: `GET /api/v1/tasks/filters/pending`

**Description**: Retrieve all pending (incomplete) tasks, sorted by deadline

**Request Headers**:
```
Authorization: Bearer YOUR_API_TOKEN
Content-Type: application/json
```

**Example CURL**:
```bash
curl -X GET http://localhost:8000/api/v1/tasks/filters/pending \
  -H "Authorization: Bearer 1|abcdef123456789..." \
  -H "Content-Type: application/json"
```

---

### 3. Get Overdue Tasks

**Endpoint**: `GET /api/v1/tasks/filters/overdue`

**Description**: Retrieve all overdue tasks (deadline in past, not completed)

**Request Headers**:
```
Authorization: Bearer YOUR_API_TOKEN
Content-Type: application/json
```

**Example CURL**:
```bash
curl -X GET http://localhost:8000/api/v1/tasks/filters/overdue \
  -H "Authorization: Bearer 1|abcdef123456789..." \
  -H "Content-Type: application/json"
```

---

### 4. Get Tasks by Priority

**Endpoint**: `GET /api/v1/tasks/filters/by-priority`

**Description**: Retrieve tasks filtered by priority level

**Query Parameters**:
- `priority` (required): One of `low`, `medium`, `high`

**Request Headers**:
```
Authorization: Bearer YOUR_API_TOKEN
Content-Type: application/json
```

**Example CURL**:
```bash
curl -X GET "http://localhost:8000/api/v1/tasks/filters/by-priority?priority=high" \
  -H "Authorization: Bearer 1|abcdef123456789..." \
  -H "Content-Type: application/json"
```

---

### 5. Get Dashboard Statistics

**Endpoint**: `GET /api/v1/tasks/dashboard/stats`

**Description**: Retrieve task statistics for the dashboard

**Request Headers**:
```
Authorization: Bearer YOUR_API_TOKEN
Content-Type: application/json
```

**Success Response** (HTTP 200):
```json
{
  "status": true,
  "message": "Dashboard statistics retrieved",
  "data": {
    "total_tasks": 15,
    "completed_tasks": 8,
    "pending_tasks": 5,
    "overdue_tasks": 2
  }
}
```

**Example CURL**:
```bash
curl -X GET http://localhost:8000/api/v1/tasks/dashboard/stats \
  -H "Authorization: Bearer 1|abcdef123456789..." \
  -H "Content-Type: application/json"
```

---

## Health Check

### Check API Status

**Endpoint**: `GET /api/health`

**Description**: Check if API is running and get version information

**Success Response** (HTTP 200):
```json
{
  "status": true,
  "message": "API is running",
  "data": {
    "version": "1.0",
    "timestamp": "2024-02-28T10:30:00.000Z"
  }
}
```

**Example CURL**:
```bash
curl -X GET http://localhost:8000/api/health
```

---

## Error Codes & Status Codes

### HTTP Status Codes

| Code | Description |
|------|-------------|
| 200 | OK - Successful request |
| 201 | Created - Resource created successfully |
| 400 | Bad Request - Invalid request parameters |
| 401 | Unauthorized - Missing or invalid authentication token |
| 403 | Forbidden - User does not have permission to access resource |
| 422 | Unprocessable Entity - Validation failed |
| 500 | Internal Server Error - Server error |

---

## Authentication & Security

### Bearer Token Format

```
Authorization: Bearer <token>
```

### Token Structure

Tokens are issued using Laravel Sanctum and follow this format:
```
1|abcdef123456789...
```

### Security Best Practices

1. **Store tokens securely**: Use secure HTTP-only cookies or encryption in localStorage
2. **Use HTTPS**: Always use HTTPS in production
3. **Token expiration**: Implement token refresh mechanism
4. **Rate limiting**: Implement rate limiting for login attempts
5. **Password security**: Enforce strong password policies
6. **CORS**: Only allow trusted origins

---

## Setup Instructions

### 1. Install Dependencies
```bash
composer install
```

### 2. Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

Update `.env` with your database credentials:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_manager
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Run Migrations
```bash
php artisan migrate
```

### 4. Start Development Server
```bash
php artisan serve
```

API will be available at: `http://localhost:8000/api`

---

## Testing with Postman

1. Import the collection from the repository
2. Update the environment variable `{{token}}` after login
3. Test each endpoint in order

---

## Future Enhancements

- Role-based access control (RBAC)
- Task sharing and collaboration
- Real-time notifications
- Advanced task analytics
- File attachments for tasks
- Calendar integration
- Mobile app support
- Two-factor authentication

---

## Support & Contributing

For issues, feature requests, or contributions, please open an issue on GitHub.

---

## License

This project is licensed under the MIT License.
