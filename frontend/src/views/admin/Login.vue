<template>
  <div class="admin-login">
    <div class="login-container">
      <div class="login-card">
        <div class="login-header">
          <h1>Cebu Events</h1>
          <p>Admin Panel</p>
        </div>

        <form @submit.prevent="handleLogin" class="login-form">
          <div v-if="error" class="error-message">
            {{ error }}
          </div>

          <div class="form-group">
            <label for="email">Email Address</label>
            <input
              type="email"
              id="email"
              v-model="form.email"
              placeholder="admin@cebuevents.com"
              required
            />
          </div>

          <div class="form-group">
            <label for="password">Password</label>
            <input
              type="password"
              id="password"
              v-model="form.password"
              placeholder="Enter your password"
              required
            />
          </div>

          <button type="submit" class="login-btn" :disabled="loading">
            <span v-if="loading">Signing in...</span>
            <span v-else>Sign In</span>
          </button>
        </form>

        <div class="login-footer">
          <router-link to="/">← Back to Website</router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '../../api/axios';

const router = useRouter();

const form = ref({
  email: '',
  password: ''
});

const loading = ref(false);
const error = ref('');

const handleLogin = async () => {
  loading.value = true;
  error.value = '';

  try {
    const response = await api.post('/admin/login', form.value);

    localStorage.setItem('admin_token', response.data.token);
    localStorage.setItem('admin_user', JSON.stringify(response.data.user));

    router.push('/admin');
  } catch (err) {
    if (err.response?.data?.errors?.email) {
      error.value = err.response.data.errors.email[0];
    } else if (err.response?.data?.message) {
      error.value = err.response.data.message;
    } else {
      error.value = 'An error occurred. Please try again.';
    }
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.admin-login {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #1e293b, #0f172a);
  padding: 2rem;
}

.login-container {
  width: 100%;
  max-width: 420px;
}

.login-card {
  background: white;
  border-radius: 20px;
  padding: 2.5rem;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.login-header {
  text-align: center;
  margin-bottom: 2rem;
}

.brand-icon {
  font-size: 3rem;
  display: block;
  margin-bottom: 0.5rem;
}

.login-header h1 {
  font-size: 1.75rem;
  font-weight: 700;
  color: #1e293b;
  margin: 0;
}

.login-header p {
  color: #64748b;
  margin: 0.25rem 0 0;
}

.error-message {
  background: #fef2f2;
  color: #dc2626;
  padding: 1rem;
  border-radius: 10px;
  margin-bottom: 1.5rem;
  font-size: 0.9rem;
  text-align: center;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  font-weight: 500;
  color: #374151;
  margin-bottom: 0.5rem;
  font-size: 0.9rem;
}

.form-group input {
  width: 100%;
  padding: 0.875rem 1rem;
  border: 2px solid #e5e7eb;
  border-radius: 10px;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.form-group input:focus {
  outline: none;
  border-color: #0ea5e9;
  box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1);
}

.login-btn {
  width: 100%;
  padding: 1rem;
  background: linear-gradient(135deg, #0ea5e9, #0284c7);
  color: white;
  border: none;
  border-radius: 10px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.login-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(14, 165, 233, 0.4);
}

.login-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.login-footer {
  margin-top: 2rem;
  text-align: center;
}

.login-footer a {
  color: #64748b;
  text-decoration: none;
  font-size: 0.9rem;
  transition: color 0.3s ease;
}

.login-footer a:hover {
  color: #0ea5e9;
}
</style>
