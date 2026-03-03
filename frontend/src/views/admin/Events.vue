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
        <h1>Events Management</h1>
        <button @click="openCreateModal" class="add-btn">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="12" y1="5" x2="12" y2="19"/>
            <line x1="5" y1="12" x2="19" y2="12"/>
          </svg>
          Add New Event
        </button>
      </header>

      <div class="page-content">
        <!-- Search & Filter -->
        <div class="controls">
          <div class="search-box">
            <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="11" cy="11" r="8"/>
              <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input
              type="text"
              v-model="searchQuery"
              placeholder="Search events..."
              @input="debouncedSearch"
            />
          </div>
        </div>

        <!-- Events Table -->
        <div class="table-wrapper">
          <div v-if="loading" class="loading">
            <div class="spinner"></div>
          </div>

          <table v-else class="events-table">
            <thead>
              <tr>
                <th>Event</th>
                <th>Category</th>
                <th>Date & Time</th>
                <th>Location</th>
                <th>Featured</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="event in events" :key="event.id">
                <td>
                  <div class="event-cell">
                    <img
                      :src="event.image ? `http://localhost:8000/storage/${event.image}` : defaultImage"
                      :alt="event.name"
                      class="event-thumb"
                    />
                    <span class="event-name">{{ event.name }}</span>
                  </div>
                </td>
                <td>
                  <span class="category-tag" :class="getCategoryClass(event.category)">
                    {{ event.category }}
                  </span>
                </td>
                <td>{{ formatDate(event.date_time) }}</td>
                <td class="location-cell">{{ event.location }}</td>
                <td>
                  <button
                    @click="toggleFeatured(event)"
                    class="featured-toggle"
                    :class="{ active: event.is_featured }"
                  >
                    <svg v-if="event.is_featured" viewBox="0 0 24 24" fill="#f59e0b" stroke="#f59e0b" stroke-width="2">
                      <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                    <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                    {{ event.is_featured ? 'Featured' : 'Not Featured' }}
                  </button>
                </td>
                <td>
                  <div class="actions">
                    <button @click="openEditModal(event)" class="action-btn edit" title="Edit">
                      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                      </svg>
                    </button>
                    <button @click="confirmDelete(event)" class="action-btn delete" title="Delete">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="delete-icon">
                        <path d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64s14.3 32 32 32h384c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32l21.2 339C55.5 487.8 73.8 512 100 512h248c26.2 0 44.5-24.2 46.8-45l21.2-339zM160 192v224c0 8.8-7.2 16-16 16s-16-7.2-16-16V192c0-8.8 7.2-16 16-16s16 7.2 16 16zm96 0v224c0 8.8-7.2 16-16 16s-16-7.2-16-16V192c0-8.8 7.2-16 16-16s16 7.2 16 16zm96 0v224c0 8.8-7.2 16-16 16s-16-7.2-16-16V192c0-8.8 7.2-16 16-16s16 7.2 16 16z"/>
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>

          <div v-if="!loading && events.length === 0" class="no-data">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="4" width="18" height="18" rx="2"/>
              <line x1="16" y1="2" x2="16" y2="6"/>
              <line x1="8" y1="2" x2="8" y2="6"/>
              <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            <p>No events found</p>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="totalPages > 1" class="pagination">
          <button
            @click="changePage(currentPage - 1)"
            :disabled="currentPage === 1"
            class="page-btn"
          >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="15 18 9 12 15 6"/>
            </svg>
            Prev
          </button>
          <span class="page-info">Page {{ currentPage }} of {{ totalPages }}</span>
          <button
            @click="changePage(currentPage + 1)"
            :disabled="currentPage === totalPages"
            class="page-btn"
          >
            Next
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="9 18 15 12 9 6"/>
            </svg>
          </button>
        </div>
      </div>
    </main>

    <!-- Event Form Modal (Create/Edit) -->
    <div v-if="showFormModal" class="modal-overlay" @click.self="closeFormModal">
      <div class="modal-content form-modal">
        <div class="form-modal-header">
          <h3>{{ editingEvent ? 'Edit Event' : 'Create New Event' }}</h3>
          <button @click="closeFormModal" class="modal-close-btn">&times;</button>
        </div>

        <form @submit.prevent="handleFormSubmit" class="event-form-modal">
          <div v-if="formError" class="error-message">{{ formError }}</div>

          <div class="form-grid">
            <div class="form-group full-width">
              <label for="modal_name">Event Name *</label>
              <input type="text" id="modal_name" v-model="form.name" placeholder="Enter event name" :class="{ 'error': formErrors.name }" />
              <span v-if="formErrors.name" class="field-error">{{ formErrors.name }}</span>
            </div>

            <div class="form-group">
              <label for="modal_category">Category *</label>
              <select id="modal_category" v-model="form.category" :class="{ 'error': formErrors.category }">
                <option value="">Select category</option>
                <option value="Festival">Festival</option>
                <option value="Music">Music</option>
                <option value="Sports">Sports</option>
                <option value="Community">Community</option>
              </select>
              <span v-if="formErrors.category" class="field-error">{{ formErrors.category }}</span>
            </div>

            <div class="form-group">
              <label for="modal_date_time">Date & Time *</label>
              <input type="datetime-local" id="modal_date_time" v-model="form.date_time" :class="{ 'error': formErrors.date_time }" />
              <span v-if="formErrors.date_time" class="field-error">{{ formErrors.date_time }}</span>
            </div>

            <div class="form-group full-width">
              <label for="modal_location">Location *</label>
              <input type="text" id="modal_location" v-model="form.location" placeholder="Enter venue/location" :class="{ 'error': formErrors.location }" />
              <span v-if="formErrors.location" class="field-error">{{ formErrors.location }}</span>
            </div>

            <div class="form-group full-width">
              <label for="modal_description">Description *</label>
              <textarea id="modal_description" v-model="form.description" placeholder="Describe the event..." rows="4" :class="{ 'error': formErrors.description }"></textarea>
              <span v-if="formErrors.description" class="field-error">{{ formErrors.description }}</span>
            </div>

            <div class="form-group full-width">
              <label>Event Image</label>
              <div class="image-upload">
                <div v-if="imagePreview || currentImage" class="image-preview">
                  <img :src="imagePreview || `http://localhost:8000/storage/${currentImage}`" alt="Preview" />
                  <button type="button" @click="removeImage" class="remove-image">&times;</button>
                </div>
                <div v-else class="upload-placeholder" @click="$refs.formFileInput.click()">
                  <svg class="upload-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                    <circle cx="8.5" cy="8.5" r="1.5"/>
                    <path d="M21 15l-5-5L5 21"/>
                  </svg>
                  <span>Click to upload image</span>
                  <span class="upload-hint">JPEG, PNG, GIF, WEBP (max 2MB)</span>
                </div>
                <input type="file" ref="formFileInput" @change="handleImageChange" accept="image/jpeg,image/png,image/gif,image/webp" hidden />
              </div>
              <span v-if="formErrors.image" class="field-error">{{ formErrors.image }}</span>
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
            <button type="button" @click="closeFormModal" class="cancel-btn">Cancel</button>
            <button type="submit" class="submit-btn" :disabled="submitting">
              {{ submitting ? 'Saving...' : (editingEvent ? 'Update Event' : 'Create Event') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="deleteModal" class="modal-overlay" @click.self="deleteModal = null">
      <div class="modal-content delete-modal">
        <div class="modal-icon">
          <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
          </svg>
        </div>
        <h3>Delete Event</h3>
        <p>Are you sure you want to delete "{{ deleteModal.name }}"? This action cannot be undone.</p>
        <div class="modal-actions">
          <button @click="deleteModal = null" class="cancel-btn">Cancel</button>
          <button @click="deleteEvent" class="delete-confirm-btn" :disabled="deleting">
            <svg v-if="!deleting" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 448 512" style="fill: white;">
              <path d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64s14.3 32 32 32h384c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32l21.2 339C55.5 487.8 73.8 512 100 512h248c26.2 0 44.5-24.2 46.8-45l21.2-339zM160 192v224c0 8.8-7.2 16-16 16s-16-7.2-16-16V192c0-8.8 7.2-16 16-16s16 7.2 16 16zm96 0v224c0 8.8-7.2 16-16 16s-16-7.2-16-16V192c0-8.8 7.2-16 16-16s16 7.2 16 16zm96 0v224c0 8.8-7.2 16-16 16s-16-7.2-16-16V192c0-8.8 7.2-16 16-16s16 7.2 16 16z"/>
            </svg>
            {{ deleting ? 'Deleting...' : 'Delete' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import api from '../../api/axios';

const router = useRouter();
const route = useRoute();
const sidebarOpen = ref(false);

const events = ref([]);
const loading = ref(true);
const searchQuery = ref('');
const currentPage = ref(1);
const totalPages = ref(1);
const deleteModal = ref(null);
const deleting = ref(false);

const defaultImage = 'https://images.unsplash.com/photo-1533174072545-7a4b6ad7a6c3?w=100';

// Form modal state
const showFormModal = ref(false);
const editingEvent = ref(null);
const formFileInput = ref(null);
const submitting = ref(false);
const formError = ref('');
const formErrors = ref({});
const imageFile = ref(null);
const imagePreview = ref(null);
const currentImage = ref(null);

const form = ref({
  name: '',
  description: '',
  date_time: '',
  location: '',
  category: '',
  is_featured: false
});

const resetForm = () => {
  form.value = { name: '', description: '', date_time: '', location: '', category: '', is_featured: false };
  imageFile.value = null;
  imagePreview.value = null;
  currentImage.value = null;
  formError.value = '';
  formErrors.value = {};
  editingEvent.value = null;
};

const openCreateModal = () => {
  resetForm();
  showFormModal.value = true;
};

const openEditModal = async (event) => {
  resetForm();
  editingEvent.value = event;
  try {
    const response = await api.get(`/admin/events/${event.id}`);
    const ev = response.data;
    const date = new Date(ev.date_time);
    const formattedDate = date.toISOString().slice(0, 16);
    form.value = {
      name: ev.name,
      description: ev.description,
      date_time: formattedDate,
      location: ev.location,
      category: ev.category,
      is_featured: ev.is_featured
    };
    currentImage.value = ev.image;
  } catch (err) {
    formError.value = 'Failed to load event data';
  }
  showFormModal.value = true;
};

const closeFormModal = () => {
  showFormModal.value = false;
  resetForm();
};

const handleImageChange = (e) => {
  const file = e.target.files[0];
  if (file) {
    if (file.size > 2 * 1024 * 1024) {
      formErrors.value.image = 'Image size must be less than 2MB';
      return;
    }
    imageFile.value = file;
    const reader = new FileReader();
    reader.onload = (ev) => { imagePreview.value = ev.target.result; };
    reader.readAsDataURL(file);
    formErrors.value.image = null;
  }
};

const removeImage = () => {
  imageFile.value = null;
  imagePreview.value = null;
  currentImage.value = null;
};

const validateForm = () => {
  formErrors.value = {};
  if (!form.value.name.trim()) formErrors.value.name = 'Event name is required';
  if (!form.value.category) formErrors.value.category = 'Category is required';
  if (!form.value.date_time) formErrors.value.date_time = 'Date and time is required';
  if (!form.value.location.trim()) formErrors.value.location = 'Location is required';
  if (!form.value.description.trim()) formErrors.value.description = 'Description is required';
  return Object.keys(formErrors.value).length === 0;
};

const handleFormSubmit = async () => {
  if (!validateForm()) { formError.value = 'Please fix the errors above'; return; }
  submitting.value = true;
  formError.value = '';
  try {
    const formData = new FormData();
    formData.append('name', form.value.name);
    formData.append('description', form.value.description);
    formData.append('date_time', form.value.date_time);
    formData.append('location', form.value.location);
    formData.append('category', form.value.category);
    formData.append('is_featured', form.value.is_featured ? '1' : '0');
    if (imageFile.value) formData.append('image', imageFile.value);

    if (editingEvent.value) {
      await api.post(`/admin/events/${editingEvent.value.id}`, formData, { headers: { 'Content-Type': 'multipart/form-data' } });
    } else {
      await api.post('/admin/events', formData, { headers: { 'Content-Type': 'multipart/form-data' } });
    }
    closeFormModal();
    fetchEvents();
  } catch (err) {
    if (err.response?.data?.errors) {
      formErrors.value = {};
      Object.keys(err.response.data.errors).forEach(key => {
        formErrors.value[key] = err.response.data.errors[key][0];
      });
      formError.value = 'Please fix the errors above';
    } else {
      formError.value = err.response?.data?.message || 'An error occurred. Please try again.';
    }
  } finally {
    submitting.value = false;
  }
};

let searchTimeout = null;

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    hour: 'numeric',
    minute: '2-digit'
  });
};

const getCategoryClass = (category) => {
  const categoryMap = {
    'Festival': 'festival',
    'Music': 'music',
    'Sports': 'sports',
    'Community': 'community'
  };
  return categoryMap[category] || 'default';
};

const debouncedSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    currentPage.value = 1;
    fetchEvents();
  }, 300);
};

