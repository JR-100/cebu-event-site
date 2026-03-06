@extends('layouts.admin')

@section('header-title', 'Profile Settings')

@section('content')
<div class="profile-page">
    <!-- Profile Info Card -->
    <div class="profile-card">
        <form id="profileImageForm" method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data" style="display:none;">
            @csrf
            @method('PUT')
            <input type="hidden" name="name" value="{{ auth()->user()->name }}" />
            <input type="hidden" name="email" value="{{ auth()->user()->email }}" />
            <input type="file" id="avatarFileInput" name="profile_image" accept="image/jpeg,image/png,image/gif,image/webp" onchange="document.getElementById('profileImageForm').submit()" />
        </form>

        <div class="profile-avatar" onclick="document.getElementById('avatarFileInput').click()" style="cursor:pointer;">
            @if(auth()->user()->profile_image)
                <img src="{{ asset('storage/' . auth()->user()->profile_image) }}" alt="Profile" class="avatar-img" />
            @else
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="avatar-placeholder">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
            @endif
            <div class="avatar-overlay">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                    <circle cx="12" cy="13" r="4"/>
                </svg>
            </div>
        </div>
        <div class="profile-info">
            <h2>{{ auth()->user()->name ?? 'Admin' }}</h2>
            <p>{{ auth()->user()->email }}</p>
            @if(auth()->user()->profile_image)
                <form method="POST" action="{{ route('admin.profile.update') }}" style="display:inline;">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="name" value="{{ auth()->user()->name }}" />
                    <input type="hidden" name="email" value="{{ auth()->user()->email }}" />
                    <input type="hidden" name="remove_image" value="1" />
                    <button type="submit" class="remove-photo-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                        </svg>
                        Remove Photo
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('profile_success'))
        <div class="alert alert-success">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ session('profile_success') }}
        </div>
    @endif
    @if($errors->any() && !$errors->has('current_password') && !$errors->has('password'))
        <div class="alert alert-error">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="forms-grid">
        <!-- Edit Profile Form -->
        <div class="form-section">
            <div class="form-section-header">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
                <h3>Edit Profile</h3>
            </div>

            <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data" class="profile-form">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', auth()->user()->name) }}" placeholder="Enter your name" required />
                    @error('name') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" placeholder="Enter your email" required />
                    @error('email') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <button type="submit" class="save-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                        <polyline points="7 3 7 8 15 8"/>
                    </svg>
                    Save Changes
                </button>
            </form>
        </div>

        <!-- Change Password Form -->
        <div class="form-section">
            <div class="form-section-header">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                <h3>Change Password</h3>
            </div>

            @if(session('password_success'))
                <div class="alert alert-success inline">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    {{ session('password_success') }}
                </div>
            @endif
            @if($errors->has('current_password') || $errors->has('password'))
                <div class="alert alert-error inline">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    @error('current_password') <p>{{ $message }}</p> @enderror
                    @error('password') <p>{{ $message }}</p> @enderror
                </div>
            @endif

            <form method="POST" action="{{ route('admin.password.update') }}" class="profile-form">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password" placeholder="Enter current password" required />
                </div>

                <div class="form-group">
                    <label for="password">New Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter new password" required />
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirm New Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm new password" required />
                </div>

                <button type="submit" class="save-btn password-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    Update Password
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
