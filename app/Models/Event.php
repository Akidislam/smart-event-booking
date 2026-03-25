<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category',
        'banner_image',
        'start_datetime',
        'end_datetime',
        'max_attendees',
        'ticket_price',
        'is_free',
        'is_public',
        'status',
        'google_calendar_event_id',
        'venue_id',
        'user_id',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'is_free' => 'boolean',
        'is_public' => 'boolean',
        'ticket_price' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function getTicketPriceFormattedAttribute(): string
    {
        return $this->is_free ? 'Free' : '৳' . number_format($this->ticket_price, 0);
    }

    public function getDurationAttribute(): string
    {
        $diff = $this->start_datetime->diff($this->end_datetime);
        if ($diff->h > 0) {
            return $diff->h . 'h ' . ($diff->i > 0 ? $diff->i . 'm' : '');
        }
        return $diff->i . ' minutes';
    }

    public function getAttendeesCountAttribute(): int
    {
        return $this->bookings()->where('status', '!=', 'cancelled')->sum('attendees');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_datetime', '>=', now());
    }

    public static function categories(): array
    {
        return [
            'conference' => 'Conference',
            'wedding' => 'Wedding',
            'concert' => 'Concert',
            'birthday' => 'Birthday Party',
            'corporate' => 'Corporate',
            'workshop' => 'Workshop',
            'networking' => 'Networking',
            'exhibition' => 'Exhibition',
        ];
    }
}
