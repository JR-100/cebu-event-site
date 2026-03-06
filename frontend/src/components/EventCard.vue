<template>
  <div class="event-card" @click="$emit('click')">
    <div class="event-image">
      <img
        :src="event.image ? `${import.meta.env.VITE_API_URL}/storage/${event.image}` : defaultImage"
        :alt="event.name"
      />
      <span v-if="event.is_featured" class="featured-badge">Featured</span>
      <span class="category-badge">{{ event.category }}</span>
    </div>
    <div class="event-content">
      <div class="event-date">
        <span class="date-day">{{ formatDay }}</span>
        <span class="date-month">{{ formatMonth }}</span>
      </div>
      <div class="event-info">
        <h3 class="event-title">{{ event.name }}</h3>
        <p class="event-location">
          <span class="icon">📍</span>
          {{ event.location }}
        </p>
        <p class="event-description">{{ truncatedDescription }}</p>
        <router-link :to="`/events/${event.id}`" class="view-btn">
          View Details →
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  event: {
    type: Object,
    required: true
  }
});

defineEmits(['click']);

const defaultImage = 'https://images.unsplash.com/photo-1533174072545-7a4b6ad7a6c3?w=800';

const formatDay = computed(() => {
  return new Date(props.event.date_time).getDate();
});

const formatMonth = computed(() => {
  return new Date(props.event.date_time).toLocaleDateString('en-US', { month: 'short' });
});

const truncatedDescription = computed(() => {
  if (props.event.description.length > 100) {
    return props.event.description.substring(0, 100) + '...';
  }
  return props.event.description;
});
</script>

<style scoped>
.event-card {
  background: white;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
  cursor: pointer;
}

.event-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.event-image {
  position: relative;
  height: 200px;
  overflow: hidden;
}

.event-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.event-card:hover .event-image img {
  transform: scale(1.05);
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

.category-badge {
  position: absolute;
  top: 12px;
  right: 12px;
  background: rgba(0, 0, 0, 0.7);
  color: white;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 500;
}

.event-content {
  display: flex;
  gap: 1rem;
  padding: 1.5rem;
}

.event-date {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #0ea5e9, #0284c7);
  color: white;
  padding: 0.75rem;
  border-radius: 12px;
  min-width: 60px;
}

.date-day {
  font-size: 1.5rem;
  font-weight: 700;
  line-height: 1;
}

.date-month {
  font-size: 0.75rem;
  text-transform: uppercase;
  font-weight: 500;
}

.event-info {
  flex: 1;
}

.event-title {
  font-size: 1.1rem;
  font-weight: 700;
  color: #1e293b;
  margin: 0 0 0.5rem 0;
  line-height: 1.3;
}

.event-location {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  color: #64748b;
  font-size: 0.875rem;
  margin: 0 0 0.5rem 0;
}

.icon {
  font-size: 0.875rem;
}

.event-description {
  color: #64748b;
  font-size: 0.875rem;
  line-height: 1.5;
  margin: 0 0 1rem 0;
}

.view-btn {
  display: inline-block;
  color: #0ea5e9;
  font-weight: 600;
  font-size: 0.875rem;
  text-decoration: none;
  transition: color 0.3s ease;
}

.view-btn:hover {
  color: #0284c7;
}

@media (max-width: 480px) {
  .event-image {
    height: 160px;
  }

  .event-content {
    padding: 1rem;
    gap: 0.75rem;
  }

  .event-date {
    min-width: 50px;
    padding: 0.5rem;
  }

  .date-day {
    font-size: 1.25rem;
  }

  .date-month {
    font-size: 0.65rem;
  }

  .event-title {
    font-size: 1rem;
  }

  .event-location {
    font-size: 0.8rem;
  }

  .event-description {
    font-size: 0.8rem;
  }
}
</style>
