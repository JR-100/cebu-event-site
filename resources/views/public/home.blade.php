@extends('layouts.app')

@section('title', 'Cebu Event Site - Discover Events')

@section('content')
<div class="home">
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-slideshow">
            <div class="hero-slide active" style="background-image: url('{{ asset('images/hero-image.jpg') }}')"></div>
            <div class="hero-slide" style="background-image: url('{{ asset('images/simala.jpg') }}')"></div>
            <div class="hero-slide" style="background-image: url('{{ asset('images/background 2.webp') }}')"></div>
            <div class="hero-slide" style="background-image: url('{{ asset('images/tops.webp') }}')"></div>
            <div class="hero-slide" style="background-image: url('{{ asset('images/magellans-cross.webp') }}')"></div>
        </div>
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1>Discover Cebu's Best Events</h1>
            <p>From vibrant festivals to intimate gatherings, find what's happening in the Queen City of the South</p>
            <a href="{{ url('/events') }}" class="btn btn-hero">Browse All Events</a>
        </div>
    </section>

    <!-- Discover Intro Section -->
    <section class="discover-intro">
        <div class="container">
            <h2>Discover Amazing Events</h2>
            <p>Find and explore exciting events happening in Cebu. From vibrant festivals to live music, sports competitions, and community gatherings, there's always something amazing to experience in our beautiful island province.</p>
        </div>
    </section>

    <!-- Featured Events Section -->
    <section class="featured-section">
        <div class="container">
            <div class="section-header">
                <h2>Featured Events</h2>
                <p>Don't miss these highlighted experiences</p>
            </div>

            <div class="events-grid">
                @forelse($featuredEvents as $event)
                    @include('partials.event-card', ['event' => $event])
                @empty
                    <p class="no-events-text">No featured events at the moment.</p>
                @endforelse
            </div>

            <div class="section-footer">
                <a href="{{ url('/events') }}" class="btn btn-secondary">View All Events →</a>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="categories-section">
        <div class="container">
            <div class="section-header">
                <h2>Explore by Category</h2>
                <p>Find events that match your interests</p>
            </div>

            <div class="categories-grid">
                <a href="{{ url('/events?category=Festival') }}" class="category-card" style="background: linear-gradient(135deg, #f59e0b, #d97706)">
                    <span class="category-icon">🎉</span>
                    <h3>Festival</h3>
                </a>
                <a href="{{ url('/events?category=Music') }}" class="category-card" style="background: linear-gradient(135deg, #8b5cf6, #6d28d9)">
                    <span class="category-icon">🎵</span>
                    <h3>Music</h3>
                </a>
                <a href="{{ url('/events?category=Sports') }}" class="category-card" style="background: linear-gradient(135deg, #22c55e, #16a34a)">
                    <span class="category-icon">⚽</span>
                    <h3>Sports</h3>
                </a>
                <a href="{{ url('/events?category=Community') }}" class="category-card" style="background: linear-gradient(135deg, #ec4899, #db2777)">
                    <span class="category-icon">🤝</span>
                    <h3>Community</h3>
                </a>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <h2>Ready to Explore Cebu?</h2>
            <p>Check out our complete calendar of events and plan your next adventure</p>
            <a href="{{ url('/calendar') }}" class="btn btn-primary btn-large">View Event Calendar</a>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    // Hero slideshow
    document.addEventListener('DOMContentLoaded', function() {
        const slides = document.querySelectorAll('.hero-slide');
        let currentSlide = 0;

        setInterval(function() {
            slides[currentSlide].classList.remove('active');
            currentSlide = (currentSlide + 1) % slides.length;
            slides[currentSlide].classList.add('active');
        }, 5000);
    });
</script>
@endpush
