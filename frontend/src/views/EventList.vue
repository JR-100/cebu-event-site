<template>
  <div class="event-list-page">
    <div class="page-header">
      <div class="container">
        <h1>Event List</h1>
        <p>Browse all upcoming events in Cebu</p>
      </div>
    </div>

    <div class="container">
      <div class="list-controls">
        <div class="search-box">
          <input
            type="text"
            v-model="searchQuery"
            placeholder="Search events..."
            @input="debouncedSearch"
          />
        </div>

        <select v-model="selectedCategory" @change="fetchEvents" class="category-select">
          <option value="all">All Categories</option>
          <option v-for="category in categories" :key="category" :value="category">
            {{ category }}
          </option>
        </select>
      </div>

      <div v-if="loading" class="loading">
        <div class="spinner"></div>
        <p>Loading events...</p>
      </div>

      <div v-else-if="events.length === 0" class="no-events">
        <span class="no-events-icon">📭</span>
        <h3>No events found</h3>
        <p>Check back later for upcoming events</p>
      </div>

      <div v-else class="events-list">
        <div
          v-for="event in events"
          :key="event.id"
          class="event-list-item"
        >
          <div class="event-image">
            <img
              :src="event.image ? `http://localhost:8000/storage/${event.image}` : defaultImage"
              :alt="event.name"
            />
            <span v-if="event.is_featured" class="featured-badge">⭐ Featured</span>
          </div>

          <div class="event-details">
            <div class="event-meta">
              <span class="event-category">{{ event.category }}</span>
              <span class="event-date">
                📅 {{ formatDate(event.date_time) }}
              </span>
            </div>

            <h3 class="event-title">{{ event.name }}</h3>

            <p class="event-location">
              📍 {{ event.location }}
            </p>

            <p class="event-description">{{ truncateText(event.description, 150) }}</p>
          </div>

          <div class="event-action">
            <router-link :to="`/events/${event.id}`" class="view-btn">
              View Details
            </router-link>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="totalPages > 1" class="pagination">
        <button
          @click="changePage(currentPage - 1)"
          :disabled="currentPage === 1"
          class="page-btn"
        >
          ← Previous
        </button>

        <div class="page-numbers">
          <button
            v-for="page in visiblePages"
            :key="page"
            @click="changePage(page)"
            class="page-number"
            :class="{ active: currentPage === page }"
          >
            {{ page }}
          </button>
        </div>

        <button
          @click="changePage(currentPage + 1)"
          :disabled="currentPage === totalPages"
          class="page-btn"
        >
          Next →
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import api from '../api/axios';

const events = ref([]);
const loading = ref(true);
const searchQuery = ref('');
const selectedCategory = ref('all');
const currentPage = ref(1);
const totalPages = ref(1);
const categories = ref([]);

const defaultImage = 'https://images.unsplash.com/photo-1533174072545-7a4b6ad7a6c3?w=800';

let searchTimeout = null;

const visiblePages = computed(() => {
  const pages = [];
  const start = Math.max(1, currentPage.value - 2);
  const end = Math.min(totalPages.value, currentPage.value + 2);

  for (let i = start; i <= end; i++) {
    pages.push(i);
  }
  return pages;
});

const debouncedSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    currentPage.value = 1;
    fetchEvents();
  }, 300);
};

const changePage = (page) => {
  currentPage.value = page;
  fetchEvents();
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    weekday: 'short',
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    hour: 'numeric',
    minute: '2-digit'
  });
};

const truncateText = (text, length) => {
  if (text.length > length) {
    return text.substring(0, length) + '...';
  }
  return text;
};

const fetchEvents = async () => {
  loading.value = true;
  try {
    const params = {
      page: currentPage.value,
      per_page: 10,
    };

    if (selectedCategory.value !== 'all') {
      params.category = selectedCategory.value;
    }

    if (searchQuery.value) {
      params.search = searchQuery.value;
    }

    const response = await api.get('/events', { params });
    events.value = response.data.data;
    totalPages.value = response.data.last_page;
  } catch (error) {
    console.error('Error fetching events:', error);
  } finally {
    loading.value = false;
  }
};

const fetchCategories = async () => {
  try {
    const response = await api.get('/events/categories');
    categories.value = response.data;
  } catch (error) {
    console.error('Error fetching categories:', error);
  }
};

