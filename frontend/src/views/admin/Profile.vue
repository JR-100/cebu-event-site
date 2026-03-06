<template>
  <div class="admin-layout">
    <!-- Mobile Sidebar Overlay -->
    <div v-if="sidebarOpen" class="sidebar-overlay" @click="sidebarOpen = false"></div>
    <!-- Sidebar -->
    <aside class="sidebar" :class="{ open: sidebarOpen }">
      <div class="sidebar-header">
        <svg class="brand-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
        </svg>
        <span class="brand-text">Cebu Events</span>
      </div>

      <nav class="sidebar-nav">
        <router-link to="/admin" class="nav-item" exact-active-class="active">
          <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="3" width="7" height="7" rx="1"/>
            <rect x="14" y="3" width="7" height="7" rx="1"/>
            <rect x="3" y="14" width="7" height="7" rx="1"/>
            <rect x="14" y="14" width="7" height="7" rx="1"/>
          </svg>
          Dashboard
        </router-link>
        <router-link to="/admin/events" class="nav-item" active-class="active">
          <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="4" width="18" height="18" rx="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/>
            <line x1="8" y1="2" x2="8" y2="6"/>
            <line x1="3" y1="10" x2="21" y2="10"/>
          </svg>
          Events
        </router-link>
      </nav>

      <div class="sidebar-footer">
        <router-link to="/admin/profile" class="nav-item" active-class="active">
          <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
          </svg>
          Profile
        </router-link>
        <router-link to="/" class="nav-item">
          <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <line x1="2" y1="12" x2="22" y2="12"/>
            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
          </svg>
          View Website
        </router-link>
        <button @click="logout" class="nav-item logout-btn">
          <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
            <polyline points="16 17 21 12 16 7"/>
            <line x1="21" y1="12" x2="9" y2="12"/>
          </svg>
          Logout
        </button>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <header class="top-header">
        <button class="mobile-menu-btn" @click="sidebarOpen = !sidebarOpen">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
        <h1>Profile Settings</h1>
      </header>

      <div class="page-content">
        <!-- Profile Info Card -->
        <div class="profile-card">
          <div class="profile-avatar" @click="triggerImageUpload">
            <img v-if="imagePreview || profile.profile_image" :src="imagePreview || `${import.meta.env.VITE_API_URL}/storage/${profile.profile_image}`" alt="Profile" class="avatar-img" />
            <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
              <circle cx="12" cy="7" r="4"/>
            </svg>
            <div class="avatar-overlay">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                <circle cx="12" cy="13" r="4"/>
              </svg>
            </div>
            <input type="file" ref="imageInput" @change="handleImageChange" accept="image/jpeg,image/png,image/gif,image/webp" hidden />
          </div>
          <div class="profile-info">
            <h2>{{ profile.name || 'Admin' }}</h2>
            <p>{{ profile.email }}</p>
            <button v-if="imagePreview || profile.profile_image" @click.stop="removeImage" class="remove-photo-btn">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="3 6 5 6 21 6"/>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
              </svg>
              Remove Photo
            </button>
          </div>
        </div>

        <!-- Success/Error Messages -->
        <div v-if="profileSuccess" class="alert success">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
            <polyline points="22 4 12 14.01 9 11.01"/>
          </svg>
          {{ profileSuccess }}
        </div>
        <div v-if="profileError" class="alert error">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <line x1="15" y1="9" x2="9" y2="15"/>
            <line x1="9" y1="9" x2="15" y2="15"/>
          </svg>
          {{ profileError }}
        </div>

        <div class="forms-grid">
          <!-- Edit Profile Form -->
          <div class="form-section">
            <div class="section-header">
              <svg class="section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
              </svg>
              <h3>Edit Profile</h3>
            </div>

            <form @submit.prevent="updateProfile" class="profile-form">
              <div class="form-group">
                <label for="name">Full Name</label>
                <input
                  type="text"
                  id="name"
                  v-model="profile.name"
                  placeholder="Enter your name"
                  required
                />
              </div>

              <div class="form-group">
                <label for="email">Email Address</label>
                <input
                  type="email"
                  id="email"
                  v-model="profile.email"
                  placeholder="Enter your email"
                  required
                />
              </div>

              <button type="submit" class="save-btn" :disabled="savingProfile">
                <svg v-if="!savingProfile" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                  <polyline points="17 21 17 13 7 13 7 21"/>
                  <polyline points="7 3 7 8 15 8"/>
                </svg>
                {{ savingProfile ? 'Saving...' : 'Save Changes' }}
              </button>
            </form>
          </div>

          <!-- Change Password Form -->
          <div class="form-section">
            <div class="section-header">
              <svg class="section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
              </svg>
              <h3>Change Password</h3>
            </div>

            <div v-if="passwordSuccess" class="alert success inline">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                <polyline points="22 4 12 14.01 9 11.01"/>
              </svg>
              {{ passwordSuccess }}
            </div>
            <div v-if="passwordError" class="alert error inline">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <line x1="15" y1="9" x2="9" y2="15"/>
                <line x1="9" y1="9" x2="15" y2="15"/>
              </svg>
              {{ passwordError }}
            </div>

            <form @submit.prevent="updatePassword" class="profile-form">
              <div class="form-group">
                <label for="current_password">Current Password</label>
                <input
                  type="password"
                  id="current_password"
                  v-model="passwords.current_password"
                  placeholder="Enter current password"
                  required
                />
              </div>

              <div class="form-group">
                <label for="password">New Password</label>
                <input
                  type="password"
                  id="password"
                  v-model="passwords.password"
                  placeholder="Enter new password"
                  required
                />
              </div>

              <div class="form-group">
                <label for="password_confirmation">Confirm New Password</label>
                <input
                  type="password"
                  id="password_confirmation"
                  v-model="passwords.password_confirmation"
                  placeholder="Confirm new password"
                  required
                />
              </div>

              <button type="submit" class="save-btn password-btn" :disabled="savingPassword">
                <svg v-if="!savingPassword" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                  <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                {{ savingPassword ? 'Updating...' : 'Update Password' }}
              </button>
            </form>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '../../api/axios';

