<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import Navbar from './components/Navbar.vue';
import Footer from './components/Footer.vue';

const route = useRoute();

const isAdminRoute = computed(() => {
  return route.path.startsWith('/admin');
});
</script>

<template>
  <div id="app">
    <Navbar v-if="!isAdminRoute" />
    <router-view />
    <Footer v-if="!isAdminRoute" />
  </div>
</template>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  background: #f8fafc;
}

#app {
  min-height: 100vh;
}
</style>
