<template>
  <div class="home">
    <!-- Hero Section -->
    <section class="hero">
      <div class="hero-overlay"></div>
      <div class="hero-content">
        <h1>Cebu Event Site</h1>
      </div>
    </section>

    <!-- Discover Intro Section -->
    <section class="discover-intro">
      <div class="container">
        <h2>Discover Amazing Events</h2>
        <p>Find and explore exciting events happening in Cebu. From vibrant festivals to live music, sports competitions, and community gatherings, there's always something amazing to experience in our beautiful island province.</p>
      </div>
    </section>

    <!-- Featured Events Section -->
    <section class="featured-section">
      <div class="container">
        <div class="section-header">
          <h2>Featured Events</h2>
          <p>Don't miss these highlighted experiences</p>
        </div>

        <div v-if="loading" class="loading">
          <div class="spinner"></div>
          <p>Loading events...</p>
        </div>

        <div v-else class="events-grid">
          <EventCard
            v-for="event in featuredEvents"
            :key="event.id"
            :event="event"
          />
        </div>

        <div class="section-footer">
          <router-link to="/events" class="btn btn-secondary">View All Events →</router-link>
        </div>
      </div>
    </section>

    <!-- Categories Section -->
    <section class="categories-section">
      <div class="container">
        <div class="section-header">
          <h2>Explore by Category</h2>
          <p>Find events that match your interests</p>
        </div>

        <div class="categories-grid">
          <router-link
            v-for="category in categories"
            :key="category.name"
            :to="`/events?category=${category.name}`"
            class="category-card"
            :style="{ background: category.gradient }"
          >
            <span class="category-icon">{{ category.icon }}</span>
            <h3>{{ category.name }}</h3>
          </router-link>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
      <div class="container">
        <h2>Ready to Explore Cebu?</h2>
        <p>Check out our complete calendar of events and plan your next adventure</p>
        <router-link to="/calendar" class="btn btn-primary btn-large">View Event Calendar</router-link>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../api/axios';
import EventCard from '../components/EventCard.vue';

const featuredEvents = ref([]);
const loading = ref(true);

const categories = [
  { name: 'Festival', icon: '🎉', gradient: 'linear-gradient(135deg, #0d9488, #14b8a6)' },
  { name: 'Music', icon: '🎵', gradient: 'linear-gradient(135deg, #0891b2, #06b6d4)' },
  { name: 'Sports', icon: '⚽', gradient: 'linear-gradient(135deg, #22c55e, #16a34a)' },
  { name: 'Community', icon: '🤝', gradient: 'linear-gradient(135deg, #ec4899, #db2777)' },
];

onMounted(async () => {
  try {
    const response = await api.get('/events/featured');
    featuredEvents.value = response.data;
  } catch (error) {
    console.error('Error fetching featured events:', error);
  } finally {
    loading.value = false;
  }
});
</script>

<style scoped>
.home {
  min-height: 100vh;
}

/* Hero Section */
.hero {
  height: 70vh;
  min-height: 500px;
  position: relative;
  display: flex;
  align-items: flex-end;
  justify-content: flex-start;
  background-image: url('/src/assets/hero-image.jpg');
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  margin-top: 60px;
}

.hero-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to bottom, rgba(30, 58, 74, 0.2), rgba(30, 58, 74, 0.5));
}

.hero-content {
  position: relative;
  color: white;
  max-width: 900px;
  padding: 4rem;
  padding-bottom: 0;
}

.hero-content h1 {
  font-size: 2.75rem;
  font-weight: 700;
  font-style: italic;
  margin-bottom: 0;
  line-height: 1.2;
}

.hero-content p {
  font-size: 1.1rem;
  opacity: 0;
  margin-bottom: 0;
}

/* Discover Section - Below Hero */
.discover-intro {
  background: #1e3a4a;
  padding: 4rem 2rem;
  color: white;
}

.discover-intro .container {
  max-width: 1200px;
  margin: 0 auto;
}

.discover-intro h2 {
  font-size: 2rem;
  font-weight: 700;
  font-style: italic;
  margin-bottom: 1rem;
}

.discover-intro p {
  font-size: 1.1rem;
  line-height: 1.7;
  opacity: 0.9;
  max-width: 900px;
}

/* Buttons */
.btn {
  display: inline-block;
  padding: 1rem 2rem;
  border-radius: 12px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.3s ease;
  cursor: pointer;
}

.btn-primary {
  background: linear-gradient(135deg, #0ea5e9, #0284c7);
  color: white;
  box-shadow: 0 4px 15px rgba(14, 165, 233, 0.4);
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(14, 165, 233, 0.5);
}

.btn-outline {
  background: transparent;
  color: white;
  border: 2px solid white;
}

.btn-outline:hover {
  background: white;
  color: #0ea5e9;
}

.btn-secondary {
  background: #f1f5f9;
  color: #0ea5e9;
}

.btn-secondary:hover {
  background: #0ea5e9;
  color: white;
}

.btn-large {
  padding: 1.25rem 3rem;
  font-size: 1.1rem;
}

/* Container */
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
}

/* Section Header */
.section-header {
  text-align: center;
  margin-bottom: 3rem;
}

.section-header h2 {
  font-size: 2.5rem;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 0.5rem;
}

.section-header p {
  color: #64748b;
  font-size: 1.1rem;
}

/* Featured Section */
.featured-section {
  padding: 6rem 0;
  background: #f8fafc;
}

.events-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 2rem;
}

.section-footer {
  text-align: center;
  margin-top: 3rem;
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

/* Categories Section */
.categories-section {
  padding: 6rem 0;
}

.categories-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 1.5rem;
  justify-content: center;
}

.category-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  border-radius: 16px;
  color: white;
  text-decoration: none;
  transition: all 0.3s ease;
  width: 180px;
}

.category-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
}

.category-icon {
  font-size: 3rem;
  margin-bottom: 0.5rem;
}

.category-card h3 {
  font-size: 1rem;
  font-weight: 600;
}

/* CTA Section */
.cta-section {
  padding: 6rem 0;
  background: #1e3a4a;
  text-align: center;
  color: white;
}

.cta-section h2 {
  font-size: 2.5rem;
  font-weight: 700;
  font-style: italic;
  margin-bottom: 1rem;
}

.cta-section p {
  font-size: 1.1rem;
  opacity: 0.9;
  margin-bottom: 2rem;
}

.cta-section .btn-primary {
  background: #0ea5e9;
  color: white;
}

.cta-section .btn-primary:hover {
  background: #0284c7;
}

/* Responsive */
@media (max-width: 768px) {
  .hero-content h1 {
    font-size: 2.5rem;
  }

  .hero-content p {
    font-size: 1rem;
  }

  .section-header h2 {
    font-size: 2rem;
  }

  .events-grid {
    grid-template-columns: 1fr;
  }
}
</style>
