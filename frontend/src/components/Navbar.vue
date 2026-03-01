<template>
  <nav class="navbar" :class="{ 'scrolled': isScrolled, 'solid-bg': !isHomePage }">
    <div class="navbar-container">
      <router-link to="/" class="navbar-brand" @click="closeMenu">
        <svg class="brand-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
        </svg>
        <span class="brand-text">Cebu Event Site</span>
      </router-link>

      <button class="mobile-toggle" :class="{ 'open': isMenuOpen }" @click="isMenuOpen = !isMenuOpen" aria-label="Toggle menu">
        <span></span>
        <span></span>
        <span></span>
      </button>

      <ul class="navbar-menu" :class="{ 'active': isMenuOpen }">
        <li><router-link to="/" @click="closeMenu">Home</router-link></li>
        <li class="dropdown desktop-only">
          <a href="#" class="dropdown-trigger" @click.prevent>
            Discover <span class="arrow">▾</span>
          </a>
          <ul class="dropdown-menu">
            <li><router-link to="/events?category=Festival" @click="closeMenu" class="dropdown-item"><span class="cat-dot" style="background: #f59e0b;"></span> Festivals</router-link></li>
            <li><router-link to="/events?category=Music" @click="closeMenu" class="dropdown-item"><span class="cat-dot" style="background: #8b5cf6;"></span> Music</router-link></li>
            <li><router-link to="/events?category=Sports" @click="closeMenu" class="dropdown-item"><span class="cat-dot" style="background: #22c55e;"></span> Sports</router-link></li>
            <li><router-link to="/events?category=Community" @click="closeMenu" class="dropdown-item"><span class="cat-dot" style="background: #ec4899;"></span> Community</router-link></li>
          </ul>
        </li>
        <!-- Mobile: tap to toggle dropdown -->
        <li class="mobile-only mobile-dropdown" :class="{ 'mobile-dropdown-open': isDropdownOpen }">
          <a href="#" class="mobile-discover-trigger" @click.prevent="toggleDropdown">
            Discover
            <svg class="mobile-arrow" viewBox="0 0 12 12" fill="currentColor"><path d="M2 4.5L6 8.5L10 4.5"/></svg>
          </a>
          <ul class="mobile-dropdown-menu">
            <li><router-link to="/events?category=Festival" @click="closeMenu" class="mobile-cat-item"><span class="cat-dot" style="background: #f59e0b;"></span> Festivals</router-link></li>
            <li><router-link to="/events?category=Music" @click="closeMenu" class="mobile-cat-item"><span class="cat-dot" style="background: #8b5cf6;"></span> Music</router-link></li>
            <li><router-link to="/events?category=Sports" @click="closeMenu" class="mobile-cat-item"><span class="cat-dot" style="background: #22c55e;"></span> Sports</router-link></li>
            <li><router-link to="/events?category=Community" @click="closeMenu" class="mobile-cat-item"><span class="cat-dot" style="background: #ec4899;"></span> Community</router-link></li>
          </ul>
        </li>
        <li><router-link to="/events" @click="closeMenu">Events</router-link></li>
        <li><router-link to="/calendar" @click="closeMenu">Calendar</router-link></li>
      </ul>
    </div>

    <!-- Overlay to close menu on outside click -->
    <div v-if="isMenuOpen" class="menu-overlay" @click="closeMenu"></div>
  </nav>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const isScrolled = ref(false);
const isMenuOpen = ref(false);
const isDropdownOpen = ref(false);

const isHomePage = computed(() => route.path === '/');

const closeMenu = () => {
  isMenuOpen.value = false;
  isDropdownOpen.value = false;
};

const toggleDropdown = () => {
  isDropdownOpen.value = !isDropdownOpen.value;
};

const handleScroll = () => {
  isScrolled.value = window.scrollY > 50;
};

// Close menu on route change
watch(() => route.path, () => {
  closeMenu();
});

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
  transition: all 0.4s ease;
  background: transparent;
}

.navbar.scrolled {
  background: #1e3a4a;
  box-shadow: 0 4px 30px rgba(0, 0, 0, 0.25);
}

