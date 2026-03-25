<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'address',
        'city',
        'state',
        'country',
        'latitude',
        'longitude',
        'place_id',
        'capacity',
        'price_per_hour',
        'category',
        'amenities',
        'images',
        'contact_phone',
        'contact_email',
        'status',
        'rating',
        'total_reviews',
        'user_id',
    ];

    protected $casts = [
        'amenities' => 'array',
        'images' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'price_per_hour' => 'decimal:2',
        'rating' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function getFirstImageAttribute(): string
    {
        $images = $this->images ?? [];
        return count($images) > 0 ? $images[0] : 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=800';
    }

    public function getPriceFormattedAttribute(): string
    {
        return '৳' . number_format($this->price_per_hour, 0);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByCategory($query, $category)
    {
        if ($category && $category !== 'all') {
            return $query->where('category', $category);
        }
        return $query;
    }

    public function scopeByCity($query, $city)
    {
        if ($city) {
            return $query->where('city', 'like', '%' . $city . '%');
        }
        return $query;
    }

    public static function categories(): array
    {
        return [
            'conference' => 'Conference Hall',
            'wedding' => 'Wedding Venue',
            'concert' => 'Concert Hall',
            'birthday' => 'Birthday Party',
            'corporate' => 'Corporate Event',
            'outdoor' => 'Outdoor Space',
            'rooftop' => 'Rooftop Venue',
            'banquet' => 'Banquet Hall',
        ];
    }
}
