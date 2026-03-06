@extends('layouts.app')

@section('title', 'Events - Cebu Event Site')

@section('content')
<div class="event-list-page">
    <div class="page-header">
        <h1>Event List</h1>
        <p>Browse all upcoming events in Cebu</p>
    </div>

    <div class="container">
        <!-- Filter Bar -->
        <div class="filter-bar">
            <form method="GET" action="{{ url('/events') }}" class="filter-form">
                <div class="search-box">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search events..." class="search-input" />
                </div>
                <div class="category-filter">
                    <select name="category" onchange="this.form.submit()">
                        <option value="all" {{ request('category', 'all') == 'all' ? 'selected' : '' }}>All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        @if($events->isEmpty())
            <div class="no-events">
                <span class="no-events-icon">📭</span>
                <h3>No events found</h3>
                <p>Check back later for upcoming events</p>
            </div>
        @else
            <div class="events-list">
                @php $shownUpcoming = false; $shownPast = false; @endphp
                @foreach($events as $event)
                    @php $isPast = $event->date_time < now(); @endphp

                    @if(!$isPast && !$shownUpcoming)
                        <div class="section-label upcoming-label"><span>Upcoming Events</span></div>
                        @php $shownUpcoming = true; @endphp
                    @endif

                    @if($isPast && !$shownPast)
                        <div class="section-label past-label"><span>Past Events</span></div>
                        @php $shownPast = true; @endphp
                    @endif

                    <div class="event-list-item {{ $isPast ? 'past-event' : '' }}">
                        <div class="event-image">
                            <img src="{{ $event->image ? asset('storage/' . $event->image) : asset('images/hero-image.jpg') }}" alt="{{ $event->name }}" />
                            @if($event->is_featured)
                                <span class="featured-badge">⭐ Featured</span>
                            @endif
                            <span class="status-badge {{ $isPast ? 'past' : 'upcoming' }}">
                                {{ $isPast ? 'Past' : 'Upcoming' }}
                            </span>
                        </div>
                        <div class="event-details">
                            <div class="event-meta-top">
                                <span class="event-category-badge {{ strtolower($event->category) }}">{{ $event->category }}</span>
                                <span class="event-date">📅 {{ $event->date_time->format('D, M j, Y, g:i A') }}</span>
                            </div>
                            <h3><a href="{{ url('/events/' . $event->id) }}">{{ $event->name }}</a></h3>
                            <span class="event-location">📍 {{ $event->location }}</span>
                            <p class="event-description">{{ Str::limit($event->description, 150) }}</p>
                        </div>
                        <div class="event-action">
                            <a href="{{ url('/events/' . $event->id) }}" class="view-btn">View Details</a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pagination-wrapper">
                {{ $events->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
