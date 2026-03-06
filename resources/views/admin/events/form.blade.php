@extends('layouts.admin')

@section('header-title', isset($event) ? 'Edit Event' : 'Create Event')

@section('content')
<div class="event-form-page">
    <div class="section-header-admin">
        <h2>{{ isset($event) ? 'Edit Event' : 'Create New Event' }}</h2>
        <a href="{{ route('admin.events.index') }}" class="btn btn-sm btn-outline">← Back to Events</a>
    </div>

    @if($errors->any())
        <div class="alert alert-error">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        method="POST"
        action="{{ isset($event) ? route('admin.events.update', $event) : route('admin.events.store') }}"
        enctype="multipart/form-data"
        class="event-form"
    >
        @csrf
        @if(isset($event))
            @method('PUT')
        @endif

        <div class="form-grid">
            <div class="form-group">
                <label for="name">Event Name *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $event->name ?? '') }}" required />
            </div>

            <div class="form-group">
                <label for="location">Location *</label>
                <input type="text" id="location" name="location" value="{{ old('location', $event->location ?? '') }}" required />
            </div>

            <div class="form-group">
                <label for="date_time">Date & Time *</label>
                <input type="datetime-local" id="date_time" name="date_time" value="{{ old('date_time', isset($event) ? $event->date_time->format('Y-m-d\TH:i') : '') }}" required />
            </div>

            <div class="form-group">
                <label for="category">Category *</label>
                <select id="category" name="category" required>
                    <option value="">Select Category</option>
                    @foreach(['Festival', 'Music', 'Sports', 'Community'] as $cat)
                        <option value="{{ $cat }}" {{ old('category', $event->category ?? '') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group full-width">
                <label for="description">Description *</label>
                <textarea id="description" name="description" rows="6" required>{{ old('description', $event->description ?? '') }}</textarea>
            </div>

            <div class="form-group full-width">
                <label for="image">Event Image</label>
                <div class="image-upload">
                    @if(isset($event) && $event->image)
                        <div class="image-preview" id="imagePreview">
                            <img src="{{ asset('storage/' . $event->image) }}" alt="Current image" />
                            <button type="button" class="remove-image" onclick="removeImage()">×</button>
                        </div>
                    @endif
                    <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" onchange="previewImage(this)" />
                    @if(isset($event) && $event->image)
                        <input type="hidden" name="remove_image" id="removeImageFlag" value="0" />
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label class="checkbox-label">
                    <input type="hidden" name="is_featured" value="0" />
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $event->is_featured ?? false) ? 'checked' : '' }} />
                    <span>Featured Event</span>
                </label>
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('admin.events.index') }}" class="btn btn-outline">Cancel</a>
            <button type="submit" class="btn btn-primary">{{ isset($event) ? 'Update Event' : 'Create Event' }}</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                let preview = document.getElementById('imagePreview');
                if (!preview) {
                    preview = document.createElement('div');
                    preview.id = 'imagePreview';
                    preview.className = 'image-preview';
                    input.parentElement.prepend(preview);
                }
                preview.innerHTML = `<img src="${e.target.result}" alt="Preview" /><button type="button" class="remove-image" onclick="removeImage()">×</button>`;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removeImage() {
        const preview = document.getElementById('imagePreview');
        const flag = document.getElementById('removeImageFlag');
        if (preview) preview.remove();
        if (flag) flag.value = '1';
        document.getElementById('image').value = '';
    }
</script>
@endpush
