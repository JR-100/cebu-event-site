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
            <line x1="3" y1="10" x2="21" y2="10"/>
            <line x1="9" y1="2" x2="9" y2="6"/>
            <line x1="15" y1="2" x2="15" y2="6"/>
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
        <h1>{{ isEditing ? 'Edit Event' : 'Create New Event' }}</h1>
        <router-link to="/admin/events" class="back-link">← Back to Events</router-link>
      </header>

      <div class="page-content">
        <form @submit.prevent="handleSubmit" class="event-form">
          <div v-if="error" class="error-message">
            {{ error }}
          </div>

          <div class="form-grid">
            <div class="form-group full-width">
              <label for="name">Event Name *</label>
              <input
                type="text"
                id="name"
                v-model="form.name"
                placeholder="Enter event name"
                required
                :class="{ 'error': errors.name }"
              />
              <span v-if="errors.name" class="field-error">{{ errors.name }}</span>
            </div>

            <div class="form-group">
              <label for="category">Category *</label>
              <select id="category" v-model="form.category" required :class="{ 'error': errors.category }">
                <option value="">Select category</option>
                <option value="Festival">Festival</option>
                <option value="Music">Music</option>
                <option value="Sports">Sports</option>
                <option value="Community">Community</option>
              </select>
              <span v-if="errors.category" class="field-error">{{ errors.category }}</span>
            </div>

            <div class="form-group">
              <label for="date_time">Date & Time *</label>
              <input
                type="datetime-local"
                id="date_time"
                v-model="form.date_time"
                required
                :class="{ 'error': errors.date_time }"
              />
              <span v-if="errors.date_time" class="field-error">{{ errors.date_time }}</span>
            </div>

            <div class="form-group full-width">
              <label for="location">Location *</label>
              <input
                type="text"
                id="location"
                v-model="form.location"
                placeholder="Enter venue/location"
                required
                :class="{ 'error': errors.location }"
              />
              <span v-if="errors.location" class="field-error">{{ errors.location }}</span>
            </div>

            <div class="form-group full-width">
              <label for="description">Description *</label>
              <textarea
                id="description"
                v-model="form.description"
                placeholder="Describe the event..."
                rows="5"
                required
                :class="{ 'error': errors.description }"
              ></textarea>
              <span v-if="errors.description" class="field-error">{{ errors.description }}</span>
            </div>

            <div class="form-group full-width">
              <label for="image">Event Image</label>
              <div class="image-upload">
                <div v-if="imagePreview || currentImage" class="image-preview">
                  <img :src="imagePreview || `${import.meta.env.VITE_API_URL}/storage/${currentImage}`" alt="Preview" />
                  <button type="button" @click="removeImage" class="remove-image">×</button>
                </div>
                <div v-else class="upload-placeholder" @click="triggerFileInput">
                  <svg class="upload-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                    <circle cx="8.5" cy="8.5" r="1.5"/>
                    <path d="M21 15l-5-5L5 21"/>
                  </svg>
                  <span>Click to upload image</span>
                  <span class="upload-hint">JPEG, PNG, GIF, WEBP (max 2MB)</span>
                </div>
                <input
                  type="file"
                  id="image"
                  ref="fileInput"
                  @change="handleImageChange"
                  accept="image/jpeg,image/png,image/gif,image/webp"
                  hidden
                />
              </div>
              <span v-if="errors.image" class="field-error">{{ errors.image }}</span>
            </div>

            <div class="form-group">
              <label class="checkbox-label">
                <input type="checkbox" v-model="form.is_featured" />
                <span class="checkmark"></span>
                Mark as Featured Event
              </label>
            </div>
          </div>

          <div class="form-actions">
            <router-link to="/admin/events" class="cancel-btn">Cancel</router-link>
            <button type="submit" class="submit-btn" :disabled="submitting">
              {{ submitting ? 'Saving...' : (isEditing ? 'Update Event' : 'Create Event') }}
            </button>
          </div>
        </form>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../../api/axios';

const route = useRoute();
const router = useRouter();
const sidebarOpen = ref(false);
const fileInput = ref(null);