const router = useRouter();
const sidebarOpen = ref(false);
const imageInput = ref(null);

const profile = ref({
  name: '',
  email: '',
  profile_image: null,
});

const imageFile = ref(null);
const imagePreview = ref(null);
const removeImageFlag = ref(false);

const passwords = ref({
  current_password: '',
  password: '',
  password_confirmation: '',
});

const savingProfile = ref(false);
const savingPassword = ref(false);
const profileSuccess = ref('');
const profileError = ref('');
const passwordSuccess = ref('');
const passwordError = ref('');

const fetchProfile = async () => {
  try {
    const response = await api.get('/admin/me');
    profile.value.name = response.data.name;
    profile.value.email = response.data.email;
    profile.value.profile_image = response.data.profile_image;
  } catch (err) {
    console.error('Failed to fetch profile:', err);
  }
};

const triggerImageUpload = () => {
  imageInput.value.click();
};

const handleImageChange = (e) => {
  const file = e.target.files[0];
  if (file) {
    if (file.size > 2 * 1024 * 1024) {
      profileError.value = 'Image must be less than 2MB';
      return;
    }
    imageFile.value = file;
    removeImageFlag.value = false;
    const reader = new FileReader();
    reader.onload = (e) => {
      imagePreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};

const removeImage = () => {
  imageFile.value = null;
  imagePreview.value = null;
  removeImageFlag.value = true;
  profile.value.profile_image = null;
};

const updateProfile = async () => {
  savingProfile.value = true;
  profileSuccess.value = '';
  profileError.value = '';

  try {
    const formData = new FormData();
    formData.append('name', profile.value.name);
    formData.append('email', profile.value.email);

    if (imageFile.value) {
      formData.append('profile_image', imageFile.value);
    }
    if (removeImageFlag.value) {
      formData.append('remove_image', '1');
    }

    const response = await api.post('/admin/profile', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });
    profileSuccess.value = response.data.message;

    // Update local state
    profile.value.profile_image = response.data.user.profile_image;
    imageFile.value = null;
    imagePreview.value = null;
    removeImageFlag.value = false;

    // Update stored user
    const storedUser = JSON.parse(localStorage.getItem('admin_user') || '{}');
    storedUser.name = profile.value.name;
    storedUser.email = profile.value.email;
    storedUser.profile_image = profile.value.profile_image;
    localStorage.setItem('admin_user', JSON.stringify(storedUser));
  } catch (err) {
    if (err.response?.data?.errors) {
      const errors = err.response.data.errors;
      profileError.value = Object.values(errors).flat().join(', ');
    } else {
      profileError.value = 'Failed to update profile';
    }
  } finally {
    savingProfile.value = false;
  }
};

const updatePassword = async () => {
  savingPassword.value = true;
  passwordSuccess.value = '';
  passwordError.value = '';

  try {
    const response = await api.put('/admin/password', passwords.value);
    passwordSuccess.value = response.data.message;
    passwords.value = {
      current_password: '',
      password: '',
      password_confirmation: '',
    };
  } catch (err) {
    if (err.response?.data?.errors) {
      const errors = err.response.data.errors;
      passwordError.value = Object.values(errors).flat().join(', ');
    } else if (err.response?.data?.message) {
      passwordError.value = err.response.data.message;
    } else {
      passwordError.value = 'Failed to update password';
    }
  } finally {
    savingPassword.value = false;
  }
};

const logout = async () => {
  try {
    await api.post('/admin/logout');
  } catch (e) {}
  localStorage.removeItem('admin_token');
  localStorage.removeItem('admin_user');
  router.push('/admin/login');
};

onMounted(() => {
  fetchProfile();
});
</script>

<style scoped>
.admin-layout {
  display: flex;
  min-height: 100vh;
  background: #f1f5f9;
}

/* Sidebar */
.sidebar {
  width: 260px;
  background: linear-gradient(180deg, #1e293b, #0f172a);
  color: white;
  display: flex;
  flex-direction: column;
  position: fixed;
  top: 0;
  left: 0;
  bottom: 0;
}

.sidebar-header {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1.5rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.brand-icon {
  width: 28px;
  height: 28px;
  color: #0ea5e9;
}

.brand-text {
  font-size: 1.25rem;
  font-weight: 700;
}

.sidebar-nav {
  padding: 1rem 0;
  flex: 1;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.875rem 1.5rem;
  color: #94a3b8;
  text-decoration: none;
  transition: all 0.3s ease;
  border: none;
  background: none;
  width: 100%;
  font-size: 1rem;
  cursor: pointer;
  text-align: left;
}

.nav-item:hover,
.nav-item.active {
  background: rgba(14, 165, 233, 0.1);
  color: white;
}

.nav-item.active {
  border-left: 3px solid #0ea5e9;
}

.nav-icon {
  width: 20px;
  height: 20px;
  flex-shrink: 0;
}

.sidebar-footer {
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  padding: 0.5rem 0;
  margin-top: auto;
}

.logout-btn {
  color: #f87171;
}

.logout-btn:hover {
  background: rgba(248, 113, 113, 0.1);
  color: #f87171;
}

/* Main Content */
.main-content {
  flex: 1;
  margin-left: 260px;
}

.top-header {
  background: white;
  padding: 1.5rem 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.top-header h1 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1e293b;
  margin: 0;
}

.page-content {
  padding: 2rem;
  max-width: 900px;
}

/* Profile Card */
.profile-card {
  display: flex;
  align-items: center;
  gap: 1.5rem;
  background: white;
  border-radius: 16px;
  padding: 2rem;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
  margin-bottom: 1.5rem;
}

.profile-avatar {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: linear-gradient(135deg, #0ea5e9, #0284c7);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  cursor: pointer;
  position: relative;
  overflow: hidden;
}

.profile-avatar > svg {
  width: 44px;
  height: 44px;
  color: white;
}

.avatar-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.avatar-overlay {
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.avatar-overlay svg {
  width: 28px;
  height: 28px;
  color: white;
}

.profile-avatar:hover .avatar-overlay {
  opacity: 1;
}

.profile-info h2 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1e293b;
  margin: 0 0 0.25rem 0;
}

.profile-info p {
  font-size: 0.95rem;
  color: #64748b;
  margin: 0;
}

.remove-photo-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  margin-top: 0.5rem;
  padding: 0.35rem 0.75rem;
  border: 1px solid #fecaca;
  background: #fef2f2;
  color: #dc2626;
  border-radius: 6px;
  font-size: 0.8rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.remove-photo-btn svg {
  width: 14px;
  height: 14px;
}

.remove-photo-btn:hover {
  background: #fee2e2;
}

/* Alerts */
.alert {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem 1.25rem;
  border-radius: 10px;
  font-size: 0.9rem;
  margin-bottom: 1.5rem;
}

.alert.inline {
  margin-bottom: 1rem;
}

.alert svg {
  width: 20px;
  height: 20px;
  flex-shrink: 0;
}

.alert.success {
  background: #dcfce7;
  color: #16a34a;
  border: 1px solid #bbf7d0;
}

.alert.error {
  background: #fef2f2;
  color: #dc2626;
  border: 1px solid #fecaca;
}

/* Forms Grid */
.forms-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
}

.form-section {
  background: white;
  border-radius: 16px;
  padding: 2rem;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
}

.section-header {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 2px solid #f1f5f9;
}

.section-icon {
  width: 24px;
  height: 24px;
  color: #0ea5e9;
}

.section-header h3 {
  font-size: 1.15rem;
  font-weight: 600;
  color: #1e293b;
  margin: 0;
}

/* Form */
.profile-form {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group label {
  font-weight: 500;
  color: #374151;
  margin-bottom: 0.5rem;
  font-size: 0.9rem;
}

.form-group input {
  padding: 0.875rem 1rem;
  border: 2px solid #e5e7eb;
  border-radius: 10px;
  font-size: 1rem;
  background: white;
  color: #1e293b;
  transition: all 0.3s ease;
}

.form-group input::placeholder {
  color: #94a3b8;
}

.form-group input:focus {
  outline: none;
  border-color: #0ea5e9;
  box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1);
}

.save-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.875rem 1.5rem;
  background: linear-gradient(135deg, #0ea5e9, #0284c7);
  color: white;
  border: none;
  border-radius: 10px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  margin-top: 0.5rem;
}

.save-btn svg {
  width: 18px;
  height: 18px;
}

.save-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(14, 165, 233, 0.4);
}

.save-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.save-btn.password-btn {
  background: linear-gradient(135deg, #1e293b, #334155);
}

.save-btn.password-btn:hover:not(:disabled) {
  box-shadow: 0 4px 15px rgba(30, 41, 59, 0.4);
}

.mobile-menu-btn {
  display: none;
  background: none;
  border: none;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 8px;
  transition: background 0.2s;
}

.mobile-menu-btn:hover {
  background: #e2e8f0;
}

.mobile-menu-btn svg {
  width: 24px;
  height: 24px;
  color: #1e293b;
}

.sidebar-overlay {
  display: none;
}

@media (max-width: 768px) {
  .sidebar {
    transform: translateX(-100%);
    transition: transform 0.3s ease;
    z-index: 1001;
  }

  .sidebar.open {
    transform: translateX(0);
  }

  .sidebar-overlay {
    display: block;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1000;
  }

  .main-content {
    margin-left: 0;
  }

  .mobile-menu-btn {
    display: block;
  }

  .top-header {
    gap: 0.75rem;
  }

  .top-header h1 {
    font-size: 1.25rem;
  }

  .page-content {
    padding: 1rem;
  }

  .forms-grid {
    grid-template-columns: 1fr;
  }

  .profile-card {
    flex-direction: column;
    text-align: center;
  }

  .profile-avatar {
    margin: 0 auto;
  }
}

@media (max-width: 480px) {
  .top-header {
    padding: 0.75rem 1rem;
  }

  .profile-avatar {
    width: 80px;
    height: 80px;
  }

  .save-btn {
    width: 100%;
  }
}
</style>
