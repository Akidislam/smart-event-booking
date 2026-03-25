@extends('layouts.app')

@section('title', 'Register - EventVenue')

@push('styles')
<style>
    .auth-container {
        min-height: calc(100vh - var(--nav-h) - 200px);
        display: flex; align-items: center; justify-content: center;
        padding: 4rem 1.5rem;
    }
    .auth-card {
        background: var(--bg-card); border: 1px solid var(--border);
        border-radius: var(--radius-lg); padding: 3rem;
        width: 100%; max-width: 540px; box-shadow: var(--shadow);
    }
    .auth-header { text-align: center; margin-bottom: 2.5rem; }
    .auth-header h1 { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 2rem; font-weight: 800; margin-bottom: .5rem; }
    .auth-header p { color: var(--text-muted); font-size: .95rem; }
    .divider { display: flex; align-items: center; text-align: center; margin: 2rem 0; color: var(--text-dim); font-size: .85rem; font-weight: 600; text-transform: uppercase; }
    .divider::before, .divider::after { content: ''; flex: 1; border-bottom: 1px solid var(--border); }
    .divider:not(:empty)::before { margin-right: 1rem; }
    .divider:not(:empty)::after { margin-left: 1rem; }
    .btn-google { background: #fff; color: #1f2937; border: 1px solid #e5e7eb; font-weight: 600; justify-content: center; }
    .btn-google:hover { background: #f9fafb; border-color: #d1d5db; transform: translateY(-1px); }
    .form-error { color: var(--danger); font-size: .8rem; margin-top: .4rem; display: block; }
</style>
@endpush

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1>Create an Account</h1>
            <p>Join thousands of users organizing amazing events</p>
        </div>

        <a href="{{ route('auth.google') }}" class="btn w-full btn-google btn-lg mb-3">
            <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                <path d="M1 1h22v22H1z" fill="none"/>
            </svg>
            Sign up with Google
        </a>

        <div class="divider">Or register with email</div>

        <form action="{{ route('register.post') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Full Name</label>
                <div class="input-icon">
                    <i class="fas fa-user"></i>
                    <input type="text" name="name" class="form-control" placeholder="John Doe" value="{{ old('name') }}" required autofocus>
                </div>
                @error('name')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Email Address</label>
                <div class="input-icon">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" class="form-control" placeholder="name@example.com" value="{{ old('email') }}" required>
                </div>
                @error('email')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group mb-4">
                <label class="form-label">Phone Number (Optional)</label>
                <div class="input-icon">
                    <i class="fas fa-phone"></i>
                    <input type="text" name="phone" class="form-control" placeholder="+880 1XXX-XXXXXX" value="{{ old('phone') }}">
                </div>
                @error('phone')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div class="grid-2 gap-3 mb-4">
                <div class="form-group mb-0">
                    <label class="form-label">Password</label>
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                </div>
                <div class="form-group mb-0">
                    <label class="form-label">Confirm Password</label>
                    <div class="input-icon">
                        <i class="fas fa-check"></i>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="••••••••" required>
                    </div>
                </div>
                <div style="grid-column:1/-1;">
                    @error('password')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-full justify-center">Create Account</button>
        </form>

        <div class="text-center mt-4" style="color:var(--text-muted);font-size:.9rem;">
            Already have an account? <a href="{{ route('login') }}" style="color:var(--primary-light);text-decoration:none;font-weight:600;">Sign in</a>
        </div>
    </div>
</div>
@endsection
