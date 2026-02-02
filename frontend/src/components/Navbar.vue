<template>
  <nav class="navbar" :class="{ 'scrolled': isScrolled }">
    <div class="navbar-container">
      <router-link to="/" class="navbar-brand">
        <span class="brand-text">Cebu Event Site</span>
      </router-link>

      <button class="mobile-toggle" @click="isMenuOpen = !isMenuOpen">
        <span></span>
        <span></span>
        <span></span>
      </button>

      <ul class="navbar-menu" :class="{ 'active': isMenuOpen }">
        <li><router-link to="/" @click="isMenuOpen = false">Home</router-link></li>
        <li class="dropdown">
          <router-link to="/discover" @click="isMenuOpen = false" class="dropdown-trigger">
            Discover <span class="arrow">▾</span>
          </router-link>
          <ul class="dropdown-menu">
            <li><router-link to="/events?category=Festival">Festivals</router-link></li>
            <li><router-link to="/events?category=Music">Music</router-link></li>
            <li><router-link to="/events?category=Sports">Sports</router-link></li>
            <li><router-link to="/events?category=Community">Community</router-link></li>
          </ul>
        </li>
        <li><router-link to="/events" @click="isMenuOpen = false">Events</router-link></li>
        <li><router-link to="/calendar" @click="isMenuOpen = false">Calendar</router-link></li>
      </ul>
    </div>
  </nav>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const isScrolled = ref(false);
const isMenuOpen = ref(false);

const handleScroll = () => {
  isScrolled.value = window.scrollY > 50;
};

onMounted(() => {
  window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
});
</script>

<style scoped>
.navbar {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 1000;
  padding: 1rem 2rem;
  transition: all 0.3s ease;
  background: #1e3a4a;
}

.navbar.scrolled {
  background: #1e3a4a;
  box-shadow: 0 2px 20px rgba(0, 0, 0, 0.2);
}

.navbar-container {
  max-width: 1200px;
  margin: 0 auto;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.navbar-brand {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  text-decoration: none;
  font-size: 1.25rem;
  font-weight: 600;
  color: white;
}

.navbar.scrolled .navbar-brand {
  color: white;
}

.navbar-menu {
  display: flex;
  list-style: none;
  gap: 2rem;
  margin: 0;
  padding: 0;
  align-items: center;
}

.navbar-menu li {
  display: flex;
  align-items: center;
}

.navbar-menu a {
  text-decoration: none;
  color: white;
  font-weight: 500;
  padding: 0.5rem 1rem;
  border-radius: 8px;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
}

.navbar.scrolled .navbar-menu a {
  color: white;
}

.navbar-menu a:hover,
.navbar-menu a.router-link-active {
  background: rgba(255, 255, 255, 0.15);
}

.navbar.scrolled .navbar-menu a:hover,
.navbar.scrolled .navbar-menu a.router-link-active {
  background: rgba(255, 255, 255, 0.15);
  color: white;
}

/* Dropdown styles */
.dropdown {
  position: relative;
}

.dropdown-trigger {
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.arrow {
  font-size: 0.7rem;
}

.dropdown-menu {
  position: absolute;
  top: 100%;
  left: 0;
  background: white;
  min-width: 180px;
  border-radius: 8px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
  padding: 0.5rem 0;
  opacity: 0;
  visibility: hidden;
  transform: translateY(10px);
  transition: all 0.3s ease;
  z-index: 100;
  list-style: none;
}

.dropdown:hover .dropdown-menu {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

.dropdown-menu li a {
  display: block;
  padding: 0.75rem 1.25rem;
  color: #374151;
  text-decoration: none;
  transition: all 0.2s ease;
}

.dropdown-menu li a:hover {
  background: #f1f5f9;
  color: #0ea5e9;
}

.mobile-toggle {
  display: none;
  flex-direction: column;
  gap: 5px;
  background: none;
  border: none;
  cursor: pointer;
  padding: 5px;
}

.mobile-toggle span {
  width: 25px;
  height: 3px;
  background: white;
  border-radius: 3px;
  transition: background 0.3s ease;
}

.navbar.scrolled .mobile-toggle span {
  background: white;
}

@media (max-width: 768px) {
  .mobile-toggle {
    display: flex;
  }

  .navbar-menu {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    flex-direction: column;
    background: #1e3a4a;
    padding: 1rem;
    gap: 0;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    display: none;
  }

  .navbar-menu.active {
    display: flex;
  }

  .navbar-menu a {
    color: white;
    padding: 1rem;
    display: block;
  }

  .navbar-menu a:hover,
  .navbar-menu a.router-link-active {
    background: rgba(255, 255, 255, 0.15);
    color: white;
  }
}
</style>
