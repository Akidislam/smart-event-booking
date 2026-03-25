<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_reference',
        'user_id',
        'venue_id',
        'event_id',
        'start_datetime',
        'end_datetime',
        'attendees',
        'total_amount',
        'status',
        'payment_status',
        'payment_method',
        'special_requests',
        'google_calendar_event_id',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($booking) {
            $booking->booking_reference = 'BK-' . strtoupper(Str::random(8));
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function getTotalAmountFormattedAttribute(): string
    {
        return '৳' . number_format($this->total_amount, 0);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
                'confirmed' => '<span class="badge-success">Confirmed</span>',
                'cancelled' => '<span class="badge-danger">Cancelled</span>',
                'completed' => '<span class="badge-info">Completed</span>',
                default => '<span class="badge-warning">Pending</span>',
            };
    }
}
