<footer class="footer">
    <div class="footer-container">
        <div class="footer-brand">
            <div class="brand-header">
                <svg class="brand-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                </svg>
                <span class="brand-text">Cebu Events</span>
            </div>
            <p class="footer-tagline">Discover the best events in the Queen City of the South</p>
        </div>

        <div class="footer-links">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="{{ url('/') }}">Home</a></li>
                <li><a href="{{ url('/events') }}">Event List</a></li>
                <li><a href="{{ url('/calendar') }}">Calendar</a></li>
            </ul>
        </div>

        <div class="footer-links">
            <h4>Categories</h4>
            <ul>
                <li><a href="{{ url('/events?category=Festival') }}">Festivals</a></li>
                <li><a href="{{ url('/events?category=Music') }}">Music</a></li>
                <li><a href="{{ url('/events?category=Sports') }}">Sports</a></li>
                <li><a href="{{ url('/events?category=Community') }}">Community</a></li>
            </ul>
        </div>

        <div class="footer-contact">
            <h4>Contact</h4>
            <p>📍 Cebu City, Philippines</p>
            <p>📧 info@cebuevents.com</p>
            <p>📞 +63 32 123 4567</p>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; {{ date('Y') }} Cebu Events. All rights reserved.</p>
    </div>
</footer>
