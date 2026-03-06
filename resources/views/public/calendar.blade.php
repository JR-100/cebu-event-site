@extends('layouts.app')

@section('title', 'Calendar - Cebu Event Site')

@section('content')
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
                <div class="calendar-component">
                    <div class="calendar-header">
                        <button type="button" class="nav-btn" onclick="changeMonth(-1)">&lt;</button>
                        <h3 id="calMonthLabel">{{ \Carbon\Carbon::create($year, $month, 1)->format('F Y') }}</h3>
                        <button type="button" class="nav-btn" onclick="changeMonth(1)">&gt;</button>
                    </div>

                    <div class="calendar-legend">
                        <span class="legend-item"><span class="legend-dot festival"></span>Festival</span>
                        <span class="legend-item"><span class="legend-dot food"></span>Food</span>
                        <span class="legend-item"><span class="legend-dot music"></span>Music</span>
                        <span class="legend-item"><span class="legend-dot workshop"></span>Workshop</span>
                        <span class="legend-item"><span class="legend-dot sports"></span>Sports</span>
                        <span class="legend-item"><span class="legend-dot art"></span>Art</span>
                        <span class="legend-item"><span class="legend-dot business"></span>Business</span>
                        <span class="legend-item"><span class="legend-dot community"></span>Community</span>
                    </div>

                    <div class="calendar-weekdays">
                        <span>Sun</span><span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span>
                    </div>

                    <div class="calendar-days">
                        @php
                            $firstDay = \Carbon\Carbon::create($year, $month, 1);
                            $daysInMonth = $firstDay->daysInMonth;
                            $startDow = $firstDay->dayOfWeek;
                            $today = now()->format('Y-m-d');
                            $selectedDate = request('date');

                            // Previous month padding
                            $prevMonthLastDay = \Carbon\Carbon::create($year, $month, 1)->subDay()->day;

                            // Prepare events data for JavaScript
                            $allEventsJs = [];
                            foreach ($calendarEvents as $date => $evts) {
                                $allEventsJs[$date] = $evts->map(fn($e) => [
                                    'id' => $e->id,
                                    'name' => $e->name,
                                    'category' => $e->category,
                                    'location' => $e->location,
                                    'date_time' => $e->date_time->toISOString(),
                                    'description' => $e->description,
                                    'image' => $e->image,
                                    'is_featured' => $e->is_featured,
                                ])->values()->toArray();
                            }
                        @endphp

                        {{-- Previous month days --}}
                        @for($i = $startDow - 1; $i >= 0; $i--)
                            <div class="calendar-day other-month">
                                <span class="day-number">{{ $prevMonthLastDay - $i }}</span>
                            </div>
                        @endfor

                        {{-- Current month days --}}
                        @for($d = 1; $d <= $daysInMonth; $d++)
                            @php
                                $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $d);
                                $dayEvents = $calendarEvents->get($dateStr, collect());
                                $isToday = $dateStr === $today;
                                $isSelected = $dateStr === $selectedDate;
                                $hasEvents = count($dayEvents) > 0;
                                $catClass = '';
                                if ($hasEvents) {
                                    $catClass = 'cat-' . strtolower($dayEvents[0]->category ?? 'default');
                                }
                            @endphp
                            <div class="calendar-day {{ $isToday && !$hasEvents ? 'today' : '' }} {{ $hasEvents ? 'has-events ' . $catClass : '' }} {{ $isSelected ? 'selected' : '' }}"
                                 onclick="selectDate('{{ $dateStr }}', this)"
                            >
                                <span class="day-number">{{ $d }}</span>
                                @if(count($dayEvents) > 1)
                                    <div class="event-count">+{{ count($dayEvents) }}</div>
                                @endif
                            </div>
                        @endfor

                        {{-- Next month padding --}}
                        @php
                            $totalCells = $startDow + $daysInMonth;
                            $remaining = ($totalCells % 7 == 0) ? 0 : 7 - ($totalCells % 7);
                            // Always show 6 rows
                            if($totalCells + $remaining < 42) $remaining = 42 - $totalCells;
                        @endphp
                        @for($i = 1; $i <= $remaining; $i++)
                            <div class="calendar-day other-month">
                                <span class="day-number">{{ $i }}</span>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>

            <!-- Events Panel -->
            <div class="events-panel" id="eventsPanel">
                <div id="panelHeader">
                    <h2>Select a date</h2>
                </div>
                <div class="no-events-small" id="panelEmpty">
                    <p>Click on a date to see events</p>
                </div>
                <div class="events-list-small" id="panelList" style="display:none;"></div>
            </div>
        </div>
    </div>

    <!-- Event Modal -->
    <div class="cal-modal-overlay hidden" id="calendarModal" onclick="if(event.target===this)closeCalendarModal()">
        <div class="cal-modal-content">
            <button class="modal-close" onclick="closeCalendarModal()">&times;</button>
            <div class="modal-image">
                <img id="modalImg" src="" alt="" />
                <span class="featured-badge hidden" id="modalFeatured">⭐ Featured</span>
            </div>
            <div class="modal-body">
                <span class="modal-category" id="modalCategory"></span>
                <h2 id="modalName"></h2>
                <div class="modal-details">
                    <p><strong>📅</strong> <span id="modalDate"></span></p>
                    <p><strong>📍</strong> <span id="modalLocation"></span></p>
                </div>
                <p class="modal-description" id="modalDescription"></p>
                <a id="modalLink" href="#" class="view-full-btn">View Full Details →</a>
            </div>
        </div>
    </div>
</div>

<script>
let calendarEventsData = @json($allEventsJs);
let currentMonth = {{ $month }};
let currentYear = {{ $year }};
let selectedDateStr = null;
let currentDateEvents = [];

