@extends('layouts.admin')

@section('header-title', 'Dashboard')

@section('content')
<div class="dashboard">
    <!-- Quick Actions -->
    <div class="quick-actions">
        <a href="javascript:void(0)" onclick="openDashboardCreateModal()" class="action-card">
            <div class="action-icon blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            </div>
            <div class="action-text">
                <h3>Add New Event</h3>
                <p>Create a new event listing</p>
            </div>
        </a>
        <a href="{{ route('admin.events.index') }}" class="action-card">
            <div class="action-icon purple">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="9" y1="2" x2="9" y2="6"/><line x1="15" y1="2" x2="15" y2="6"/></svg>
            </div>
            <div class="action-text">
                <h3>Manage Events</h3>
                <p>Edit or remove events</p>
            </div>
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <div class="stat-info">
                <h3>{{ $totalEvents }}</h3>
                <p>Total Events</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/></svg>
            </div>
            <div class="stat-info">
                <h3>{{ $upcomingEvents }}</h3>
                <p>Upcoming Events</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon yellow">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            </div>
            <div class="stat-info">
                <h3>{{ $featuredEvents }}</h3>
                <p>Featured Events</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon purple">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            </div>
            <div class="stat-info">
                <h3>{{ $categoriesCount }}</h3>
                <p>Categories</p>
            </div>
        </div>
    </div>

    <!-- Recent Events -->
    <div class="recent-section">
        <div class="section-header-admin">
            <h2>Recent Events</h2>
            <a href="{{ route('admin.events.index') }}" class="view-all-link">View All →</a>
        </div>
        <table class="events-table">
            <thead>
                <tr>
                    <th>Event Name</th>
                    <th>Category</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentEvents as $event)
                    <tr>
                        <td>
                            <div class="event-cell">
                                @if($event->is_featured)
                                    <span class="featured-star-icon">⭐</span>
                                @endif
                                <span>{{ $event->name }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="category-tag {{ strtolower($event->category) }}">{{ $event->category }}</span>
                        </td>
                        <td>{{ $event->date_time->format('M d, Y') }}</td>
                        <td>
                            @if($event->date_time >= now())
                                <span class="status-text upcoming">Upcoming</span>
                            @else
                                <span class="status-text past">Past</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted" style="padding: 2rem;">No events yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- CREATE Event Modal -->
<div class="modal-overlay hidden" id="dashboardFormModal">
    <div class="modal-content form-modal event-form-modal">
        <div class="form-modal-header">
            <h3>Create New Event</h3>
            <button class="modal-close-btn" onclick="closeDashboardModal()">&times;</button>
        </div>

        <div class="error-message" id="dashFormError" style="display:none;"></div>

        <form id="dashEventForm" method="POST" action="{{ route('admin.events.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="form-grid">
                <div class="form-group full-width">
                    <label for="dash_name">Event Name *</label>
                    <input type="text" id="dash_name" name="name" placeholder="Enter event name" required />
                </div>

                <div class="form-group">
                    <label for="dash_category">Category *</label>
                    <select id="dash_category" name="category" required>
                        <option value="">Select category</option>
                        <option value="Festival">Festival</option>
                        <option value="Food">Food</option>
                        <option value="Music">Music</option>
                        <option value="Workshop">Workshop</option>
                        <option value="Sports">Sports</option>
                        <option value="Art">Art</option>
                        <option value="Business">Business</option>
                        <option value="Community">Community</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="dash_date_time">Date & Time *</label>
                    <input type="datetime-local" id="dash_date_time" name="date_time" required />
                </div>

                <div class="form-group full-width">
                    <label for="dash_location">Location *</label>
                    <input type="text" id="dash_location" name="location" placeholder="Enter venue/location" required />
                </div>

                <div class="form-group full-width">
                    <label for="dash_description">Description *</label>
                    <textarea id="dash_description" name="description" placeholder="Describe the event..." rows="4" required></textarea>
                </div>

                <div class="form-group full-width">
                    <label>Event Image</label>
                    <div class="image-upload">
                        <div class="image-preview" id="dashImagePreview" style="display:none;">
                            <img id="dashPreviewImg" src="" alt="Preview" />
                            <button type="button" class="remove-image" onclick="removeDashImage()">&times;</button>
                        </div>
                        <div class="upload-placeholder" id="dashUploadPlaceholder" onclick="document.getElementById('dash_image').click()">
                            <svg class="upload-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                            <span>Click to upload image</span>
                            <span class="upload-hint">JPEG, PNG, GIF, WEBP (max 2MB)</span>
                        </div>
                        <input type="file" id="dash_image" name="image" accept="image/jpeg,image/png,image/gif,image/webp" hidden onchange="previewDashImage(this)" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="hidden" name="is_featured" value="0" />
                        <input type="checkbox" id="dash_featured" name="is_featured" value="1" />
                        <span class="checkmark"></span>
                        Mark as Featured Event
                    </label>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="cancel-btn" onclick="closeDashboardModal()">Cancel</button>
                <button type="submit" class="submit-btn">Create Event</button>
            </div>
        </form>
    </div>
</div>

<script>
function openDashboardCreateModal() {
    document.getElementById('dashEventForm').reset();
    removeDashImage();
    document.getElementById('dashFormError').style.display = 'none';
    document.getElementById('dashboardFormModal').classList.remove('hidden');
}

function closeDashboardModal() {
    document.getElementById('dashboardFormModal').classList.add('hidden');
}

function previewDashImage(input) {
    if (input.files && input.files[0]) {
        if (input.files[0].size > 2 * 1024 * 1024) {
            alert('Image size must be less than 2MB');
            input.value = '';
            return;
        }
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('dashPreviewImg').src = e.target.result;
            document.getElementById('dashImagePreview').style.display = 'block';
            document.getElementById('dashUploadPlaceholder').style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function removeDashImage() {
    document.getElementById('dashImagePreview').style.display = 'none';
    document.getElementById('dashUploadPlaceholder').style.display = 'flex';
    document.getElementById('dash_image').value = '';
    document.getElementById('dashPreviewImg').src = '';
}

document.getElementById('dashboardFormModal').addEventListener('click', function(e) {
    if (e.target === this) closeDashboardModal();
});
</script>
@endsection
