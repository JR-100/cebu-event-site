<div class="event-card" onclick="window.location='{{ url('/events/' . $event->id) }}'">
    <div class="event-image">
        <img
            src="{{ $event->image ? asset('storage/' . $event->image) : asset('images/hero-image.jpg') }}"
            alt="{{ $event->name }}"
        />
        @if($event->is_featured)
            <span class="featured-badge">Featured</span>
        @endif
        <span class="category-badge">{{ $event->category }}</span>
    </div>
    <div class="event-info">
        <h3>{{ $event->name }}</h3>
        <div class="event-meta">
            <span class="event-date">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                    <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                {{ $event->date_time->format('M d, Y') }}
            </span>
            <span class="event-location">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
                </svg>
                {{ $event->location }}
            </span>
        </div>
        <p class="event-description">{{ Str::limit($event->description, 100) }}</p>
    </div>
</div>
