import axios from 'axios';

/**
 * Smart Task Manager API Client
 * Complete NextJS/JavaScript example integration
 */

const API_BASE_URL = 'http://localhost:8000/api/v1';
const API_HEALTH_CHECK = 'http://localhost:8000/api/health';

/**
 * API Client Class for Task Manager
 */
class TaskManagerAPI {
  constructor() {
    this.token = localStorage.getItem('taskManagerToken') || null;
    this.client = axios.create({
      baseURL: API_BASE_URL,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
    });

    // Add token to all requests if available
    if (this.token) {
      this.client.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
    }
  }

  /**
   * Set authentication token
   */
  setToken(token) {
    this.token = token;
    localStorage.setItem('taskManagerToken', token);
    this.client.defaults.headers.common['Authorization'] = `Bearer ${token}`;
  }

  /**
   * Clear authentication token
   */
  clearToken() {
    this.token = null;
    localStorage.removeItem('taskManagerToken');
    delete this.client.defaults.headers.common['Authorization'];
  }

  /**
   * Check API health
   */
  async checkHealth() {
    try {
      const response = await axios.get(API_HEALTH_CHECK);
      return response.data;
    } catch (error) {
      return {
        status: false,
        message: 'API is unavailable',
      };
    }
  }

  /**
   * AUTHENTICATION ENDPOINTS
   */

  /**
   * Register a new user
   */
  async register(name, email, password, passwordConfirmation) {
    try {
      const response = await this.client.post('/auth/register', {
        name,
        email,
        password,
        password_confirmation: passwordConfirmation,
      });

      if (response.data.data?.token) {
        this.setToken(response.data.data.token);
      }

      return response.data;
    } catch (error) {
      return {
        status: false,
        message: error.response?.data?.message || 'Registration failed',
        data: error.response?.data?.data || null,
      };
    }
  }

  /**
   * Login user
   */
  async login(email, password) {
    try {
      const response = await this.client.post('/auth/login', {
        email,
        password,
      });

      if (response.data.data?.token) {
        this.setToken(response.data.data.token);
      }

      return response.data;
    } catch (error) {
      return {
        status: false,
        message: error.response?.data?.message || 'Login failed',
        data: error.response?.data?.data || null,
      };
    }
  }

  /**
   * Get current user details
   */
  async getMe() {
    try {
      const response = await this.client.get('/auth/me');
      return response.data;
    } catch (error) {
      return {
        status: false,
        message: error.response?.data?.message || 'Failed to fetch user',
        data: null,
      };
    }
  }

  /**
   * Logout user
   */
  async logout() {
    try {
      const response = await this.client.post('/auth/logout');
      this.clearToken();
      return response.data;
    } catch (error) {
      this.clearToken();
      return {
        status: false,
        message: error.response?.data?.message || 'Logout failed',
        data: null,
      };
    }
  }

  /**
   * Logout from all devices
   */
  async logoutAllDevices() {
    try {
      const response = await this.client.post('/auth/logout-all-devices');
      this.clearToken();
      return response.data;
    } catch (error) {
      this.clearToken();
      return {
        status: false,
        message: error.response?.data?.message || 'Logout failed',
        data: null,
      };
    }
  }

  /**
   * TASK ENDPOINTS
   */

  /**
   * Get all tasks with pagination
   */
  async getTasks(perPage = 15, page = 1) {
    try {
      const response = await this.client.get('/tasks', {
        params: {
          per_page: perPage,
          page,
        },
      });
      return response.data;
    } catch (error) {
      return {
        status: false,
        message: error.response?.data?.message || 'Failed to fetch tasks',
        data: null,
      };
    }
  }

  /**
   * Create a new task
   */
  async createTask(title, description = null, priority = 'medium', deadline = null) {
    try {
      const response = await this.client.post('/tasks', {
        title,
        description,
        priority,
        deadline,
      });
      return response.data;
    } catch (error) {
      return {
        status: false,
        message: error.response?.data?.message || 'Failed to create task',
        data: error.response?.data?.data || null,
      };
    }
  }

  /**
   * Get a single task
   */
  async getTask(taskId) {
    try {
      const response = await this.client.get(`/tasks/${taskId}`);
      return response.data;
    } catch (error) {
      return {
        status: false,
        message: error.response?.data?.message || 'Failed to fetch task',
        data: null,
      };
    }
  }

  /**
   * Update a task
   */
  async updateTask(taskId, updates) {
    try {
      const response = await this.client.put(`/tasks/${taskId}`, updates);
      return response.data;
    } catch (error) {
      return {
        status: false,
        message: error.response?.data?.message || 'Failed to update task',
        data: error.response?.data?.data || null,
      };
    }
  }

