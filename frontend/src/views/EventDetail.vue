<template>
  <div class="event-detail-page">
    <div v-if="loading" class="loading-page">
      <div class="spinner"></div>
      <p>Loading event...</p>
    </div>

    <div v-else-if="!event" class="not-found">
      <h1>Event Not Found</h1>
      <p>The event you're looking for doesn't exist.</p>
      <router-link to="/events" class="back-btn">← Back to Events</router-link>
    </div>

    <template v-else>
      <!-- Hero Image -->
      <div class="event-hero">
        <img
          :src="event.image ? `http://localhost:8000/storage/${event.image}` : defaultImage"
          :alt="event.name"
        />
        <div class="hero-overlay"></div>
        <router-link to="/events" class="back-button">← Back to Events</router-link>
      </div>

      <div class="event-content">
        <div class="container">
          <div class="event-main">
            <!-- Event Header -->
            <div class="event-header">
              <div class="event-badges">
                <span class="category-badge">{{ event.category }}</span>
                <span v-if="event.is_featured" class="featured-badge">⭐ Featured</span>
              </div>
              <h1>{{ event.name }}</h1>
            </div>

            <!-- Event Info Cards -->
            <div class="info-cards">
              <div class="info-card">
                <span class="info-icon">📅</span>
                <div class="info-content">
                  <span class="info-label">Date & Time</span>
                  <span class="info-value">{{ formatDateTime(event.date_time) }}</span>
                </div>
              </div>

              <div class="info-card">
                <span class="info-icon">📍</span>
                <div class="info-content">
                  <span class="info-label">Location</span>
                  <span class="info-value">{{ event.location }}</span>
                </div>
              </div>
            </div>

            <!-- Event Description -->
            <div class="event-description">
              <h2>About This Event</h2>
              <p>{{ event.description }}</p>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="event-sidebar">
            <div class="sidebar-card">
              <h3>Event Details</h3>
              <ul class="details-list">
                <li>
                  <span class="detail-label">Category</span>
                  <span class="detail-value">{{ event.category }}</span>
                </li>
                <li>
                  <span class="detail-label">Date</span>
                  <span class="detail-value">{{ formatDate(event.date_time) }}</span>
                </li>
                <li>
                  <span class="detail-label">Time</span>
                  <span class="detail-value">{{ formatTime(event.date_time) }}</span>
                </li>
                <li>
                  <span class="detail-label">Venue</span>
                  <span class="detail-value">{{ event.location }}</span>
                </li>
              </ul>
            </div>

            <div class="sidebar-card share-card">
              <h3>Share This Event</h3>
              <div class="share-buttons">
                <button @click="shareEvent('facebook')" class="share-btn facebook">
                  Facebook
                </button>
                <button @click="shareEvent('twitter')" class="share-btn twitter">
                  Twitter
                </button>
                <button @click="copyLink" class="share-btn copy">
                  {{ copied ? 'Copied!' : 'Copy Link' }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import api from '../api/axios';

const route = useRoute();
const event = ref(null);
const loading = ref(true);
const copied = ref(false);

const defaultImage = 'https://images.unsplash.com/photo-1533174072545-7a4b6ad7a6c3?w=1920';

const formatDateTime = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    weekday: 'long',
    month: 'long',
    day: 'numeric',
    year: 'numeric',
    hour: 'numeric',
    minute: '2-digit'
  });
};

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    month: 'long',
    day: 'numeric',
    year: 'numeric'
  });
};

const formatTime = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleTimeString('en-US', {
    hour: 'numeric',
    minute: '2-digit'
  });
};

const shareEvent = (platform) => {
  const url = window.location.href;
  const text = `Check out ${event.value.name} in Cebu!`;

  let shareUrl = '';
  if (platform === 'facebook') {
    shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;
  } else if (platform === 'twitter') {
    shareUrl = `https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}&url=${encodeURIComponent(url)}`;
  }

  if (shareUrl) {
    window.open(shareUrl, '_blank', 'width=600,height=400');
  }
};

const copyLink = async () => {
  try {
    await navigator.clipboard.writeText(window.location.href);
    copied.value = true;
    setTimeout(() => {
      copied.value = false;
    }, 2000);
  } catch (err) {
    console.error('Failed to copy:', err);
  }
};

onMounted(async () => {
  try {
    const response = await api.get(`/events/${route.params.id}`);
    event.value = response.data;
  } catch (error) {
    console.error('Error fetching event:', error);
  } finally {
    loading.value = false;
  }
});
</script>

<style scoped>
.event-detail-page {
  min-height: 100vh;
  background: #f8fafc;
}

.loading-page,
.not-found {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 2rem;
}

.spinner {
  width: 50px;
  height: 50px;
  border: 4px solid #e2e8f0;
  border-top-color: #0ea5e9;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.not-found h1 {
  font-size: 2rem;
  color: #1e293b;
  margin-bottom: 0.5rem;
}

.not-found p {
  color: #64748b;
  margin-bottom: 2rem;
}

.back-btn {
  padding: 0.75rem 1.5rem;
  background: #0ea5e9;
  color: white;
  text-decoration: none;
  border-radius: 8px;
  font-weight: 500;
}