const categoryColors = {
    festival: '#f59e0b', food: '#f97316', music: '#8b5cf6', workshop: '#0ea5e9',
    sports: '#22c55e', art: '#6366f1', business: '#64748b', community: '#ec4899'
};

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function changeMonth(delta) {
    currentMonth += delta;
    if (currentMonth > 12) { currentMonth = 1; currentYear++; }
    if (currentMonth < 1) { currentMonth = 12; currentYear--; }

    // Update URL without reload
    history.replaceState(null, '', '/calendar?month=' + currentMonth + '&year=' + currentYear);

    fetch('/calendar/data?month=' + currentMonth + '&year=' + currentYear)
        .then(r => r.json())
        .then(data => {
            calendarEventsData = data.events;
            document.getElementById('calMonthLabel').textContent = data.monthLabel;
            renderCalendarGrid(data);
            clearSelection();
        });
}

function renderCalendarGrid(data) {
    const container = document.querySelector('.calendar-days');
    let html = '';

    // Previous month padding
    for (let i = data.startDow - 1; i >= 0; i--) {
        html += '<div class="calendar-day other-month"><span class="day-number">' + (data.prevMonthLastDay - i) + '</span></div>';
    }

    // Current month days
    for (let d = 1; d <= data.daysInMonth; d++) {
        const dateStr = data.year + '-' + String(data.month).padStart(2, '0') + '-' + String(d).padStart(2, '0');
        const dayEvents = data.events[dateStr] || [];
        const isToday = dateStr === data.today;
        const hasEvents = dayEvents.length > 0;
        let catClass = '';
        if (hasEvents) {
            catClass = 'cat-' + (dayEvents[0].category || 'default').toLowerCase();
        }
        let classes = 'calendar-day';
        if (isToday && !hasEvents) classes += ' today';
        if (hasEvents) classes += ' has-events ' + catClass;

        html += '<div class="' + classes + '" onclick="selectDate(\'' + dateStr + '\', this)">';
        html += '<span class="day-number">' + d + '</span>';
        if (dayEvents.length > 1) {
            html += '<div class="event-count">+' + dayEvents.length + '</div>';
        }
        html += '</div>';
    }

    // Next month padding
    const totalCells = data.startDow + data.daysInMonth;
    let remaining = (totalCells % 7 === 0) ? 0 : 7 - (totalCells % 7);
    if (totalCells + remaining < 42) remaining = 42 - totalCells;
    for (let i = 1; i <= remaining; i++) {
        html += '<div class="calendar-day other-month"><span class="day-number">' + i + '</span></div>';
    }

    container.innerHTML = html;
}

function selectDate(dateStr, el) {
    selectedDateStr = dateStr;
    const events = calendarEventsData[dateStr] || [];
    currentDateEvents = events;

    document.querySelectorAll('.calendar-day').forEach(d => d.classList.remove('selected'));
    el.classList.add('selected');

    const header = document.getElementById('panelHeader');
    const empty = document.getElementById('panelEmpty');
    const list = document.getElementById('panelList');

    const date = new Date(dateStr + 'T00:00:00');
    const formatted = date.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });

    if (events.length === 0) {
        header.innerHTML = '<div class="selected-date-header"><h2>' + formatted + '</h2><button class="clear-btn" onclick="clearSelection()">Clear</button></div>';
        empty.innerHTML = '<p>No events on this date</p>';
        empty.style.display = 'block';
        list.style.display = 'none';
    } else {
        header.innerHTML = '<div class="selected-date-header"><h2>' + formatted + '</h2><button class="clear-btn" onclick="clearSelection()">Clear</button></div>';
        empty.style.display = 'none';
        list.style.display = 'flex';

        let html = '';
        events.forEach(function(ev, idx) {
            const time = new Date(ev.date_time).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
            html += '<div class="event-item-small" onclick="openCalendarModal(' + idx + ')">';
            html += '<div class="event-time">' + time + '</div>';
            html += '<div class="event-info-small">';
            html += '<h4>' + escapeHtml(ev.name) + '</h4>';
            html += '<p>📍 ' + escapeHtml(ev.location) + '</p>';
            html += '<span class="event-category-small">' + escapeHtml(ev.category) + '</span>';
            html += '</div></div>';
        });
        list.innerHTML = html;
    }
}

function clearSelection() {
    selectedDateStr = null;
    currentDateEvents = [];
    document.querySelectorAll('.calendar-day').forEach(d => d.classList.remove('selected'));
    document.getElementById('panelHeader').innerHTML = '<h2>Select a date</h2>';
    document.getElementById('panelEmpty').innerHTML = '<p>Click on a date to see events</p>';
    document.getElementById('panelEmpty').style.display = 'block';
    document.getElementById('panelList').style.display = 'none';
}

function openCalendarModal(idx) {
    const ev = currentDateEvents[idx];
    if (!ev) return;
    const defaultImg = '{{ asset("images/hero-image.jpg") }}';
    document.getElementById('modalImg').src = ev.image ? '/storage/' + ev.image : defaultImg;
    document.getElementById('modalName').textContent = ev.name;
    document.getElementById('modalCategory').textContent = ev.category;
    document.getElementById('modalLocation').textContent = ev.location;
    document.getElementById('modalDescription').textContent = ev.description;
    document.getElementById('modalLink').href = '/events/' + ev.id;
    const modalDate = new Date(ev.date_time).toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit' });
    document.getElementById('modalDate').textContent = modalDate;
    const featuredEl = document.getElementById('modalFeatured');
    if (ev.is_featured) featuredEl.classList.remove('hidden');
    else featuredEl.classList.add('hidden');
    document.getElementById('calendarModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeCalendarModal() {
    document.getElementById('calendarModal').classList.add('hidden');
    document.body.style.overflow = '';
}
</script>
@endsection
