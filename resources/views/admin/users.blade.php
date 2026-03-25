@extends('layouts.app')

@section('title', 'Manage Users - Admin Portal')

@section('content')
<div class="page-header py-4 mb-4" style="background:var(--bg-card); border-bottom:1px solid var(--border-strong);">
    <div class="container d-flex justify-between align-center">
        <div>
            <h1 style="color:var(--warning); margin-bottom:.25rem;"><i class="fas fa-users-gear"></i> User Management</h1>
            <p class="text-muted m-0">View, modify roles, and manage all platform accounts.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
</div>

<div class="container mb-5">
    
    <!-- Search / Filter -->
    <div class="card mb-4 p-3">
        <form action="{{ route('admin.users') }}" method="GET" class="d-flex gap-3">
            <div class="input-icon" style="flex:1;">
                <i class="fas fa-search"></i>
                <input type="text" name="search" class="form-control pl-4" placeholder="Search by name or email..." value="{{ request('search') }}">
            </div>
            <select name="role" class="form-control" style="width:200px;" onchange="this.form.submit()">
                <option value="">All Roles</option>
                <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>Standard Users</option>
                <option value="venue_owner" {{ request('role') == 'venue_owner' ? 'selected' : '' }}>Venue Owners</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Administrators</option>
            </select>
            <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Apply</button>
        </form>
    </div>

    <!-- Table -->
    <div class="card p-0">
        <div class="table-wrap">
            <table>
                <thead style="background:var(--bg-surface);">
                    <tr>
                        <th style="padding:1rem 1.5rem; width:50px;">ID</th>
                        <th>User Profile</th>
                        <th>Contact / Phone</th>
                        <th>Registered On</th>
                        <th>Current Role</th>
                        <th style="text-align:right; padding-right:1.5rem;">Update Role</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                    <tr>
                        <td style="padding-left:1.5rem; color:var(--text-muted);">#{{ $u->id }}</td>
                        <td>
                            <div class="d-flex align-center gap-3">
                                <img src="{{ $u->avatar_url }}" style="width:40px;height:40px;border-radius:50%; border:1px solid var(--border);" alt="">
                                <div>
                                    <div class="fw-bold">{{ $u->name }}</div>
                                    <div class="text-muted fs-sm">{{ $u->email }}</div>
                                    @if($u->google_id)
                                        <div title="Google Authenticated" class="mt-1"><i class="fab fa-google text-primary" style="font-size:.7rem;"></i> <span style="font-size:.7rem;color:var(--text-muted)">OAuth User</span></div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fs-sm">{{ $u->phone ?? 'Not provided' }}</div>
                        </td>
                        <td class="fs-sm text-muted">
                            {{ $u->created_at->format('M d, Y h:ia') }}
                        </td>
                        <td>
                            <span class="badge {{ $u->role === 'admin' ? 'badge-danger' : ($u->role === 'venue_owner' ? 'badge-warning' : 'badge-primary') }}" style="font-size:.7rem;">
                                {{ strtoupper($u->role) }}
                            </span>
                        </td>
                        <td style="text-align:right; padding-right:1.5rem;">
                            @if($u->id !== auth()->id())
                                <form action="{{ route('admin.users.role', $u) }}" method="POST" class="d-flex gap-2" style="justify-content:flex-end;">
                                    @csrf @method('PUT')
                                    <select name="role" class="form-control" style="width:auto; padding:.4rem; font-size:.8rem; height:auto;">
                                        <option value="user" {{ $u->role == 'user' ? 'selected' : '' }}>User</option>
                                        <option value="venue_owner" {{ $u->role == 'venue_owner' ? 'selected' : '' }}>Venue Owner</option>
                                        <option value="admin" {{ $u->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                    </select>
                                    <button type="submit" class="btn btn-outline btn-sm" style="padding:.4rem .7rem;">Save</button>
                                </form>
                            @else
                                <span class="fs-sm text-muted">Current User (Protected)</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-5 text-muted">No users found matching your criteria.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="d-flex justify-center mt-4">
        {{ $users->links() }}
    </div>
</div>
@endsection
