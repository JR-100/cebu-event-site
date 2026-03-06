<nav class="navbar {{ request()->is('/') ? '' : 'solid-bg' }}" id="mainNavbar">
    <div class="navbar-container">
        <a href="{{ url('/') }}" class="navbar-brand">
            <svg class="brand-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
            </svg>
            <span class="brand-text">Cebu Event Site</span>
        </a>

        <button class="mobile-toggle" id="mobileToggle" aria-label="Toggle menu" onclick="toggleMobileMenu()">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <ul class="navbar-menu" id="navbarMenu">
            <li><a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">Home</a></li>
            <li class="dropdown desktop-only">
                <a href="#" class="dropdown-trigger">
                    Discover <span class="arrow">&#9662;</span>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="{{ url('/events?category=Festival') }}" class="dropdown-item"><span class="cat-dot" style="background: #f59e0b;"></span> Festivals</a></li>
                    <li><a href="{{ url('/events?category=Food') }}" class="dropdown-item"><span class="cat-dot" style="background: #f97316;"></span> Food</a></li>
                    <li><a href="{{ url('/events?category=Music') }}" class="dropdown-item"><span class="cat-dot" style="background: #8b5cf6;"></span> Music</a></li>
                    <li><a href="{{ url('/events?category=Workshop') }}" class="dropdown-item"><span class="cat-dot" style="background: #0ea5e9;"></span> Workshop</a></li>
                    <li><a href="{{ url('/events?category=Sports') }}" class="dropdown-item"><span class="cat-dot" style="background: #22c55e;"></span> Sports</a></li>
                    <li><a href="{{ url('/events?category=Art') }}" class="dropdown-item"><span class="cat-dot" style="background: #6366f1;"></span> Art</a></li>
                    <li><a href="{{ url('/events?category=Business') }}" class="dropdown-item"><span class="cat-dot" style="background: #64748b;"></span> Business</a></li>
                    <li><a href="{{ url('/events?category=Community') }}" class="dropdown-item"><span class="cat-dot" style="background: #ec4899;"></span> Community</a></li>
                </ul>
            </li>
            <li class="mobile-only mobile-dropdown" id="mobileDropdown">
                <a href="#" class="mobile-discover-trigger" onclick="event.preventDefault(); toggleMobileDropdown()">
                    Discover
                    <svg class="mobile-arrow" viewBox="0 0 12 12" fill="currentColor"><path d="M2 4.5L6 8.5L10 4.5"/></svg>
                </a>
                <ul class="mobile-dropdown-menu">
                    <li><a href="{{ url('/events?category=Festival') }}" class="mobile-cat-item"><span class="cat-dot" style="background: #f59e0b;"></span> Festivals</a></li>
                    <li><a href="{{ url('/events?category=Food') }}" class="mobile-cat-item"><span class="cat-dot" style="background: #f97316;"></span> Food</a></li>
                    <li><a href="{{ url('/events?category=Music') }}" class="mobile-cat-item"><span class="cat-dot" style="background: #8b5cf6;"></span> Music</a></li>
                    <li><a href="{{ url('/events?category=Workshop') }}" class="mobile-cat-item"><span class="cat-dot" style="background: #0ea5e9;"></span> Workshop</a></li>
                    <li><a href="{{ url('/events?category=Sports') }}" class="mobile-cat-item"><span class="cat-dot" style="background: #22c55e;"></span> Sports</a></li>
                    <li><a href="{{ url('/events?category=Art') }}" class="mobile-cat-item"><span class="cat-dot" style="background: #6366f1;"></span> Art</a></li>
                    <li><a href="{{ url('/events?category=Business') }}" class="mobile-cat-item"><span class="cat-dot" style="background: #64748b;"></span> Business</a></li>
                    <li><a href="{{ url('/events?category=Community') }}" class="mobile-cat-item"><span class="cat-dot" style="background: #ec4899;"></span> Community</a></li>
                </ul>
            </li>
            <li><a href="{{ url('/events') }}" class="{{ request()->is('events') && !request()->has('category') ? 'active' : '' }}">Events</a></li>
            <li><a href="{{ url('/calendar') }}" class="{{ request()->is('calendar') ? 'active' : '' }}">Calendar</a></li>
        </ul>
    </div>
    <div class="menu-overlay" id="menuOverlay" onclick="closeMobileMenu()"></div>
</nav>

<script>
    // Scroll effect for navbar
    window.addEventListener('scroll', function() {
        const navbar = document.getElementById('mainNavbar');
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });

    function toggleMobileMenu() {
        const btn = document.getElementById('mobileToggle');
        const menu = document.getElementById('navbarMenu');
        const overlay = document.getElementById('menuOverlay');
        btn.classList.toggle('open');
        menu.classList.toggle('active');
        overlay.classList.toggle('active');
    }

    function closeMobileMenu() {
        document.getElementById('mobileToggle').classList.remove('open');
        document.getElementById('navbarMenu').classList.remove('active');
        document.getElementById('menuOverlay').classList.remove('active');
        document.getElementById('mobileDropdown')?.classList.remove('mobile-dropdown-open');
    }

    function toggleMobileDropdown() {
        document.getElementById('mobileDropdown').classList.toggle('mobile-dropdown-open');
    }
</script>