.navbar.solid-bg {
  background: #1e3a4a;
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
  gap: 0.6rem;
  text-decoration: none;
  font-size: 1.2rem;
  font-weight: 700;
  color: white;
  text-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
  padding: 0.45rem 1rem;
  background: rgba(15, 30, 45, 0.55);
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  border: 1px solid rgba(255, 255, 255, 0.15);
  border-radius: 14px;
  box-shadow: 0 4px 24px rgba(0, 0, 0, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.06);
  transition: all 0.4s ease;
}

.navbar-brand:hover {
  background: rgba(15, 30, 45, 0.65);
  border-color: rgba(56, 189, 248, 0.25);
  box-shadow: 0 4px 28px rgba(0, 0, 0, 0.25), inset 0 1px 0 rgba(255, 255, 255, 0.08);
}

.brand-icon {
  width: 26px;
  height: 26px;
  color: #38bdf8;
  filter: drop-shadow(0 2px 6px rgba(56, 189, 248, 0.4));
}

.brand-text {
  letter-spacing: 0.02em;
}

.navbar.scrolled .brand-icon {
  filter: drop-shadow(0 1px 3px rgba(56, 189, 248, 0.3));
}

.navbar.scrolled .navbar-brand {
  color: white;
  text-shadow: none;
  background: rgba(30, 58, 74, 0.6);
  border-color: rgba(255, 255, 255, 0.1);
}

.navbar.scrolled .navbar-brand:hover {
  background: rgba(30, 58, 74, 0.7);
  border-color: rgba(56, 189, 248, 0.2);
}

.navbar-menu {
  display: flex;
  list-style: none;
  gap: 0.5rem;
  margin: 0;
  padding: 0.4rem 0.6rem;
  align-items: center;
  background: rgba(15, 30, 45, 0.55);
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  border: 1px solid rgba(255, 255, 255, 0.15);
  border-radius: 14px;
  box-shadow: 0 4px 24px rgba(0, 0, 0, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.06);
}

.navbar.scrolled .navbar-menu {
  background: rgba(30, 58, 74, 0.6);
  border-color: rgba(255, 255, 255, 0.1);
}

.navbar-menu li {
  display: flex;
  align-items: center;
}

.navbar-menu a {
  text-decoration: none;
  color: rgba(255, 255, 255, 0.95);
  font-weight: 600;
  padding: 0.5rem 1rem;
  border-radius: 10px;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  font-size: 0.92rem;
  text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
}

.navbar.scrolled .navbar-menu a {
  color: rgba(255, 255, 255, 0.95);
  text-shadow: none;
}

.navbar-menu a:hover {
  background: rgba(255, 255, 255, 0.15);
  color: white;
}

.navbar-menu a.router-link-active {
  background: rgba(56, 189, 248, 0.2);
  color: #7dd3fc;
}

.navbar.scrolled .navbar-menu a:hover {
  background: rgba(255, 255, 255, 0.12);
  color: white;
}

.navbar.scrolled .navbar-menu a.router-link-active {
  background: rgba(56, 189, 248, 0.2);
  color: #7dd3fc;
}

/* Dropdown styles */
.dropdown {
  position: relative;
}

.dropdown-trigger {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  cursor: pointer;
}

.arrow {
  font-size: 0.65rem;
  transition: transform 0.3s ease;
  display: inline-block;
  margin-left: 2px;
}

.dropdown:hover .arrow,
.dropdown-open .arrow {
  transform: rotate(180deg);
}

.dropdown-menu {
  position: absolute;
  top: calc(100% + 8px);
  left: 50%;
  transform: translateX(-50%) translateY(8px);
  background: #1a2e3a;
  min-width: 180px;
  border-radius: 10px;
  box-shadow: 0 12px 36px rgba(0, 0, 0, 0.35);
  padding: 0.4rem;
  opacity: 0;
  visibility: hidden;
  transition: all 0.25s ease;
  z-index: 100;
  list-style: none;
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.dropdown:hover .dropdown-menu {
  opacity: 1;
  visibility: visible;
  transform: translateX(-50%) translateY(0);
}

.dropdown-item {
  display: flex !important;
  align-items: center;
  gap: 0.6rem;
  padding: 0.65rem 0.9rem !important;
  border-radius: 0 !important;
  background: transparent !important;
  text-decoration: none;
  transition: all 0.2s ease;
  color: rgba(255, 255, 255, 0.85);
  font-weight: 500;
  font-size: 0.9rem;
}

.dropdown-item:hover {
  background: transparent !important;
  color: #38bdf8;
}

.cat-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  flex-shrink: 0;
}

