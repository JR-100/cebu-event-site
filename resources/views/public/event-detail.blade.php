@extends('layouts.app')

@section('title', $event->name . ' - Cebu Event Site')

@section('content')
<div class="event-detail-page">
    <!-- Hero Image -->
    <div class="event-hero">
        <img
            src="{{ $event->image ? asset('storage/' . $event->image) : asset('images/hero-image.jpg') }}"
            alt="{{ $event->name }}"
        />
        <div class="hero-overlay"></div>
        <a href="{{ url('/events') }}" class="back-button">← Back to Events</a>
    </div>

    <div class="event-content">
        <div class="event-detail-grid">
            <!-- Main Content -->
            <div class="event-main">
                <!-- Event Header -->
                <div class="event-header">
                    <div class="event-badges">
                        <span class="category-badge {{ strtolower($event->category) }}">{{ $event->category }}</span>
                        @if($event->is_featured)
                            <span class="featured-badge">⭐ Featured</span>
                        @endif
                    </div>
                    <h1>{{ $event->name }}</h1>
                </div>

                <!-- Event Info Cards -->
                <div class="info-cards">
                    <div class="info-card">
                        <span class="info-icon">📅</span>
                        <div class="info-content">
                            <span class="info-label">Date & Time</span>
                            <span class="info-value">{{ $event->date_time->format('l, F j, Y g:i A') }}</span>
                        </div>
                    </div>

                    <div class="info-card">
                        <span class="info-icon">📍</span>
                        <div class="info-content">
                            <span class="info-label">Location</span>
                            <span class="info-value">{{ $event->location }}</span>
                        </div>
                    </div>
                </div>

                <!-- Event Description -->
                <div class="event-description">
                    <h2>About This Event</h2>
                    <p>{!! nl2br(e($event->description)) !!}</p>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="event-sidebar">
                <div class="sidebar-card">
                    <h3>Event Details</h3>
                    <ul class="details-list">
                        <li>
                            <span class="detail-label">Category</span>
                            <span class="detail-value">{{ $event->category }}</span>
                        </li>
                        <li>
                            <span class="detail-label">Date</span>
                            <span class="detail-value">{{ $event->date_time->format('F j, Y') }}</span>
                        </li>
                        <li>
                            <span class="detail-label">Time</span>
                            <span class="detail-value">{{ $event->date_time->format('g:i A') }}</span>
                        </li>
                        <li>
                            <span class="detail-label">Venue</span>
                            <span class="detail-value">{{ $event->location }}</span>
                        </li>
                    </ul>
                </div>

                <div class="sidebar-card share-card">
                    <h3>Share This Event</h3>
                    <div class="share-buttons">
                        <button onclick="shareEvent('facebook')" class="share-btn facebook">Facebook</button>
                        <button onclick="shareEvent('twitter')" class="share-btn twitter">Twitter</button>
                        <button onclick="copyLink(this)" class="share-btn copy">Copy Link</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function shareEvent(platform) {
    const url = window.location.href;
    const text = 'Check out {{ addslashes($event->name) }} in Cebu!';
    let shareUrl = '';
    if (platform === 'facebook') {
        shareUrl = 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(url);
    } else if (platform === 'twitter') {
        shareUrl = 'https://twitter.com/intent/tweet?text=' + encodeURIComponent(text) + '&url=' + encodeURIComponent(url);
    }
    if (shareUrl) window.open(shareUrl, '_blank', 'width=600,height=400');
}

function copyLink(btn) {
    navigator.clipboard.writeText(window.location.href).then(function() {
        btn.textContent = 'Copied!';
        setTimeout(function() { btn.textContent = 'Copy Link'; }, 2000);
    });
}
</script>
@endsection
