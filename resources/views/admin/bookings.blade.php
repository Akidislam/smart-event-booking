@extends('layouts.app')

@section('title', 'Manage Bookings - Admin Portal')

@section('content')
<div class="page-header py-4 mb-4" style="background:var(--bg-card); border-bottom:1px solid var(--border-strong);">
    <div class="container d-flex justify-between align-center">
        <div>
            <h1 style="color:var(--warning); margin-bottom:.25rem;"><i class="fas fa-money-bill-transfer text-success"></i> Central Booking Ledger</h1>
            <p class="text-muted m-0">Monitor all transactions, bookings, and reservations System-wide.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
</div>

<div class="container mb-5">
    
    <div class="card p-0">
        <div class="table-wrap">
            <table style="width:100%; border-collapse:collapse;">
                <thead style="background:var(--bg-surface);">
                    <tr>
                        <th style="padding:1rem 1.5rem;">Reference & User</th>
                        <th>Target</th>
                        <th>Date & Details</th>
                        <th>Financials</th>
                        <th style="text-align:right; padding-right:1.5rem;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $b)
                    <tr>
                        <td style="padding:1.5rem;">
                            <a href="{{ route('bookings.show', $b) }}" target="_blank" class="fw-bold d-block mb-1" style="font-family:monospace;letter-spacing:1px;">{{ $b->booking_reference }}</a>
                            <div class="d-flex align-center gap-2 mt-2">
                                <img src="{{ $b->user->avatar_url }}" style="width:20px; height:20px; border-radius:50%;" alt="">
                                <span class="fs-sm text-muted">{{ $b->user->name }}</span>
                            </div>
                        </td>
                        <td>
                            @if($b->event)
                                <div class="badge badge-success mb-1" style="font-size:.65rem; padding:.2rem .4rem;">EVENT</div>
                                <div class="fs-sm fw-bold"><a href="{{ route('events.show', $b->event) }}" target="_blank" style="text-decoration:none;">{{ str()->limit($b->event->title, 30) }}</a></div>
                            @elseif($b->venue)
                                <div class="badge badge-info mb-1" style="font-size:.65rem; padding:.2rem .4rem;">VENUE</div>
                                <div class="fs-sm fw-bold"><a href="{{ route('venues.show', $b->venue) }}" target="_blank" style="text-decoration:none;">{{ str()->limit($b->venue->name, 30) }}</a></div>
                            @endif
                        </td>
                        <td>
                            <div class="fs-sm fw-bold">{{ $b->start_datetime->format('M d, Y') }}</div>
                            <div class="fs-sm text-muted mb-1">{{ $b->start_datetime->format('h:i A') }}</div>
                            <div class="fs-sm text-muted"><i class="fas fa-users"></i> {{ $b->attendees }} Pax</div>
                        </td>
                        <td>
                            <div class="fw-bold fs-md">{{ $b->total_amount_formatted }}</div>
                            <div class="mt-1">
                                @if($b->payment_status === 'paid') <span class="badge badge-success" style="font-size:.65rem; padding:.2rem .4rem;">PAID</span>
                                @else <span class="badge badge-warning" style="font-size:.65rem; padding:.2rem .4rem;">UNPAID</span> @endif
                            </div>
                        </td>
                        <td style="text-align:right; padding-right:1.5rem;">
                            <div class="d-flex flex-column align-end">
                                {!! $b->status_badge !!}
                                <div class="text-muted fs-sm mt-2">Processed On<br>{{ $b->created_at->format('M d') }}</div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-5 text-muted"><i class="fas fa-file-invoice-dollar mb-3" style="font-size:3rem;opacity:.3;display:block;"></i> No bookings found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="d-flex justify-center mt-4">
        {{ $bookings->links() }}
    </div>
</div>
@endsection
