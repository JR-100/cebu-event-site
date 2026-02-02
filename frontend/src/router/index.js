import { createRouter, createWebHistory } from 'vue-router';

// Public pages
import Home from '../views/Home.vue';
import Discover from '../views/Discover.vue';
import EventList from '../views/EventList.vue';
import EventDetail from '../views/EventDetail.vue';
import Calendar from '../views/Calendar.vue';

// Admin pages
import AdminLogin from '../views/admin/Login.vue';
import AdminDashboard from '../views/admin/Dashboard.vue';
import AdminEvents from '../views/admin/Events.vue';
import AdminEventForm from '../views/admin/EventForm.vue';

const routes = [
  // Public routes
  { path: '/', name: 'Home', component: Home },
  { path: '/discover', name: 'Discover', component: Discover },
  { path: '/events', name: 'EventList', component: EventList },
  { path: '/events/:id', name: 'EventDetail', component: EventDetail },
  { path: '/calendar', name: 'Calendar', component: Calendar },

  // Admin routes
  { path: '/admin/login', name: 'AdminLogin', component: AdminLogin },
  {
    path: '/admin',
    name: 'AdminDashboard',
    component: AdminDashboard,
    meta: { requiresAuth: true }
  },
  {
    path: '/admin/events',
    name: 'AdminEvents',
    component: AdminEvents,
    meta: { requiresAuth: true }
  },
  {
    path: '/admin/events/create',
    name: 'AdminEventCreate',
    component: AdminEventForm,
    meta: { requiresAuth: true }
  },
  {
    path: '/admin/events/:id/edit',
    name: 'AdminEventEdit',
    component: AdminEventForm,
    meta: { requiresAuth: true }
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Navigation guard for admin routes
router.beforeEach((to, from, next) => {
  if (to.meta.requiresAuth) {
    const token = localStorage.getItem('admin_token');
    if (!token) {
      next('/admin/login');
    } else {
      next();
    }
  } else {
    next();
  }
});

export default router;
