<template>
  <div class="calendar-component">
    <div class="calendar-header">
      <button @click="previousMonth" class="nav-btn">&lt;</button>
      <h3>{{ monthYear }}</h3>
      <button @click="nextMonth" class="nav-btn">&gt;</button>
    </div>

    <div class="calendar-legend">
      <span class="legend-item"><span class="legend-dot festival"></span>Festival</span>
      <span class="legend-item"><span class="legend-dot music"></span>Music</span>
      <span class="legend-item"><span class="legend-dot sports"></span>Sports</span>
      <span class="legend-item"><span class="legend-dot community"></span>Community</span>
    </div>

    <div class="calendar-weekdays">
      <span v-for="day in weekdays" :key="day">{{ day }}</span>
    </div>

    <div class="calendar-days">
      <div
        v-for="(day, index) in calendarDays"
        :key="index"
        class="calendar-day"
        :class="[
          {
            'other-month': !day.isCurrentMonth,
            'today': day.isToday && day.events.length === 0,
            'has-events': day.events.length > 0,
            'selected': isSelected(day.date)
          },
          day.events.length > 0 ? 'cat-' + getDominantCategory(day.events) : ''
        ]"
        @click="selectDate(day)"
      >
        <span class="day-number">{{ day.dayNumber }}</span>
        <div v-if="day.events.length > 1" class="event-count">+{{ day.events.length }}</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
  events: {
    type: Object,
    default: () => ({})
  },
  selectedDate: {
    type: String,
    default: null
  }
});

const emit = defineEmits(['dateSelected', 'monthChanged']);

const currentDate = ref(new Date());
const weekdays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

const monthYear = computed(() => {
  return currentDate.value.toLocaleDateString('en-US', {
    month: 'long',
    year: 'numeric'
  });
});

const calendarDays = computed(() => {
  const year = currentDate.value.getFullYear();
  const month = currentDate.value.getMonth();

  const firstDay = new Date(year, month, 1);
  const lastDay = new Date(year, month + 1, 0);

  const days = [];

  // Days from previous month
  const firstDayOfWeek = firstDay.getDay();
  const prevMonthLastDay = new Date(year, month, 0).getDate();

  for (let i = firstDayOfWeek - 1; i >= 0; i--) {
    const date = new Date(year, month - 1, prevMonthLastDay - i);
    days.push(createDayObject(date, false));
  }

  // Days of current month
  for (let i = 1; i <= lastDay.getDate(); i++) {
    const date = new Date(year, month, i);
    days.push(createDayObject(date, true));
  }

  // Days from next month
  const remainingDays = 42 - days.length;
  for (let i = 1; i <= remainingDays; i++) {
    const date = new Date(year, month + 1, i);
    days.push(createDayObject(date, false));
  }

  return days;
});

function createDayObject(date, isCurrentMonth) {
  const dateStr = date.toISOString().split('T')[0];
  const today = new Date();

  return {
    date: dateStr,
    dayNumber: date.getDate(),
    isCurrentMonth,
    isToday: dateStr === today.toISOString().split('T')[0],
    events: props.events[dateStr] || []
  };
}

function isSelected(date) {
  return date === props.selectedDate;
}

function selectDate(day) {
  if (day.events.length > 0 || day.isCurrentMonth) {
    emit('dateSelected', day.date, day.events);
  }
}

function getCategoryClass(category) {
  const categoryMap = {
    'Festival': 'festival',
    'Music': 'music',
    'Sports': 'sports',
    'Community': 'community'
  };
  return categoryMap[category] || 'default';
}

function getDominantCategory(events) {
  if (!events || events.length === 0) return 'default';
  // Use the first event's category as the dominant color
  return getCategoryClass(events[0].category);
}

function previousMonth() {
  currentDate.value = new Date(
    currentDate.value.getFullYear(),
    currentDate.value.getMonth() - 1,
    1
  );
  emitMonthChanged();
}

