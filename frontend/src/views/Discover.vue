<template>
  <div class="discover-page">
    <div class="page-header">
      <div class="container">
        <h1>Discover Events</h1>
        <p>Find the perfect event that matches your interests</p>
      </div>
    </div>

    <div class="container">
      <!-- Filters -->
      <div class="filters-section">
        <div class="search-box">
          <input
            type="text"
            v-model="searchQuery"
            placeholder="Search events..."
            @input="debouncedSearch"
          />
          <span class="search-icon">🔍</span>
        </div>

        <div class="category-filters">
          <button
            v-for="category in allCategories"
            :key="category"
            class="category-btn"
            :class="{ active: selectedCategory === category }"
            @click="selectCategory(category)"
          >
            {{ category }}
          </button>
        </div>
      </div>

      <!-- Featured Events -->
      <div v-if="!searchQuery && selectedCategory === 'all'" class="featured-section">
        <h2>⭐ Featured Events</h2>
        <div class="events-grid featured">
          <EventCard
            v-for="event in featuredEvents"
            :key="event.id"
            :event="event"
          />
        </div>
      </div>

      <!-- All Events -->
      <div class="events-section">
        <h2>{{ sectionTitle }}</h2>

        <div v-if="loading" class="loading">
          <div class="spinner"></div>
          <p>Loading events...</p>
        </div>

        <div v-else-if="events.length === 0" class="no-events">
          <span class="no-events-icon">📭</span>
          <h3>No events found</h3>
          <p>Try adjusting your search or filter criteria</p>
        </div>

        <div v-else class="events-grid">
          <EventCard
            v-for="event in events"
            :key="event.id"
            :event="event"
          />
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
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../api/axios';
import EventCard from '../components/EventCard.vue';

const route = useRoute();
const router = useRouter();

const events = ref([]);
const featuredEvents = ref([]);
const loading = ref(true);
const searchQuery = ref('');
const selectedCategory = ref('all');
const currentPage = ref(1);
const totalPages = ref(1);

const allCategories = ['All', 'Festival', 'Music', 'Sports' , 'Community'];

const sectionTitle = computed(() => {
  if (searchQuery.value) {
    return `Search results for "${searchQuery.value}"`;
  }
  if (selectedCategory.value !== 'all') {
    return `${selectedCategory.value} Events`;
  }
  return 'Upcoming Events';
});

let searchTimeout = null;

const debouncedSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    currentPage.value = 1;
    fetchEvents();
  }, 300);
};

const selectCategory = (category) => {
  selectedCategory.value = category;
  currentPage.value = 1;
  fetchEvents();
};

const changePage = (page) => {
  currentPage.value = page;
  fetchEvents();
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

const fetchEvents = async () => {
  loading.value = true;
  try {
    const params = {
      page: currentPage.value,
      per_page: 9,
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

const fetchFeaturedEvents = async () => {
  try {
    const response = await api.get('/events/featured');
    featuredEvents.value = response.data;
  } catch (error) {
    console.error('Error fetching featured events:', error);
  }
};

onMounted(() => {
  // Check for category in URL
  if (route.query.category) {
    selectedCategory.value = route.query.category;
  }

  fetchEvents();
  fetchFeaturedEvents();
});

watch(() => route.query.category, (newCategory) => {
  if (newCategory) {
    selectedCategory.value = newCategory;
    fetchEvents();
  }
});
</script>

<style scoped>
.discover-page {
  min-height: 100vh;
  padding-top: 80px;
}

.page-header {
  background: linear-gradient(135deg, #0ea5e9, #0284c7);
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
  opacity: 0.9;
  font-size: 1.1rem;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
}

/* Filters */
.filters-section {
  padding: 2rem 0;
  border-bottom: 1px solid #e2e8f0;
}

.search-box {
  position: relative;
  max-width: 500px;
  margin: 0 auto 1.5rem;
}

.search-box input {
  width: 100%;
  padding: 1rem 1rem 1rem 3rem;
  border: 2px solid #e2e8f0;
  border-radius: 12px;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.search-box input:focus {
  outline: none;
  border-color: #0ea5e9;
  box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1);
}

.search-icon {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  font-size: 1.1rem;
}

.category-filters {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
  justify-content: center;
}

.category-btn {
  padding: 0.5rem 1.25rem;
  border: 2px solid #e2e8f0;
  background: white;
  border-radius: 25px;
  font-size: 0.9rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  color: #64748b;
}

.category-btn:hover {
  border-color: #0ea5e9;
  color: #0ea5e9;
}

.category-btn.active {
  background: #0ea5e9;
  border-color: #0ea5e9;
  color: white;
}

/* Featured Section */
.featured-section {
  padding: 3rem 0 2rem;
}

.featured-section h2 {
  font-size: 1.5rem;
  font-weight: 600;
  color: #1e293b;
  margin-bottom: 1.5rem;
}

/* Events Section */
.events-section {
  padding: 2rem 0 4rem;
}

.events-section h2 {
  font-size: 1.5rem;
  font-weight: 600;
  color: #1e293b;
  margin-bottom: 1.5rem;
}

.events-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 2rem;
}

.events-grid.featured {
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
}

/* Loading */
.loading {
  text-align: center;
  padding: 4rem;
}

.spinner {
  width: 50px;
  height: 50px;
  border: 4px solid #f1f5f9;
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
  gap: 1.5rem;
  margin-top: 3rem;
}

.page-btn {
  padding: 0.75rem 1.5rem;
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
  font-weight: 500;
}

@media (max-width: 768px) {
  .discover-page {
    padding-top: 60px;
  }

  .page-header {
    padding: 2.5rem 0;
  }

  .page-header h1 {
    font-size: 2rem;
  }

  .page-header p {
    font-size: 1rem;
  }

  .container {
    padding: 0 1rem;
  }

  .search-box {
    margin-bottom: 1rem;
  }

  .category-filters {
    gap: 0.5rem;
  }

  .category-btn {
    padding: 0.4rem 1rem;
    font-size: 0.8rem;
  }

  .events-grid {
    grid-template-columns: 1fr;
  }

  .events-grid.featured {
    grid-template-columns: 1fr;
  }

  .featured-section h2,
  .events-section h2 {
    font-size: 1.25rem;
  }

  .pagination {
    gap: 1rem;
    flex-wrap: wrap;
  }

  .page-btn {
    padding: 0.5rem 1rem;
    font-size: 0.85rem;
  }
}

@media (max-width: 480px) {
  .page-header h1 {
    font-size: 1.6rem;
  }

  .category-btn {
    padding: 0.35rem 0.75rem;
    font-size: 0.75rem;
  }
}
</style>
