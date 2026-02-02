<template>
  <div class="calendar-page">
    <div class="page-header">
      <div class="container">
        <h1>Event Calendar</h1>
        <p>Plan your month with our event calendar</p>
      </div>
    </div>

    <div class="container">
      <div class="calendar-layout">
        <!-- Calendar -->
        <div class="calendar-wrapper">
          <CalendarView
            :events="calendarEvents"
            :selected-date="selectedDate"
            @date-selected="handleDateSelected"
            @month-changed="handleMonthChanged"
          />
        </div>

        <!-- Selected Day Events -->
        <div class="events-panel">
          <div v-if="selectedDate" class="selected-date-header">
            <h2>{{ formatSelectedDate }}</h2>
            <button @click="clearSelection" class="clear-btn">Clear</button>
          </div>
          <h2 v-else>Select a date</h2>

          <div v-if="loading" class="loading-small">
            <div class="spinner-small"></div>
          </div>

          <div v-else-if="selectedEvents.length === 0" class="no-events-small">
            <p>{{ selectedDate ? 'No events on this date' : 'Click on a date to see events' }}</p>
          </div>

          <div v-else class="events-list-small">
            <div
              v-for="event in selectedEvents"
              :key="event.id"
              class="event-item-small"
              @click="openEventModal(event)"
            >
              <div class="event-time">
                {{ formatTime(event.date_time) }}
              </div>
              <div class="event-info-small">
                <h4>{{ event.name }}</h4>
                <p>📍 {{ event.location }}</p>
                <span class="event-category-small">{{ event.category }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Event Modal -->
    <div v-if="modalEvent" class="modal-overlay" @click.self="closeModal">
      <div class="modal-content">
        <button class="modal-close" @click="closeModal">×</button>

        <div class="modal-image">
          <img
            :src="modalEvent.image ? `http://localhost:8000/storage/${modalEvent.image}` : defaultImage"
            :alt="modalEvent.name"
          />
          <span v-if="modalEvent.is_featured" class="featured-badge">⭐ Featured</span>
        </div>

        <div class="modal-body">
          <span class="modal-category">{{ modalEvent.category }}</span>
          <h2>{{ modalEvent.name }}</h2>

          <div class="modal-details">
            <p><strong>📅</strong> {{ formatDateTime(modalEvent.date_time) }}</p>
            <p><strong>📍</strong> {{ modalEvent.location }}</p>
          </div>

          <p class="modal-description">{{ modalEvent.description }}</p>

          <router-link :to="`/events/${modalEvent.id}`" class="view-full-btn">
            View Full Details →
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import api from '../api/axios';
import CalendarView from '../components/CalendarView.vue';

const calendarEvents = ref({});
const selectedDate = ref(null);
const selectedEvents = ref([]);
const modalEvent = ref(null);
const loading = ref(false);

const defaultImage = 'https://images.unsplash.com/photo-1533174072545-7a4b6ad7a6c3?w=800';

const formatSelectedDate = computed(() => {
  if (!selectedDate.value) return '';
  const date = new Date(selectedDate.value);
  return date.toLocaleDateString('en-US', {
    weekday: 'long',
    month: 'long',
    day: 'numeric',
    year: 'numeric'
  });
});

const formatTime = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleTimeString('en-US', {
    hour: 'numeric',
    minute: '2-digit'
  });
};

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

const handleDateSelected = (date, events) => {
  selectedDate.value = date;
  selectedEvents.value = events;
};

const handleMonthChanged = async ({ month, year }) => {
  loading.value = true;
  try {
    const response = await api.get('/events/calendar', {
      params: { month, year }
    });
    calendarEvents.value = response.data;
  } catch (error) {
    console.error('Error fetching calendar events:', error);
  } finally {
    loading.value = false;
  }
};

const clearSelection = () => {
  selectedDate.value = null;
  selectedEvents.value = [];
};

const openEventModal = (event) => {
  modalEvent.value = event;
  document.body.style.overflow = 'hidden';
};

const closeModal = () => {
  modalEvent.value = null;
  document.body.style.overflow = '';
};
</script>

