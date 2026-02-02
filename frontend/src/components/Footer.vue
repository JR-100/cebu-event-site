<template>
  <footer class="footer">
    <div class="footer-container">
      <div class="footer-brand">
        <span class="brand-icon">🌴</span>
        <span class="brand-text">Cebu Events</span>
        <p class="footer-tagline">Discover the best events in the Queen City of the South</p>
      </div>

      <div class="footer-links">
        <h4>Quick Links</h4>
        <ul>
          <li><router-link to="/">Home</router-link></li>
          <li><router-link to="/discover">Discover</router-link></li>
          <li><router-link to="/events">Event List</router-link></li>
          <li><router-link to="/calendar">Calendar</router-link></li>
        </ul>
      </div>

      <div class="footer-links">
        <h4>Categories</h4>
        <ul>
          <li><router-link to="/events?category=Festival">Festivals</router-link></li>
          <li><router-link to="/events?category=Music">Music</router-link></li>
          <li><router-link to="/events?category=Food">Food</router-link></li>
          <li><router-link to="/events?category=Workshop">Workshops</router-link></li>
        </ul>
      </div>

      <div class="footer-contact">
        <h4>Contact</h4>
        <p>📍 Cebu City, Philippines</p>
        <p>📧 info@cebuevents.com</p>
        <p>📞 +63 32 123 4567</p>
      </div>
    </div>

    <div class="footer-bottom">
      <p>&copy; {{ currentYear }} Cebu Events. All rights reserved.</p>
    </div>
  </footer>
</template>

<script setup>
import { computed } from 'vue';

const currentYear = computed(() => new Date().getFullYear());
</script>

<style scoped>
.footer {
  background: linear-gradient(135deg, #1e293b, #0f172a);
  color: white;
  padding: 4rem 2rem 1rem;
  margin-top: 4rem;
}

.footer-container {
  max-width: 1200px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 3rem;
}

.footer-brand {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.brand-icon {
  font-size: 2rem;
}

.brand-text {
  font-size: 1.5rem;
  font-weight: 700;
}

.footer-tagline {
  color: #94a3b8;
  font-size: 0.9rem;
  line-height: 1.6;
}

.footer-links h4,
.footer-contact h4 {
  font-size: 1rem;
  font-weight: 600;
  margin-bottom: 1rem;
  color: #0ea5e9;
}

.footer-links ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.footer-links li {
  margin-bottom: 0.5rem;
}

.footer-links a {
  color: #94a3b8;
  text-decoration: none;
  transition: color 0.3s ease;
}

.footer-links a:hover {
  color: white;
}

.footer-contact p {
  color: #94a3b8;
  margin: 0.5rem 0;
  font-size: 0.9rem;
}

.footer-bottom {
  max-width: 1200px;
  margin: 3rem auto 0;
  padding-top: 2rem;
  border-top: 1px solid #334155;
  text-align: center;
}

.footer-bottom p {
  color: #64748b;
  font-size: 0.875rem;
}

@media (max-width: 768px) {
  .footer-container {
    grid-template-columns: 1fr 1fr;
  }

  .footer-brand {
    grid-column: span 2;
  }
}
</style>