/* Hero */
.event-hero {
  position: relative;
  height: 50vh;
  min-height: 400px;
}

.event-hero img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.hero-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to bottom, rgba(0,0,0,0.3), rgba(0,0,0,0.5));
}

.back-button {
  position: absolute;
  top: 100px;
  left: 2rem;
  color: white;
  text-decoration: none;
  font-weight: 500;
  padding: 0.5rem 1rem;
  background: rgba(0, 0, 0, 0.3);
  border-radius: 8px;
  backdrop-filter: blur(10px);
  transition: all 0.3s ease;
}

.back-button:hover {
  background: rgba(0, 0, 0, 0.5);
}

/* Content */
.event-content {
  margin-top: -100px;
  position: relative;
  z-index: 10;
  padding-bottom: 4rem;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
  display: grid;
  grid-template-columns: 1fr 350px;
  gap: 2rem;
}

/* Main Content */
.event-main {
  background: white;
  border-radius: 20px;
  padding: 2.5rem;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
}

.event-header {
  margin-bottom: 2rem;
}

.event-badges {
  display: flex;
  gap: 0.75rem;
  margin-bottom: 1rem;
}

.category-badge {
  background: #e0f2fe;
  color: #0284c7;
  padding: 6px 16px;
  border-radius: 25px;
  font-size: 0.875rem;
  font-weight: 600;
}

.featured-badge {
  background: linear-gradient(135deg, #f59e0b, #d97706);
  color: white;
  padding: 6px 16px;
  border-radius: 25px;
  font-size: 0.875rem;
  font-weight: 600;
}

.event-header h1 {
  font-size: 2.25rem;
  font-weight: 700;
  color: #1e293b;
  line-height: 1.3;
}

/* Info Cards */
.info-cards {
  display: flex;
  gap: 1.5rem;
  margin-bottom: 2rem;
  flex-wrap: wrap;
}

.info-card {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  flex: 1;
  min-width: 200px;
  padding: 1.25rem;
  background: #f8fafc;
  border-radius: 12px;
}

.info-icon {
  font-size: 1.5rem;
}

.info-content {
  display: flex;
  flex-direction: column;
}

.info-label {
  font-size: 0.75rem;
  color: #64748b;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.info-value {
  font-size: 1rem;
  color: #1e293b;
  font-weight: 600;
}

/* Description */
.event-description h2 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1e293b;
  margin-bottom: 1rem;
}

.event-description p {
  color: #475569;
  line-height: 1.8;
  font-size: 1rem;
  white-space: pre-line;
}

/* Sidebar */
.event-sidebar {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.sidebar-card {
  background: white;
  border-radius: 16px;
  padding: 1.5rem;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.sidebar-card h3 {
  font-size: 1rem;
  font-weight: 600;
  color: #1e293b;
  margin-bottom: 1.25rem;
  padding-bottom: 0.75rem;
  border-bottom: 1px solid #e2e8f0;
}

.details-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.details-list li {
  display: flex;
  justify-content: space-between;
  padding: 0.75rem 0;
  border-bottom: 1px solid #f1f5f9;
}

.details-list li:last-child {
  border-bottom: none;
}

.detail-label {
  color: #64748b;
  font-size: 0.875rem;
}

.detail-value {
  color: #1e293b;
  font-weight: 500;
  font-size: 0.875rem;
  text-align: right;
  max-width: 60%;
}

/* Share */
.share-buttons {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.share-btn {
  padding: 0.75rem;
  border: none;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.share-btn.facebook {
  background: #1877f2;
  color: white;
}

.share-btn.twitter {
  background: #1da1f2;
  color: white;
}

.share-btn.copy {
  background: #f1f5f9;
  color: #475569;
}

.share-btn:hover {
  transform: translateY(-2px);
  opacity: 0.9;
}

@media (max-width: 900px) {
  .container {
    grid-template-columns: 1fr;
    padding: 0 1rem;
  }

  .event-sidebar {
    flex-direction: row;
    flex-wrap: wrap;
  }

  .sidebar-card {
    flex: 1;
    min-width: 250px;
  }
}

@media (max-width: 600px) {
  .event-hero {
    height: 35vh;
    min-height: 250px;
  }

  .back-button {
    top: 70px;
    left: 1rem;
    font-size: 0.875rem;
    padding: 0.4rem 0.75rem;
  }

  .event-content {
    margin-top: -60px;
  }

  .event-main {
    padding: 1.25rem;
    border-radius: 16px;
  }

  .event-header h1 {
    font-size: 1.5rem;
  }

  .event-badges {
    gap: 0.5rem;
  }

  .category-badge,
  .featured-badge {
    padding: 4px 10px;
    font-size: 0.75rem;
  }

  .info-cards {
    flex-direction: column;
    gap: 0.75rem;
  }

  .info-card {
    min-width: auto;
    padding: 1rem;
  }

  .event-sidebar {
    flex-direction: column;
  }

  .sidebar-card {
    min-width: auto;
  }

  .share-buttons {
    flex-direction: column;
  }

  .event-description h2 {
    font-size: 1.1rem;
  }

  .event-description p {
    font-size: 0.9rem;
  }
}
</style>