const isEditing = computed(() => !!route.params.id);

const form = ref({
  name: '',
  description: '',
  date_time: '',
  location: '',
  category: '',
  is_featured: false
});

const imageFile = ref(null);
const imagePreview = ref(null);
const currentImage = ref(null);
const submitting = ref(false);
const error = ref('');
const errors = ref({});

const triggerFileInput = () => {
  fileInput.value.click();
};

const handleImageChange = (e) => {
  const file = e.target.files[0];
  if (file) {
    if (file.size > 2 * 1024 * 1024) {
      errors.value.image = 'Image size must be less than 2MB';
      return;
    }

    imageFile.value = file;
    const reader = new FileReader();
    reader.onload = (e) => {
      imagePreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
    errors.value.image = null;
  }
};

const removeImage = () => {
  imageFile.value = null;
  imagePreview.value = null;
  currentImage.value = null;
  if (fileInput.value) {
    fileInput.value.value = '';
  }
};

const validateForm = () => {
  errors.value = {};

  if (!form.value.name.trim()) {
    errors.value.name = 'Event name is required';
  }

  if (!form.value.category) {
    errors.value.category = 'Category is required';
  }

  if (!form.value.date_time) {
    errors.value.date_time = 'Date and time is required';
  }

  if (!form.value.location.trim()) {
    errors.value.location = 'Location is required';
  }

  if (!form.value.description.trim()) {
    errors.value.description = 'Description is required';
  }

  return Object.keys(errors.value).length === 0;
};

const handleSubmit = async () => {
  if (!validateForm()) {
    error.value = 'Please fix the errors above';
    return;
  }

  submitting.value = true;
  error.value = '';

  try {
    const formData = new FormData();
    formData.append('name', form.value.name);
    formData.append('description', form.value.description);
    formData.append('date_time', form.value.date_time);
    formData.append('location', form.value.location);
    formData.append('category', form.value.category);
    formData.append('is_featured', form.value.is_featured ? '1' : '0');

    if (imageFile.value) {
      formData.append('image', imageFile.value);
    }

    if (isEditing.value) {
      await api.post(`/admin/events/${route.params.id}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
    } else {
      await api.post('/admin/events', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
    }

    router.push('/admin/events');
  } catch (err) {
    if (err.response?.data?.errors) {
      errors.value = {};
      Object.keys(err.response.data.errors).forEach(key => {
        errors.value[key] = err.response.data.errors[key][0];
      });
      error.value = 'Please fix the errors above';
    } else {
      error.value = err.response?.data?.message || 'An error occurred. Please try again.';
    }
  } finally {
    submitting.value = false;
  }
};

const logout = async () => {
  try {
    await api.post('/admin/logout');
  } catch (err) {
    console.error('Logout error:', err);
  } finally {
    localStorage.removeItem('admin_token');
    localStorage.removeItem('admin_user');
    router.push('/admin/login');
  }
};

onMounted(async () => {
  if (isEditing.value) {
    try {
      const response = await api.get(`/admin/events/${route.params.id}`);
      const event = response.data;

      // Format datetime for input
      const date = new Date(event.date_time);
      const formattedDate = date.toISOString().slice(0, 16);

      form.value = {
        name: event.name,
        description: event.description,
        date_time: formattedDate,
        location: event.location,
        category: event.category,
        is_featured: event.is_featured
      };

      currentImage.value = event.image;
    } catch (err) {
      error.value = 'Failed to load event';
      console.error('Error loading event:', err);
    }
  }
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
  height: 100vh;
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

.back-link {
  color: #64748b;
  text-decoration: none;
  font-size: 0.9rem;
  transition: color 0.3s ease;
}

.back-link:hover {
  color: #0ea5e9;
}

.page-content {
  padding: 2rem;
}

/* Form */
.event-form {
  background: white;
  border-radius: 16px;
  padding: 2rem;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
  max-width: 800px;
}

.error-message {
  background: #fef2f2;
  color: #dc2626;
  padding: 1rem;
  border-radius: 10px;
  margin-bottom: 1.5rem;
  font-size: 0.9rem;
}

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group.full-width {
  grid-column: span 2;
}

.form-group label {
  font-weight: 500;
  color: #374151;
  margin-bottom: 0.5rem;
  font-size: 0.9rem;
}

.form-group input,
.form-group select,
.form-group textarea {
  padding: 0.875rem 1rem;
  border: 2px solid #e5e7eb;
  border-radius: 10px;
  font-size: 1rem;
  background: white;
  color: #1e293b;
  transition: all 0.3s ease;
}

.form-group input::placeholder,
.form-group textarea::placeholder {
  color: #94a3b8;
}

.form-group input[type="datetime-local"]::-webkit-calendar-picker-indicator {
  cursor: pointer;
  filter: invert(20%) sepia(10%) saturate(500%) hue-rotate(180deg);
  opacity: 1;
  font-size: 1.2rem;
  padding: 4px;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #0ea5e9;
  box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1);
}

.form-group input.error,
.form-group select.error,
.form-group textarea.error {
  border-color: #ef4444;
}

.field-error {
  color: #ef4444;
  font-size: 0.8rem;
  margin-top: 0.25rem;
}

.form-group textarea {
  resize: vertical;
  min-height: 120px;
}

/* Image Upload */
.image-upload {
  border: 2px dashed #e5e7eb;
  border-radius: 10px;
  overflow: hidden;
}

.upload-placeholder {
  padding: 2rem;
  text-align: center;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
}

.upload-placeholder:hover {
  background: #f8fafc;
  border-color: #0ea5e9;
}

.upload-icon {
  width: 48px;
  height: 48px;
  color: #94a3b8;
}

.upload-hint {
  font-size: 0.8rem;
  color: #94a3b8;
}

.image-preview {
  position: relative;
}

.image-preview img {
  width: 100%;
  max-height: 300px;
  object-fit: cover;
}

.remove-image {
  position: absolute;
  top: 10px;
  right: 10px;
  width: 30px;
  height: 30px;
  border: none;
  background: rgba(0, 0, 0, 0.7);
  color: white;
  border-radius: 50%;
  font-size: 1.25rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.remove-image:hover {
  background: #ef4444;
}

/* Checkbox */
.checkbox-label {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  cursor: pointer;
  font-weight: 500;
  color: #374151;
}

.checkbox-label input {
  display: none;
}

.checkmark {
  width: 22px;
  height: 22px;
  border: 2px solid #e5e7eb;
  border-radius: 6px;
  transition: all 0.3s ease;
  position: relative;
}

.checkbox-label input:checked + .checkmark {
  background: #0ea5e9;
  border-color: #0ea5e9;
}

.checkbox-label input:checked + .checkmark::after {
  content: '✓';
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  color: white;
  font-size: 0.8rem;
}

/* Form Actions */
.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 2rem;
  padding-top: 1.5rem;
  border-top: 1px solid #e5e7eb;
}

.cancel-btn {
  padding: 0.875rem 1.5rem;
  border: 2px solid #e5e7eb;
  background: white;
  border-radius: 10px;
  font-weight: 500;
  color: #64748b;
  text-decoration: none;
  transition: all 0.3s ease;
}

.cancel-btn:hover {
  background: #f8fafc;
}

.submit-btn {
  padding: 0.875rem 2rem;
  border: none;
  background: linear-gradient(135deg, #0ea5e9, #0284c7);
  color: white;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.submit-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(14, 165, 233, 0.4);
}

.submit-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
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
    flex-wrap: wrap;
  }

  .top-header h1 {
    font-size: 1.25rem;
    flex: 1;
  }

  .page-content {
    padding: 1rem;
  }

  .form-grid {
    grid-template-columns: 1fr;
  }

  .form-group.full-width {
    grid-column: span 1;
  }

  .back-link {
    font-size: 0.85rem;
  }
}

@media (max-width: 480px) {
  .top-header {
    padding: 0.75rem 1rem;
  }

  .submit-btn {
    width: 100%;
  }
}
</style>
