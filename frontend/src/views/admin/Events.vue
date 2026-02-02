<template>
  <div class="admin-layout">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="sidebar-header">
        <span class="brand-icon">🌴</span>
        <span class="brand-text">Cebu Events</span>
      </div>

      <nav class="sidebar-nav">
        <router-link to="/admin" class="nav-item" exact-active-class="active">
          <span class="nav-icon">📊</span>
          Dashboard
        </router-link>
        <router-link to="/admin/events" class="nav-item" active-class="active">
          <span class="nav-icon">📅</span>
          Events
        </router-link>
      </nav>

      <div class="sidebar-footer">
        <router-link to="/" class="nav-item">
          <span class="nav-icon">🌐</span>
          View Website
        </router-link>
        <button @click="logout" class="nav-item logout-btn">
          <span class="nav-icon">🚪</span>
          Logout
        </button>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <header class="top-header">
        <h1>Events Management</h1>
        <router-link to="/admin/events/create" class="add-btn">
          + Add New Event
        </router-link>
      </header>

      <div class="page-content">
        <!-- Search & Filter -->
        <div class="controls">
          <div class="search-box">
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
                <td><span class="category-tag">{{ event.category }}</span></td>
                <td>{{ formatDate(event.date_time) }}</td>
                <td class="location-cell">{{ event.location }}</td>
                <td>
                  <button
                    @click="toggleFeatured(event)"
                    class="featured-toggle"
                    :class="{ active: event.is_featured }"
                  >
                    {{ event.is_featured ? '⭐ Yes' : '☆ No' }}
                  </button>
                </td>
                <td>
                  <div class="actions">
                    <router-link :to="`/admin/events/${event.id}/edit`" class="action-btn edit">
                      ✏️
                    </router-link>
                    <button @click="confirmDelete(event)" class="action-btn delete">
                      🗑️
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>

          <div v-if="!loading && events.length === 0" class="no-data">
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
            ← Prev
          </button>
          <span class="page-info">Page {{ currentPage }} of {{ totalPages }}</span>
          <button
            @click="changePage(currentPage + 1)"
            :disabled="currentPage === totalPages"
            class="page-btn"
          >
            Next →
          </button>
        </div>
      </div>
    </main>

    <!-- Delete Confirmation Modal -->
    <div v-if="deleteModal" class="modal-overlay" @click.self="deleteModal = null">
      <div class="modal-content delete-modal">
        <h3>Delete Event</h3>
        <p>Are you sure you want to delete "{{ deleteModal.name }}"? This action cannot be undone.</p>
        <div class="modal-actions">
          <button @click="deleteModal = null" class="cancel-btn">Cancel</button>
          <button @click="deleteEvent" class="delete-confirm-btn" :disabled="deleting">
            {{ deleting ? 'Deleting...' : 'Delete' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '../../api/axios';

const router = useRouter();

const events = ref([]);
const loading = ref(true);
const searchQuery = ref('');
const currentPage = ref(1);
const totalPages = ref(1);
const deleteModal = ref(null);
const deleting = ref(false);

const defaultImage = 'https://images.unsplash.com/photo-1533174072545-7a4b6ad7a6c3?w=100';

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
  font-size: 1.75rem;
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
  font-size: 1.1rem;
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

.add-btn {
  padding: 0.75rem 1.5rem;
  background: linear-gradient(135deg, #0ea5e9, #0284c7);
  color: white;
  text-decoration: none;
  border-radius: 10px;
  font-weight: 600;
  transition: all 0.3s ease;
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

.search-box input {
  width: 100%;
  max-width: 400px;
  padding: 0.875rem 1rem;
  border: 2px solid #e2e8f0;
  border-radius: 10px;
  font-size: 1rem;
  background: white;
  transition: all 0.3s ease;
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
  background: #e0f2fe;
  color: #0284c7;
  padding: 4px 12px;
  border-radius: 15px;
  font-size: 0.75rem;
  font-weight: 500;
}

.location-cell {
  max-width: 200px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.featured-toggle {
  padding: 4px 12px;
  border: 2px solid #e2e8f0;
  border-radius: 15px;
  background: white;
  font-size: 0.75rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.featured-toggle.active {
  background: #fef3c7;
  border-color: #f59e0b;
  color: #d97706;
}

.actions {
  display: flex;
  gap: 0.5rem;
}

.action-btn {
  width: 36px;
  height: 36px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1rem;
  text-decoration: none;
}

.action-btn.edit {
  background: #e0f2fe;
}

.action-btn.edit:hover {
  background: #0ea5e9;
}

.action-btn.delete {
  background: #fee2e2;
}

.action-btn.delete:hover {
  background: #ef4444;
}

.no-data {
  padding: 3rem;
  text-align: center;
  color: #64748b;
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
  padding: 0.5rem 1rem;
  border: 2px solid #0ea5e9;
  background: white;
  color: #0ea5e9;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
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
}

.delete-modal h3 {
  font-size: 1.25rem;
  color: #1e293b;
  margin: 0 0 1rem 0;
}

.delete-modal p {
  color: #64748b;
  margin: 0 0 1.5rem 0;
}

.modal-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
}

.cancel-btn {
  padding: 0.75rem 1.5rem;
  border: 2px solid #e2e8f0;
  background: white;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.cancel-btn:hover {
  background: #f8fafc;
}

.delete-confirm-btn {
  padding: 0.75rem 1.5rem;
  border: none;
  background: #ef4444;
  color: white;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.delete-confirm-btn:hover:not(:disabled) {
  background: #dc2626;
}

.delete-confirm-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

@media (max-width: 768px) {
  .sidebar {
    display: none;
  }

  .main-content {
    margin-left: 0;
  }

  .events-table {
    font-size: 0.8rem;
  }

  .event-thumb {
    display: none;
  }
}
</style>