  /**
   * Delete a task
   */
  async deleteTask(taskId) {
    try {
      const response = await this.client.delete(`/tasks/${taskId}`);
      return response.data;
    } catch (error) {
      return {
        status: false,
        message: error.response?.data?.message || 'Failed to delete task',
        data: null,
      };
    }
  }

  /**
   * Toggle task completion status
   */
  async toggleTask(taskId) {
    try {
      const response = await this.client.post(`/tasks/${taskId}/toggle`);
      return response.data;
    } catch (error) {
      return {
        status: false,
        message: error.response?.data?.message || 'Failed to toggle task',
        data: null,
      };
    }
  }

  /**
   * FILTER ENDPOINTS
   */

  /**
   * Get completed tasks
   */
  async getCompletedTasks() {
    try {
      const response = await this.client.get('/tasks/filters/completed');
      return response.data;
    } catch (error) {
      return {
        status: false,
        message: error.response?.data?.message || 'Failed to fetch completed tasks',
        data: null,
      };
    }
  }

  /**
   * Get pending tasks
   */
  async getPendingTasks() {
    try {
      const response = await this.client.get('/tasks/filters/pending');
      return response.data;
    } catch (error) {
      return {
        status: false,
        message: error.response?.data?.message || 'Failed to fetch pending tasks',
        data: null,
      };
    }
  }

  /**
   * Get overdue tasks
   */
  async getOverdueTasks() {
    try {
      const response = await this.client.get('/tasks/filters/overdue');
      return response.data;
    } catch (error) {
      return {
        status: false,
        message: error.response?.data?.message || 'Failed to fetch overdue tasks',
        data: null,
      };
    }
  }

  /**
   * Get tasks by priority
   */
  async getTasksByPriority(priority) {
    if (!['low', 'medium', 'high'].includes(priority)) {
      return {
        status: false,
        message: 'Invalid priority. Must be one of: low, medium, high',
        data: null,
      };
    }

    try {
      const response = await this.client.get('/tasks/filters/by-priority', {
        params: { priority },
      });
      return response.data;
    } catch (error) {
      return {
        status: false,
        message: error.response?.data?.message || 'Failed to fetch tasks by priority',
        data: null,
      };
    }
  }

  /**
   * Get dashboard statistics
   */
  async getDashboardStats() {
    try {
      const response = await this.client.get('/tasks/dashboard/stats');
      return response.data;
    } catch (error) {
      return {
        status: false,
        message: error.response?.data?.message || 'Failed to fetch statistics',
        data: null,
      };
    }
  }
}

// Create singleton instance
const api = new TaskManagerAPI();

export default api;

/**
 * USAGE EXAMPLES
 */

/*
// Example 1: Register new user
async function exampleRegister() {
  const result = await api.register(
    'John Doe',
    'john@example.com',
    'SecurePassword123!',
    'SecurePassword123!'
  );

  if (result.status) {
    console.log('Registered successfully!', result.data.user);
    console.log('Token:', result.data.token);
  } else {
    console.log('Registration failed:', result.message);
  }
}

// Example 2: Login
async function exampleLogin() {
  const result = await api.login('john@example.com', 'SecurePassword123!');

  if (result.status) {
    console.log('Logged in successfully!', result.data.user);
  } else {
    console.log('Login failed:', result.message);
  }
}

// Example 3: Create task
async function exampleCreateTask() {
  const result = await api.createTask(
    'Complete project report',
    'Finish the quarterly project report with all metrics',
    'high',
    '2024-03-15'
  );

  if (result.status) {
    console.log('Task created:', result.data);
  } else {
    console.log('Failed to create task:', result.message);
  }
}

// Example 4: Get all tasks
async function exampleGetTasks() {
  const result = await api.getTasks(20, 1);

  if (result.status) {
    console.log('Tasks:', result.data.tasks);
    console.log('Pagination:', result.data.pagination);
  } else {
    console.log('Failed to fetch tasks:', result.message);
  }
}

// Example 5: Update task
async function exampleUpdateTask() {
  const result = await api.updateTask(1, {
    priority: 'low',
    is_completed: true,
  });

  if (result.status) {
    console.log('Task updated:', result.data);
  } else {
    console.log('Failed to update task:', result.message);
  }
}

// Example 6: Get dashboard stats
async function exampleDashboardStats() {
  const result = await api.getDashboardStats();

  if (result.status) {
    console.log('Dashboard Stats:', result.data);
  } else {
    console.log('Failed to fetch stats:', result.message);
  }
}

// Example 7: Logout
async function exampleLogout() {
  const result = await api.logout();

  if (result.status) {
    console.log('Logged out successfully');
  } else {
    console.log('Logout failed:', result.message);
  }
}

// Example 8: Check API health
async function exampleHealthCheck() {
  const health = await api.checkHealth();
  console.log('API Health:', health);
}
*/
