@extends('layouts.admin')

@section('header-title', 'Events Management')

@section('content')
<div class="events-management">
    <!-- Controls -->
    <div class="controls">
        <div class="search-box">
            <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" id="searchInput" value="{{ request('search') }}" placeholder="Search events..." onkeydown="if(event.key==='Enter')searchEvents()" />
        </div>
        <div class="controls-right">
            <button class="featured-toggle {{ request('featured') ? 'active' : '' }}" onclick="toggleFeaturedFilter()" title="Filter featured events">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                Featured
            </button>
            <button class="add-btn" onclick="openCreateModal()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add New Event
            </button>
        </div>
    </div>

    <!-- Events Table -->
    <div class="table-container">
        <table class="events-table">
            <thead>
                <tr>
                    <th>Event</th>
                    <th>Date</th>
                    <th>Category</th>
                    <th>Location</th>
                    <th>Featured</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($events as $event)
                    <tr>
                        <td>
                            <div class="event-cell">
                                <img src="{{ $event->image ? asset('storage/' . $event->image) : asset('images/hero-image.jpg') }}" alt="{{ $event->name }}" class="event-thumb" />
                                <span>{{ $event->name }}</span>
                            </div>
                        </td>
                        <td>{{ $event->date_time->format('M d, Y g:i A') }}</td>
                        <td>
                            <span class="category-tag {{ strtolower($event->category) }}">{{ $event->category }}</span>
                        </td>
                        <td style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $event->location }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.events.toggle-featured', $event) }}" style="display:inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="featured-btn {{ $event->is_featured ? 'active' : '' }}" title="Toggle featured">
                                    <svg viewBox="0 0 24 24" fill="{{ $event->is_featured ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                </button>
                            </form>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="action-btn edit" onclick="openEditModal({{ $event->id }}, '{{ addslashes($event->name) }}', '{{ $event->category }}', '{{ $event->date_time->format('Y-m-d\TH:i') }}', '{{ addslashes($event->location) }}', `{{ addslashes($event->description) }}`, {{ $event->is_featured ? 'true' : 'false' }}, '{{ $event->image }}')" title="Edit">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    <span>Edit</span>
                                </button>
                                <button class="action-btn delete" onclick="openDeleteModal({{ $event->id }}, '{{ addslashes($event->name) }}')" title="Delete">
                                    <svg viewBox="0 0 448 512" fill="currentColor"><path d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z"/></svg>
                                    <span>Delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty-state">No events found.</td></tr>
                @endforelse
            </tbody>
        </table>

        @if($events->hasPages())
            <div class="pagination-wrapper">
                {{ $events->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

<!-- CREATE/EDIT Event Modal -->
<div class="modal-overlay hidden" id="formModal">
    <div class="modal-content form-modal event-form-modal">
        <div class="form-modal-header">
            <h3 id="formModalTitle">Create New Event</h3>
            <button class="modal-close-btn" onclick="closeFormModal()">&times;</button>
        </div>

        <div class="error-message" id="formError" style="display:none;"></div>

        <form id="eventForm" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="formMethod" name="_method" value="POST" />

            <div class="form-grid">
                <div class="form-group full-width">
                    <label for="modal_name">Event Name *</label>
                    <input type="text" id="modal_name" name="name" placeholder="Enter event name" required />
                </div>

                <div class="form-group">
                    <label for="modal_category">Category *</label>
                    <select id="modal_category" name="category" required>
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
                    <label for="modal_date_time">Date & Time *</label>
                    <input type="datetime-local" id="modal_date_time" name="date_time" required />
                </div>

                <div class="form-group full-width">
                    <label for="modal_location">Location *</label>
                    <input type="text" id="modal_location" name="location" placeholder="Enter venue/location" required />
                </div>

                <div class="form-group full-width">
                    <label for="modal_description">Description *</label>
                    <textarea id="modal_description" name="description" placeholder="Describe the event..." rows="4" required></textarea>
                </div>

                <div class="form-group full-width">
                    <label>Event Image</label>
                    <div class="image-upload">
                        <div class="image-preview" id="modalImagePreview" style="display:none;">
                            <img id="modalPreviewImg" src="" alt="Preview" />
                            <button type="button" class="remove-image" onclick="removeModalImage()">&times;</button>
                        </div>
                        <div class="upload-placeholder" id="modalUploadPlaceholder" onclick="document.getElementById('modal_image').click()">
                            <svg class="upload-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                            <span>Click to upload image</span>
                            <span class="upload-hint">JPEG, PNG, GIF, WEBP (max 2MB)</span>
                        </div>
                        <input type="file" id="modal_image" name="image" accept="image/jpeg,image/png,image/gif,image/webp" hidden onchange="previewModalImage(this)" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="hidden" name="is_featured" value="0" />
                        <input type="checkbox" id="modal_featured" name="is_featured" value="1" />
                        <span class="checkmark"></span>
                        Mark as Featured Event
                    </label>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="cancel-btn" onclick="closeFormModal()">Cancel</button>
                <button type="submit" class="submit-btn" id="formSubmitBtn">Create Event</button>
            </div>
        </form>
    </div>
</div>

<!-- DELETE Confirmation Modal -->
<div class="modal-overlay hidden" id="deleteModal">
    <div class="modal-content delete-modal">
        <div class="modal-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        </div>
        <h3>Delete Event</h3>
        <p>Are you sure you want to delete "<span id="deleteEventName"></span>"? This action cannot be undone.</p>
        <form id="deleteForm" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-actions">
                <button type="button" class="cancel-btn" onclick="closeDeleteModal()">Cancel</button>
                <button type="submit" class="delete-confirm-btn">
                    <svg viewBox="0 0 448 512" fill="currentColor"><path d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z"/></svg>
                    Delete
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Live search with debounce
    let searchTimer = null;
    const searchInput = document.getElementById('searchInput');

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => liveSearch(), 300);
    });

    function searchEvents() {
        liveSearch();
    }

    function liveSearch() {
        const search = searchInput.value.trim();
        const url = new URL(window.location.href);
        if (search) url.searchParams.set('search', search);
        else url.searchParams.delete('search');
        url.searchParams.delete('page');

        fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTable = doc.querySelector('.table-container');
                if (newTable) {
                    document.querySelector('.table-container').innerHTML = newTable.innerHTML;
                }
                // Update URL without reload
                history.replaceState(null, '', url.toString());
            });
    }

    // Featured filter
    function toggleFeaturedFilter() {
        const url = new URL(window.location.href);
        if (url.searchParams.has('featured')) url.searchParams.delete('featured');
        else url.searchParams.set('featured', '1');
        window.location.href = url.toString();
    }

    // CREATE modal
    function openCreateModal() {
        document.getElementById('formModalTitle').textContent = 'Create New Event';
        document.getElementById('formSubmitBtn').textContent = 'Create Event';
        document.getElementById('eventForm').action = "{{ route('admin.events.store') }}";
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('modal_name').value = '';
        document.getElementById('modal_category').value = '';
        document.getElementById('modal_date_time').value = '';
        document.getElementById('modal_location').value = '';
        document.getElementById('modal_description').value = '';
        document.getElementById('modal_featured').checked = false;
        removeModalImage();
        document.getElementById('formError').style.display = 'none';
        document.getElementById('formModal').classList.remove('hidden');
    }

    // EDIT modal
    function openEditModal(id, name, category, dateTime, location, description, isFeatured, image) {
        document.getElementById('formModalTitle').textContent = 'Edit Event';
        document.getElementById('formSubmitBtn').textContent = 'Update Event';
        document.getElementById('eventForm').action = "/admin/events/" + id;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('modal_name').value = name;
        document.getElementById('modal_category').value = category;
        document.getElementById('modal_date_time').value = dateTime;
        document.getElementById('modal_location').value = location;
        document.getElementById('modal_description').value = description;
        document.getElementById('modal_featured').checked = isFeatured;

        if (image) {
            document.getElementById('modalPreviewImg').src = '/storage/' + image;
            document.getElementById('modalImagePreview').style.display = 'block';
            document.getElementById('modalUploadPlaceholder').style.display = 'none';
        } else {
            removeModalImage();
        }

        document.getElementById('formError').style.display = 'none';
        document.getElementById('formModal').classList.remove('hidden');
    }

    function closeFormModal() {
        document.getElementById('formModal').classList.add('hidden');
    }

    // Image preview
    function previewModalImage(input) {
        if (input.files && input.files[0]) {
            if (input.files[0].size > 2 * 1024 * 1024) {
                alert('Image size must be less than 2MB');
                input.value = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('modalPreviewImg').src = e.target.result;
                document.getElementById('modalImagePreview').style.display = 'block';
                document.getElementById('modalUploadPlaceholder').style.display = 'none';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removeModalImage() {
        document.getElementById('modalImagePreview').style.display = 'none';
        document.getElementById('modalUploadPlaceholder').style.display = 'flex';
        document.getElementById('modal_image').value = '';
        document.getElementById('modalPreviewImg').src = '';
    }

    // DELETE modal
    function openDeleteModal(id, name) {
        document.getElementById('deleteEventName').textContent = name;
        document.getElementById('deleteForm').action = "/admin/events/" + id;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }

    // Close modals on overlay click
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });
    });
</script>
@endpush