<style scoped>
.calendar-page {
  min-height: 100vh;
  padding-top: 80px;
  background: #f8fafc;
}

.page-header {
  background: linear-gradient(135deg, #8b5cf6, #6d28d9);
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
  padding: 2rem;
}

.calendar-layout {
  display: grid;
  grid-template-columns: 1fr 400px;
  gap: 2rem;
}

.calendar-wrapper {
  background: white;
  border-radius: 20px;
  padding: 1.5rem;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

/* Events Panel */
.events-panel {
  background: white;
  border-radius: 20px;
  padding: 1.5rem;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  max-height: 600px;
  overflow-y: auto;
}

.selected-date-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.events-panel h2 {
  font-size: 1.1rem;
  font-weight: 600;
  color: #1e293b;
  margin-bottom: 1rem;
}

.clear-btn {
  padding: 0.25rem 0.75rem;
  background: #f1f5f9;
  border: none;
  border-radius: 6px;
  color: #64748b;
  font-size: 0.875rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.clear-btn:hover {
  background: #e2e8f0;
}

.loading-small {
  text-align: center;
  padding: 2rem;
}

.spinner-small {
  width: 30px;
  height: 30px;
  border: 3px solid #e2e8f0;
  border-top-color: #0ea5e9;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.no-events-small {
  text-align: center;
  padding: 2rem;
  color: #64748b;
}

.events-list-small {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.event-item-small {
  display: flex;
  gap: 1rem;
  padding: 1rem;
  background: #f8fafc;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.event-item-small:hover {
  background: #e0f2fe;
  transform: translateX(5px);
}

.event-time {
  background: #0ea5e9;
  color: white;
  padding: 0.5rem;
  border-radius: 8px;
  font-size: 0.75rem;
  font-weight: 600;
  white-space: nowrap;
  height: fit-content;
}

.event-info-small h4 {
  font-size: 0.95rem;
  font-weight: 600;
  color: #1e293b;
  margin: 0 0 0.25rem 0;
}

.event-info-small p {
  font-size: 0.8rem;
  color: #64748b;
  margin: 0 0 0.5rem 0;
}

.event-category-small {
  font-size: 0.7rem;
  background: #e0f2fe;
  color: #0284c7;
  padding: 2px 8px;
  border-radius: 10px;
  font-weight: 500;
}

/* Modal */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 2rem;
}

.modal-content {
  background: white;
  border-radius: 20px;
  max-width: 500px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  position: relative;
}

.modal-close {
  position: absolute;
  top: 1rem;
  right: 1rem;
  width: 36px;
  height: 36px;
  border: none;
  background: rgba(255, 255, 255, 0.9);
  border-radius: 50%;
  font-size: 1.5rem;
  cursor: pointer;
  z-index: 10;
  transition: all 0.3s ease;
}

.modal-close:hover {
  background: white;
  transform: scale(1.1);
}

.modal-image {
  position: relative;
  height: 200px;
}

.modal-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 20px 20px 0 0;
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

.modal-body {
  padding: 1.5rem;
}

.modal-category {
  display: inline-block;
  background: #e0f2fe;
  color: #0284c7;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  margin-bottom: 0.75rem;
}

.modal-body h2 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1e293b;
  margin: 0 0 1rem 0;
}

.modal-details {
  background: #f8fafc;
  padding: 1rem;
  border-radius: 10px;
  margin-bottom: 1rem;
}

.modal-details p {
  margin: 0.5rem 0;
  color: #475569;
  font-size: 0.9rem;
}

.modal-description {
  color: #475569;
  line-height: 1.6;
  font-size: 0.9rem;
  margin-bottom: 1.5rem;
}

.view-full-btn {
  display: block;
  width: 100%;
  padding: 1rem;
  background: linear-gradient(135deg, #0ea5e9, #0284c7);
  color: white;
  text-align: center;
  text-decoration: none;
  border-radius: 10px;
  font-weight: 600;
  transition: all 0.3s ease;
}

.view-full-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(14, 165, 233, 0.4);
}

@media (max-width: 900px) {
  .calendar-layout {
    grid-template-columns: 1fr;
  }

  .events-panel {
    max-height: none;
  }
}
</style>
