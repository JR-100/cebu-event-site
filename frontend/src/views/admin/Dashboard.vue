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
        <h1>Dashboard</h1>
        <div class="user-info">
          <span>Welcome, {{ user?.name || 'Admin' }}</span>
        </div>
      </header>

      <div class="dashboard-content">
        <!-- Quick Actions -->
        <div class="quick-actions">
          <h2>Quick Actions</h2>
          <div class="actions-grid">
            <router-link to="/admin/events/create" class="action-card">
              <span class="action-icon">➕</span>
              <span class="action-text">Add New Event</span>
            </router-link>
            <router-link to="/admin/events" class="action-card">
              <span class="action-icon">📋</span>
              <span class="action-text">Manage Events</span>
            </router-link>
          </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-icon blue">📅</div>
            <div class="stat-info">
              <span class="stat-value">{{ stats.total }}</span>
              <span class="stat-label">Total Events</span>
            </div>
          </div>

          <div class="stat-card">
            <div class="stat-icon green">🎉</div>
            <div class="stat-info">
              <span class="stat-value">{{ stats.upcoming }}</span>
              <span class="stat-label">Upcoming Events</span>
            </div>
          </div>

          <div class="stat-card">
            <div class="stat-icon yellow">⭐</div>
            <div class="stat-info">
              <span class="stat-value">{{ stats.featured }}</span>
              <span class="stat-label">Featured Events</span>
            </div>
          </div>

          <div class="stat-card">
            <div class="stat-icon purple">📁</div>
            <div class="stat-info">
              <span class="stat-value">{{ stats.categories }}</span>
              <span class="stat-label">Categories</span>
            </div>
          </div>
        </div>

        <!-- Recent Events -->
        <div class="recent-section">
          <div class="section-header">
            <h2>Recent Events</h2>
            <router-link to="/admin/events" class="view-all">View All →</router-link>
          </div>

          <div class="events-table-wrapper">
            <table class="events-table">
              <thead>
                <tr>
                  <th>Event Name</th>
                  <th>Category</th>
                  <th>Date</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="event in recentEvents" :key="event.id">
                  <td>
                    <div class="event-name-cell">
                      <span v-if="event.is_featured" class="featured-star">⭐</span>
                      {{ event.name }}
                    </div>
                  </td>
                  <td><span class="category-tag">{{ event.category }}</span></td>
                  <td>{{ formatDate(event.date_time) }}</td>
                  <td>
                    <span class="status-badge" :class="getEventStatus(event.date_time)">
                      {{ getEventStatus(event.date_time) }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '../../api/axios';

const router = useRouter();

const user = ref(null);
const stats = ref({
  total: 0,
  upcoming: 0,
  featured: 0,
  categories: 0
});
const recentEvents = ref([]);

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  });
};

const getEventStatus = (dateString) => {
  const eventDate = new Date(dateString);
  const now = new Date();
  return eventDate >= now ? 'upcoming' : 'past';
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

onMounted(async () => {
  // Get user from localStorage
  const storedUser = localStorage.getItem('admin_user');
  if (storedUser) {
    user.value = JSON.parse(storedUser);
  }

  // Fetch dashboard data
  try {
    const [eventsRes, categoriesRes, featuredRes] = await Promise.all([
      api.get('/admin/events', { params: { per_page: 5 } }),
      api.get('/events/categories'),
      api.get('/events/featured')
    ]);

    recentEvents.value = eventsRes.data.data;
    stats.value = {
      total: eventsRes.data.total,
      upcoming: eventsRes.data.data.filter(e => new Date(e.date_time) >= new Date()).length,
      featured: featuredRes.data.length,
      categories: categoriesRes.data.length
    };
  } catch (error) {
    console.error('Error fetching dashboard data:', error);
  }
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
  padding: 0;
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

.user-info {
  color: #64748b;
}

.dashboard-content {
  padding: 2rem;
  flex-direction: column;
  gap: 20px;
}

/* Stats Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
  margin-bottom: 2rem;
}

.stat-card {
  background: white;
  border-radius: 16px;
  padding: 1.5rem;
  display: flex;
  align-items: center;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
}

.stat-icon {
  width: 56px;
  height: 56px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
}

.stat-icon.blue { background: #dbeafe; }
.stat-icon.green { background: #dcfce7; }
.stat-icon.yellow { background: #fef3c7; }
.stat-icon.purple { background: #e9d5ff; }

.stat-info {
  display: flex;
  flex-direction: column;
}

.stat-value {
  font-size: 1.75rem;
  font-weight: 700;
  color: #1e293b;
}

.stat-label {
  font-size: 0.875rem;
  color: #64748b;
}

/* Recent Events */
.recent-section {
  background: white;
  border-radius: 16px;
  padding: 1.5rem;
  margin-bottom: 2rem;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.section-header h2 {
  font-size: 1.1rem;
  font-weight: 600;
  color: #1e293b;
  margin: 0;
}

.view-all {
  color: #0ea5e9;
  text-decoration: none;
  font-size: 0.875rem;
  font-weight: 500;
}

.events-table-wrapper {
  overflow-x: auto;
}

.events-table {
  width: 100%;
  border-collapse: collapse;
}

.events-table th,
.events-table td {
  padding: 1rem;
  text-align: left;
  border-bottom: 1px solid #e2e8f0;
}

.events-table th {
  font-size: 0.75rem;
  font-weight: 600;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.events-table td {
  font-size: 0.9rem;
  color: #374151;
}

.event-name-cell {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.featured-star {
  font-size: 0.875rem;
}

.category-tag {
  background: #e0f2fe;
  color: #0284c7;
  padding: 4px 10px;
  border-radius: 15px;
  font-size: 0.75rem;
  font-weight: 500;
}

.status-badge {
  padding: 4px 10px;
  border-radius: 15px;
  font-size: 0.75rem;
  font-weight: 500;
  text-transform: capitalize;
}

.status-badge.upcoming {
  background: #dcfce7;
  color: #16a34a;
}

.status-badge.past {
  background: #f1f5f9;
  color: #64748b;
}

/* Quick Actions */
.quick-actions h2 {
  font-size: 1.1rem;
  font-weight: 600;
  color: #1e293b;
  margin-bottom: 1rem;
  gap: 20px;
}

.actions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
}

.action-card {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1.5rem;
  background: white;
  border-radius: 12px;
  text-decoration: none;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
}

.action-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.action-icon {
  font-size: 1.5rem;
}

.action-text {
  font-weight: 500;
  color: #374151;
}

@media (max-width: 768px) {
  .sidebar {
    display: none;
  }

  .main-content {
    margin-left: 0;
  }
}
</style>