const fetchEvents = async () => {
  loading.value = true;
  try {
    const params = {
      page: currentPage.value,
      per_page: 10,
    };

    if (searchQuery.value) {
      params.search = searchQuery.value;
    }

    const response = await api.get('/admin/events', { params });
    events.value = response.data.data;
    totalPages.value = response.data.last_page;
  } catch (error) {
    console.error('Error fetching events:', error);
  } finally {
    loading.value = false;
  }
};

const changePage = (page) => {
  currentPage.value = page;
  fetchEvents();
};

const toggleFeatured = async (event) => {
  try {
    const response = await api.patch(`/admin/events/${event.id}/toggle-featured`);
    event.is_featured = response.data.is_featured;
  } catch (error) {
    console.error('Error toggling featured:', error);
  }
};

const confirmDelete = (event) => {
  deleteModal.value = event;
};

const deleteEvent = async () => {
  deleting.value = true;
  try {
    await api.delete(`/admin/events/${deleteModal.value.id}`);
    events.value = events.value.filter(e => e.id !== deleteModal.value.id);
    deleteModal.value = null;
  } catch (error) {
    console.error('Error deleting event:', error);
  } finally {
    deleting.value = false;
  }
};

const logout = async () => {
  try {
    await api.post('/admin/logout');
  } catch (error) {
    console.error('Logout error:', error);
  } finally {
    localStorage.removeItem('admin_token');
    localStorage.removeItem('admin_user');
    router.push('/admin/login');
  }
};