onMounted(() => {
  fetchEvents();
  fetchCategories();
});
</script>

<style scoped>
.event-list-page {
  min-height: 100vh;
  padding-top: 80px;
  background: #f8fafc;
}

.page-header {
  background: linear-gradient(135deg, #1e293b, #0f172a);
  padding: 4rem 0;
  color: white;
  text-align: center;
}

.page-header h1 {
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
}

.page-header p {
  opacity: 0.8;
  font-size: 1.1rem;
}

.container {
  max-width: 1000px;
  margin: 0 auto;
  padding: 0 2rem;
}

.list-controls {
  display: flex;
  gap: 1rem;
  padding: 2rem 0;
  flex-wrap: wrap;
}

.search-box {
  flex: 1;
  min-width: 250px;
}

.search-box input {
  width: 100%;
  padding: 0.875rem 1.25rem;
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

.category-select {
  padding: 0.875rem 1.25rem;
  border: 2px solid #e2e8f0;
  border-radius: 10px;
  font-size: 1rem;
  background: white;
  cursor: pointer;
  min-width: 180px;
}

.category-select:focus {
  outline: none;
  border-color: #0ea5e9;
}

/* Events List */
.events-list {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  padding-bottom: 3rem;
}

.event-list-item {
  display: flex;
  background: white;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
}

.event-list-item:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.event-image {
  width: 250px;
  min-height: 200px;
  position: relative;
  flex-shrink: 0;
}

.event-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.featured-badge {
  position: absolute;
  top: 12px;
  left: 12px;
  background: linear-gradient(135deg, #f59e0b, #d97706);
  color: white;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
}

.event-details {
  flex: 1;
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
}

.event-meta {
  display: flex;
  gap: 1rem;
  margin-bottom: 0.75rem;
  flex-wrap: wrap;
}

.event-category {
  background: #e0f2fe;
  color: #0284c7;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
}

.event-date {
  color: #64748b;
  font-size: 0.875rem;
}

.event-title {
  font-size: 1.25rem;
  font-weight: 700;
  color: #1e293b;
  margin: 0 0 0.5rem 0;
}

.event-location {
  color: #64748b;
  font-size: 0.9rem;
  margin: 0 0 0.75rem 0;
}

.event-description {
  color: #64748b;
  font-size: 0.9rem;
  line-height: 1.6;
  margin: 0;
  flex: 1;
}

.event-action {
  display: flex;
  align-items: center;
  padding: 1.5rem;
}

.view-btn {
  padding: 0.75rem 1.5rem;
  background: linear-gradient(135deg, #0ea5e9, #0284c7);
  color: white;
  text-decoration: none;
  border-radius: 10px;
  font-weight: 600;
  transition: all 0.3s ease;
  white-space: nowrap;
}

.view-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(14, 165, 233, 0.4);
}

/* Loading */
.loading {
  text-align: center;
  padding: 4rem;
}

.spinner {
  width: 50px;
  height: 50px;
  border: 4px solid #e2e8f0;
  border-top-color: #0ea5e9;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 1rem;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* No Events */
.no-events {
  text-align: center;
  padding: 4rem;
  background: white;
  border-radius: 16px;
}

.no-events-icon {
  font-size: 4rem;
  display: block;
  margin-bottom: 1rem;
}

.no-events h3 {
  font-size: 1.5rem;
  color: #1e293b;
  margin-bottom: 0.5rem;
}

.no-events p {
  color: #64748b;
}

/* Pagination */
.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1rem;
  padding: 2rem 0 4rem;
}

.page-btn {
  padding: 0.75rem 1.25rem;
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

.page-numbers {
  display: flex;
  gap: 0.5rem;
}

.page-number {
  width: 40px;
  height: 40px;
  border: 2px solid #e2e8f0;
  background: white;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-weight: 500;
}

.page-number:hover {
  border-color: #0ea5e9;
  color: #0ea5e9;
}

.page-number.active {
  background: #0ea5e9;
  border-color: #0ea5e9;
  color: white;
}

@media (max-width: 768px) {
  .event-list-item {
    flex-direction: column;
  }

  .event-image {
    width: 100%;
    height: 200px;
  }

  .event-action {
    padding: 0 1.5rem 1.5rem;
  }

  .view-btn {
    width: 100%;
    text-align: center;
  }
}
</style>
