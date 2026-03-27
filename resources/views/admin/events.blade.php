@extends('layouts.app')

@section('title', 'Manage Events - Admin Portal')

@section('content')
<div class="page-header py-4 mb-4" style="background:var(--bg-card); border-bottom:1px solid var(--border-strong);">
    <div class="container mx-auto px-4 d-flex justify-between align-center">
        <div>
            <h1 style="color:var(--warning); margin-bottom:.25rem;"><i class="fas fa-calendar-alt text-primary-light"></i> Platform Events</h1>
            <p class="text-muted m-0">Oversee all events created across the platform.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary w-full sm:w-auto"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
</div>

<div class="container mx-auto px-4 mb-5">
    
    <div class="card p-0">
        <div class="table-wrap">
            <table style="width:100%; border-collapse:collapse;">
                <thead style="background:var(--bg-surface);">
                    <tr>
                        <th style="padding:1rem 1.5rem;">Event Title & Date</th>
                        <th>Organizer</th>
                        <th>Location</th>
                        <th>Status / Ticketing</th>
                        <th style="text-align:right; padding-right:1.5rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($events as $e)
                    <tr>
                        <td style="padding:1.5rem;">
                            <a href="{{ route('events.show', $e) }}" target="_blank" class="fw-bold fs-md text-primary d-block mb-1" style="text-decoration:none; font-size:1.1rem;">{{ $e->title }}</a>
                            <div class="text-muted fs-sm mb-1"><i class="fas fa-clock"></i> {{ $e->start_datetime->format('M d, Y h:i A') }}</div>
                        </td>
                        <td>
                            <div class="d-flex align-center gap-2">
                                <img class="w-full h-auto object-cover w-full h-auto object-cover" src="{{ $e->user->avatar_url }}" style="width:24px; height:24px; border-radius:50%;" alt="">
                                <div class="fw-bold fs-sm">{{ $e->user->name ?? 'Unknown' }}</div>
                            </div>
                        </td>
                        <td>
                            @if($e->venue)
                                <div class="fs-sm">{{ $e->venue->name }}</div>
                            @else
                                <div class="text-muted fs-sm"><i class="fas fa-globe text-info"></i> Online / Details TBA</div>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $e->status === 'published' ? 'badge-success' : 'badge-secondary' }} mb-1" style="font-size:.65rem; padding:.2rem .4rem;">{{ strtoupper($e->status) }}</span>
                            <div class="fs-sm text-muted mt-1">{{ $e->is_free ? 'Free Event' : $e->ticket_price_formatted }}</div>
                            <div class="fs-sm text-muted">Attendees: <strong class="{{ $e->attendees_count > 0 ? 'text-success' : '' }}">{{ $e->attendees_count }}</strong></div>
                        </td>
                        <td style="text-align:right; padding-right:1.5rem; vertical-align:middle;">
                            <div class="d-flex justify-between gap-1" style="justify-content:flex-end;">
                                <a href="{{ route('events.show', $e) }}" target="_blank" class="btn btn-outline btn-sm w-full sm:w-auto" title="View Public Page"><i class="fas fa-eye"></i></a>
                                
                                <form action="{{ route('events.destroy', $e) }}" method="POST" onsubmit="return confirm('As an admin, are you sure you want to delete this user event?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline btn-sm border-danger text-danger w-full sm:w-auto" title="Force Delete Event"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-5 text-muted"><i class="fas fa-calendar-times mb-3" style="font-size:3rem;opacity:.3;display:block;"></i> No events found in the database.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="d-flex justify-center mt-4">
        {{ $events->links() }}
    </div>
</div>
@endsection