onMounted(() => {
  fetchEvents();
  // Auto-open create modal if navigated with ?action=create (e.g., from Dashboard)
  if (route.query.action === 'create') {
    openCreateModal();
    // Clean up the URL
    router.replace({ path: '/admin/events' });
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

.add-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  background: linear-gradient(135deg, #0ea5e9, #0284c7);
  color: white;
  text-decoration: none;
  border: none;
  cursor: pointer;
  font-size: 1rem;
  border-radius: 10px;
  font-weight: 600;
  transition: all 0.3s ease;
}

.add-btn svg {
  width: 18px;
  height: 18px;
}

.add-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(14, 165, 233, 0.4);
}

.page-content {
  padding: 2rem;
}

/* Controls */
.controls {
  margin-bottom: 1.5rem;
}

.search-box {
  position: relative;
  max-width: 400px;
}

.search-box .search-icon {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  width: 20px;
  height: 20px;
  color: #94a3b8;
}

.search-box input {
  width: 100%;
  padding: 0.875rem 1rem 0.875rem 3rem;
  border: 2px solid #e2e8f0;
  border-radius: 10px;
  font-size: 1rem;
  background: white;
  color: #1e293b;
  transition: all 0.3s ease;
}

.search-box input::placeholder {
  color: #94a3b8;
}

.search-box input:focus {
  outline: none;
  border-color: #0ea5e9;
}

/* Table */
.table-wrapper {
  background: white;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
}

.loading {
  padding: 3rem;
  text-align: center;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #e2e8f0;
  border-top-color: #0ea5e9;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.events-table {
  width: 100%;
  border-collapse: collapse;
}

.events-table th,
.events-table td {
  padding: 1rem 1.25rem;
  text-align: left;
  border-bottom: 1px solid #e2e8f0;
}

.events-table th {
  font-size: 0.75rem;
  font-weight: 600;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  background: #f8fafc;
}

.events-table td {
  font-size: 0.9rem;
  color: #374151;
}

.events-table tbody tr:hover {
  background: #f8fafc;
}

.event-cell {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.event-thumb {
  width: 48px;
  height: 48px;
  border-radius: 8px;
  object-fit: cover;
}

.event-name {
  font-weight: 500;
}

.category-tag {
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
}

.category-tag.festival { background: #fef3c7; color: #b45309; }
.category-tag.music { background: #ede9fe; color: #7c3aed; }
.category-tag.sports { background: #dcfce7; color: #16a34a; }
.category-tag.community { background: #fce7f3; color: #db2777; }
.category-tag.default { background: #e0f2fe; color: #0284c7; }

.location-cell {
  max-width: 200px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.featured-toggle {
  display: flex;
  align-items: center;
  gap: 0.35rem;
  padding: 6px 14px;
  border: 2px solid #e2e8f0;
  border-radius: 20px;
  background: white;
  font-size: 0.75rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  color: #64748b;
}

.featured-toggle svg {
  width: 14px;
  height: 14px;
}

.featured-toggle.active {
  background: #fef3c7;
  border-color: #f59e0b;
  color: #b45309;
}

.actions {
  display: flex;
  gap: 0.5rem;
}

.action-btn {
  width: 38px;
  height: 38px;
  border: none;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
}

.action-btn svg {
  width: 20px;
  height: 20px;
}

.action-btn.edit {
  background: #dbeafe;
  color: #0ea5e9;
  width: 48px;
}

.action-btn.edit svg {
  stroke: #0ea5e9;
  width: 48px;


}

.action-btn.edit:hover {
  background: #0ea5e9;
  color: white;
}

.action-btn.edit:hover svg {
  stroke: white;
}

.action-btn.delete {
  background: #fee2e2;
  color: #ef4444;
  width: 48px;

}

.action-btn.delete .delete-icon {
  fill: #ef4444;
  width: 22px;
  height: 22px;
}

.action-btn.delete:hover {
  background: #ef4444;
  color: white;
}

.action-btn.delete:hover .delete-icon {
  fill: white;
}

.no-data {
  padding: 4rem;
  text-align: center;
  color: #64748b;
}

.no-data svg {
  width: 48px;
  height: 48px;
  margin-bottom: 1rem;
  opacity: 0.5;
}

.no-data p {
  margin: 0;
  font-size: 1rem;
}

/* Pagination */
.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1rem;
  margin-top: 1.5rem;
}

.page-btn {
  display: flex;
  align-items: center;
  gap: 0.35rem;
  padding: 0.5rem 1rem;
  border: 2px solid #0ea5e9;
  background: white;
  color: #0ea5e9;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.page-btn svg {
  width: 16px;
  height: 16px;
}

.page-btn:hover:not(:disabled) {
  background: #0ea5e9;
  color: white;
}

.page-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-info {
  color: #64748b;
  font-size: 0.9rem;
}

/* Modal */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 1rem;
}

.modal-content {
  background: white;
  border-radius: 16px;
  padding: 2rem;
  max-width: 400px;
  width: 100%;
  text-align: center;
}

/* Event Form Modal */
.form-modal {
  max-width: 700px;
  max-height: 90vh;
  overflow-y: auto;
  text-align: left;
}

.form-modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid #e5e7eb;
}

.form-modal-header h3 {
  font-size: 1.35rem;
  font-weight: 700;
  color: #1e293b;
  margin: 0;
}

.modal-close-btn {
  width: 36px;
  height: 36px;
  border: none;
  background: #f1f5f9;
  border-radius: 50%;
  font-size: 1.5rem;
  color: #64748b;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
  line-height: 1;
}

.modal-close-btn:hover {
  background: #e2e8f0;
  color: #1e293b;
}

.event-form-modal .error-message {
  background: #fef2f2;
  color: #dc2626;
  padding: 0.75rem 1rem;
  border-radius: 10px;
  margin-bottom: 1rem;
  font-size: 0.9rem;
}

.event-form-modal .form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.25rem;
}

.event-form-modal .form-group {
  display: flex;
  flex-direction: column;
}

.event-form-modal .form-group.full-width {
  grid-column: span 2;
}

.event-form-modal .form-group label {
  font-weight: 500;
  color: #374151;
  margin-bottom: 0.4rem;
  font-size: 0.9rem;
}

.event-form-modal .form-group input,
.event-form-modal .form-group select,
.event-form-modal .form-group textarea {
  padding: 0.75rem 0.875rem;
  border: 2px solid #e5e7eb;
  border-radius: 10px;
  font-size: 0.95rem;
  background: white;
  color: #1e293b;
  transition: all 0.3s ease;
}

.event-form-modal .form-group input::placeholder,
.event-form-modal .form-group textarea::placeholder {
  color: #94a3b8;
}

.event-form-modal .form-group input[type="datetime-local"]::-webkit-calendar-picker-indicator {
  cursor: pointer;
  filter: invert(20%) sepia(10%) saturate(500%) hue-rotate(180deg);
  opacity: 1;
  font-size: 1.2rem;
  padding: 4px;
}

.event-form-modal .form-group input:focus,
.event-form-modal .form-group select:focus,
.event-form-modal .form-group textarea:focus {
  outline: none;
  border-color: #0ea5e9;
  box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
}

.event-form-modal .form-group input.error,
.event-form-modal .form-group select.error,
.event-form-modal .form-group textarea.error {
  border-color: #ef4444;
}

.event-form-modal .field-error {
  color: #ef4444;
  font-size: 0.8rem;
  margin-top: 0.2rem;
}

.event-form-modal .form-group textarea {
  resize: vertical;
  min-height: 90px;
}

.event-form-modal .image-upload {
  border: 2px dashed #e5e7eb;
  border-radius: 10px;
  overflow: hidden;
}

.event-form-modal .upload-placeholder {
  padding: 1.5rem;
  text-align: center;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.4rem;
}

.event-form-modal .upload-placeholder:hover {
  background: #f8fafc;
}

.event-form-modal .upload-icon {
  width: 40px;
  height: 40px;
  color: #94a3b8;
}

.event-form-modal .upload-hint {
  font-size: 0.8rem;
  color: #94a3b8;
}

.event-form-modal .image-preview {
  position: relative;
}

.event-form-modal .image-preview img {
  width: 100%;
  max-height: 200px;
  object-fit: cover;
}

.event-form-modal .remove-image {
  position: absolute;
  top: 8px;
  right: 8px;
  width: 28px;
  height: 28px;
  border: none;
  background: rgba(0, 0, 0, 0.7);
  color: white;
  border-radius: 50%;
  font-size: 1.2rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  line-height: 1;
}

.event-form-modal .remove-image:hover {
  background: #ef4444;
}

.event-form-modal .checkbox-label {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  cursor: pointer;
  font-weight: 500;
  color: #374151;
}

.event-form-modal .checkbox-label input {
  display: none;
}

.event-form-modal .checkmark {
  width: 22px;
  height: 22px;
  border: 2px solid #e5e7eb;
  border-radius: 6px;
  transition: all 0.3s ease;
  position: relative;
  flex-shrink: 0;
}

.event-form-modal .checkbox-label input:checked + .checkmark {
  background: #0ea5e9;
  border-color: #0ea5e9;
}

.event-form-modal .checkbox-label input:checked + .checkmark::after {
  content: '\2713';
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  color: white;
  font-size: 0.8rem;
}

.event-form-modal .form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  margin-top: 1.5rem;
  padding-top: 1.25rem;
  border-top: 1px solid #e5e7eb;
}

.event-form-modal .cancel-btn {
  padding: 0.75rem 1.25rem;
  border: 2px solid #e5e7eb;
  background: white;
  border-radius: 10px;
  font-weight: 500;
  color: #64748b;
  cursor: pointer;
  transition: all 0.3s ease;
}

.event-form-modal .cancel-btn:hover {
  background: #f8fafc;
}

.event-form-modal .submit-btn {
  padding: 0.75rem 1.75rem;
  border: none;
  background: linear-gradient(135deg, #0ea5e9, #0284c7);
  color: white;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.event-form-modal .submit-btn:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 4px 15px rgba(14, 165, 233, 0.4);
}

.event-form-modal .submit-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.modal-icon {
  width: 56px;
  height: 56px;
  background: #fee2e2;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1rem;
}

.modal-icon svg {
  width: 28px;
  height: 28px;
  color: #ef4444;
}

.delete-modal h3 {
  font-size: 1.25rem;
  color: #1e293b;
  margin: 0 0 0.75rem 0;
}

.delete-modal p {
  color: #64748b;
  margin: 0 0 1.5rem 0;
  font-size: 0.95rem;
}

.modal-actions {
  display: flex;
  gap: 1rem;
  justify-content: center;
}

.cancel-btn {
  padding: 0.75rem 1.5rem;
  border: 2px solid #e2e8f0;
  background: white;
  border-radius: 10px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  color: #64748b;
}

.cancel-btn:hover {
  background: #f8fafc;
}

.delete-confirm-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  border: none;
  background: #ef4444;
  color: white;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.delete-confirm-btn svg {
  width: 18px;
  height: 18px;
}

.delete-confirm-btn:hover:not(:disabled) {
  background: #dc2626;
}

.delete-confirm-btn:disabled {
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

  .controls {
    flex-direction: column;
  }

  .events-table {
    font-size: 0.8rem;
  }

  .event-thumb {
    display: none;
  }

  .add-btn {
    padding: 0.5rem 1rem;
    font-size: 0.85rem;
  }

  .add-btn svg {
    width: 16px;
    height: 16px;
  }

  .form-modal {
    max-width: 95vw;
    max-height: 85vh;
    padding: 1.25rem;
  }

  .event-form-modal .form-grid {
    grid-template-columns: 1fr;
  }

  .event-form-modal .form-group.full-width {
    grid-column: span 1;
  }
}

@media (max-width: 480px) {
  .top-header {
    padding: 0.75rem 1rem;
  }

  .events-table th,
  .events-table td {
    padding: 0.5rem;
  }

  .action-btn {
    padding: 0.35rem 0.5rem;
  }

  .action-btn span {
    display: none;
  }
}
</style>
