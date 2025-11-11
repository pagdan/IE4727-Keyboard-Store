import axios from 'axios';

const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:5000/api';

const api = axios.create({
  baseURL: API_URL,
  headers: {
    'Content-Type': 'application/json'
  }
});

// Add token to requests if exists
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('adminToken');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Product API
export const productAPI = {
  getAll: (params) => api.get('/products', { params }),
  getById: (id) => api.get(`/products/${id}`),
  getFeatured: () => api.get('/products/featured'),
  create: (data) => api.post('/products', data),
  update: (id, data) => api.put(`/products/${id}`, data),
  delete: (id) => api.delete(`/products/${id}`)
};

// Order API
export const orderAPI = {
  create: (data) => api.post('/orders', data),
  getByOrderNumber: (orderNumber) => api.get(`/orders/${orderNumber}`),
  getAll: (params) => api.get('/orders', { params }),
  updateStatus: (id, status) => api.put(`/orders/${id}/status`, { status })
};

// Admin API
export const adminAPI = {
  login: (credentials) => api.post('/admin/login', credentials),
  register: (data) => api.post('/admin/register', data),
  getStats: () => api.get('/admin/stats'),
  getMe: () => api.get('/admin/me')
};

export default api;