/* Mobile/desktop visibility */
.mobile-only {
  display: none !important;
}

.mobile-discover-trigger {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  padding: 1rem 1.5rem;
  color: white;
  font-weight: 600;
  font-size: 1rem;
  text-decoration: none;
  cursor: pointer;
}

.mobile-arrow {
  width: 14px;
  height: 14px;
  transition: transform 0.3s ease;
  color: rgba(255, 255, 255, 0.45);
  fill: none;
  stroke: currentColor;
  stroke-width: 2;
  stroke-linecap: round;
  stroke-linejoin: round;
}

.mobile-dropdown-open .mobile-arrow {
  transform: rotate(180deg);
  color: #0ea5e9;
}

.mobile-dropdown-menu {
  list-style: none;
  padding: 0;
  margin: 0;
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.3s ease;
}

.mobile-dropdown-open .mobile-dropdown-menu {
  max-height: 300px;
}

.mobile-cat-item {
  display: flex !important;
  align-items: flex-start;
  gap: 0.7rem;
  padding: 0.8rem 1.5rem 0.8rem 0rem !important;
  color: rgba(255, 255, 255, 0.9);
  font-size: 0.95rem;
  font-weight: 500;
  text-decoration: none;
}

.mobile-cat-item:hover {
  background: rgba(255, 255, 255, 0.08);
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
  z-index: 1002;
}

.mobile-toggle span {
  width: 25px;
  height: 3px;
  background: white;
  border-radius: 3px;
  transition: all 0.3s ease;
  transform-origin: center;
}

/* Burger to X animation */
.mobile-toggle.open span:nth-child(1) {
  transform: translateY(8px) rotate(45deg);
}

.mobile-toggle.open span:nth-child(2) {
  opacity: 0;
  transform: scaleX(0);
}

.mobile-toggle.open span:nth-child(3) {
  transform: translateY(-8px) rotate(-45deg);
}

.navbar.scrolled .mobile-toggle span {
  background: white;
}

.menu-overlay {
  display: none;
}

@media (max-width: 768px) {
  .navbar {
    padding: 0.75rem 1rem;
  }

  .mobile-toggle {
    display: flex;
  }

  .navbar-brand {
    font-size: 1.05rem;
    padding: 0.4rem 0.8rem;
  }

  .brand-icon {
    width: 22px;
    height: 22px;
  }

  .menu-overlay {
    display: block;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.4);
    z-index: 999;
  }

  .navbar-menu {
    position: fixed;
    top: 0;
    right: -280px;
    width: 280px;
    height: 100vh;
    flex-direction: column;
    background: #1e3a4a;
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    padding: 5rem 0 2rem;
    gap: 0;
    box-shadow: -4px 0 20px rgba(0, 0, 0, 0.3);
    border: none;
    border-radius: 0;
    border-left: 1px solid rgba(255, 255, 255, 0.06);
    transition: right 0.3s ease;
    z-index: 1001;
    overflow-y: auto;
  }

  .navbar-menu.active {
    display: flex;
    right: 0;
  }

  .navbar-menu li {
    width: 100%;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  }

  .mobile-dropdown-menu li {
    border-bottom: none;
  }

  .mobile-cat-item {
    background: transparent !important;
    border-radius: 0 !important;
  }

  .mobile-cat-item:hover {
    background: rgba(255, 255, 255, 0.08) !important;
  }

  .navbar-menu a {
    color: white;
    padding: 1rem 1.5rem;
    display: block;
    width: 100%;
    font-size: 1rem;
  }

  .navbar-menu a:hover,
  .navbar-menu a.router-link-active {
    background: rgba(255, 255, 255, 0.1);
    color: #0ea5e9;
  }

  /* Dropdown on mobile: hide desktop dropdown, show flat items */
  .desktop-only {
    display: none !important;
  }

  .mobile-only {
    display: flex !important;
  }

  .mobile-dropdown {
    flex-direction: column;
    align-items: stretch;
  }

  .cat-dot {
    width: 8px;
    height: 8px;
  }
}

@media (max-width: 480px) {
  .navbar {
    padding: 0.5rem 0.75rem;
  }

  .navbar-brand {
    font-size: 0.95rem;
    padding: 0.35rem 0.6rem;
  }

  .brand-text {
    display: none;
  }
}
</style>