function nextMonth() {
  currentDate.value = new Date(
    currentDate.value.getFullYear(),
    currentDate.value.getMonth() + 1,
    1
  );
  emitMonthChanged();
}

function emitMonthChanged() {
  emit('monthChanged', {
    month: currentDate.value.getMonth() + 1,
    year: currentDate.value.getFullYear()
  });
}

// Emit initial month
emitMonthChanged();
</script>

<style scoped>
.calendar-component {
  background: white;
  border-radius: 16px;
  padding: 1.5rem;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.calendar-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.calendar-header h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1e293b;
}

.calendar-legend {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
  margin-bottom: 1rem;
  padding: 0.75rem;
  background: #f8fafc;
  border-radius: 8px;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 0.35rem;
  font-size: 0.7rem;
  color: #64748b;
  font-weight: 500;
}

.legend-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
}

.legend-dot.festival { background: #f59e0b; }
.legend-dot.music { background: #8b5cf6; }
.legend-dot.sports { background: #22c55e; }
.legend-dot.community { background: #ec4899; }

.nav-btn {
  width: 36px;
  height: 36px;
  border: none;
  background: #f1f5f9;
  border-radius: 8px;
  cursor: pointer;
  font-size: 1rem;
  color: #64748b;
  transition: all 0.3s ease;
}

.nav-btn:hover {
  background: #0ea5e9;
  color: white;
}

.calendar-weekdays {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 4px;
  margin-bottom: 0.5rem;
}

.calendar-weekdays span {
  text-align: center;
  font-size: 0.75rem;
  font-weight: 600;
  color: #64748b;
  padding: 0.5rem;
}

.calendar-days {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 4px;
}

.calendar-day {
  aspect-ratio: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4px;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
  position: relative;
}

.calendar-day:hover {
  background: #0ea5e9;
  color: white;
}

.calendar-day:hover .day-number {
  color: white;
}

.calendar-day:active {
  background: #0284c7;
  color: white;
  transform: scale(0.95);
}

.calendar-day.other-month {
  opacity: 0.3;
}

.calendar-day.today {
  background: #0ea5e9;
  color: white;
}

.calendar-day.today:hover {
  background: #0284c7;
}

.calendar-day.has-events {
  font-weight: 600;
  color: white;
}

.calendar-day.cat-festival {
  background: #f59e0b;
  color: white;
}

.calendar-day.cat-festival:hover {
  background: #d97706;
}

.calendar-day.cat-music {
  background: #8b5cf6;
  color: white;
}

.calendar-day.cat-music:hover {
  background: #7c3aed;
}

.calendar-day.cat-sports {
  background: #22c55e;
  color: white;
}

.calendar-day.cat-sports:hover {
  background: #16a34a;
}

.calendar-day.cat-community {
  background: #ec4899;
  color: white;
}

.calendar-day.cat-community:hover {
  background: #db2777;
}

.calendar-day.cat-default {
  background: #0ea5e9;
  color: white;
}

.calendar-day.cat-default:hover {
  background: #0284c7;
}

.calendar-day.has-events .day-number {
  color: white;
}

.calendar-day.selected {
  background: #1e293b;
  color: white;
  box-shadow: 0 0 0 2px #0ea5e9;
}

.calendar-day.selected:hover {
  background: #334155;
}

.calendar-day:hover .event-dot {
  background: white;
}

.day-number {
  font-size: 0.875rem;
}

.event-count {
  font-size: 0.6rem;
  font-weight: 700;
  color: rgba(255, 255, 255, 0.9);
  margin-top: 1px;
}

.event-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: #64748b;
}

.event-dot.festival { background: #f59e0b; }
.event-dot.music { background: #8b5cf6; }
.event-dot.sports { background: #22c55e; }
.event-dot.community { background: #ec4899; }
.event-dot.default { background: #64748b; }

.calendar-day.today .event-dot,
.calendar-day.selected .event-dot {
  background: white;
}
</style>
