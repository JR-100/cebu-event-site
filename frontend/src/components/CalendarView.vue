<template>
  <div class="calendar-component">
    <div class="calendar-header">
      <button @click="previousMonth" class="nav-btn">&lt;</button>
      <h3>{{ monthYear }}</h3>
      <button @click="nextMonth" class="nav-btn">&gt;</button>
    </div>

    <div class="calendar-weekdays">
      <span v-for="day in weekdays" :key="day">{{ day }}</span>
    </div>

    <div class="calendar-days">
      <div
        v-for="(day, index) in calendarDays"
        :key="index"
        class="calendar-day"
        :class="{
          'other-month': !day.isCurrentMonth,
          'today': day.isToday,
          'has-events': day.events.length > 0,
          'selected': isSelected(day.date)
        }"
        @click="selectDate(day)"
      >
        <span class="day-number">{{ day.dayNumber }}</span>
        <div v-if="day.events.length > 0" class="event-dots">
          <span
            v-for="(event, i) in day.events.slice(0, 3)"
            :key="i"
            class="event-dot"
            :title="event.name"
          ></span>
        </div>
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
  background: #f1f5f9;
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
}

.calendar-day.selected {
  background: #1e293b;
  color: white;
}

.day-number {
  font-size: 0.875rem;
}

.event-dots {
  display: flex;
  gap: 2px;
  margin-top: 2px;
}

.event-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: #f59e0b;
}

.calendar-day.today .event-dot,
.calendar-day.selected .event-dot {
  background: white;
}
</style>
