<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Singer
 * @package App\Models
 *
 * @property int $id
 * @property int $user_id
 * @property string $genre
 * @property float $hourly_rate
 * @property string|null $bio
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Singer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'stage_name',
        'genre',
        'experience_years',
        'availability_status',
        'rating',
        'biography',
        'profile_image',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'experience_years' => 'integer',
        'rating'           => 'float',
    ];

    /**
     * Get the URL for the singer's profile image.
     * Falls back to a generated avatar using UI Avatars service.
     */
    public function getProfileImageUrlAttribute(): string
    {
        if ($this->profile_image) {
            if (str_starts_with($this->profile_image, 'http')) {
                return $this->profile_image;
            }
            return asset('storage/' . $this->profile_image);
        }

        $name = urlencode($this->stage_name ?: $this->name ?: 'Singer');
        return "https://ui-avatars.com/api/?name={$name}&background=10B981&color=ffffff&size=128&font-size=0.4&bold=true";
    }

    /**
     * Get the user profile associated with the singer.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the availabilities for the singer.
     */
    public function availabilities()
    {
        return $this->hasMany(SingerAvailability::class);
    }

    /**
     * Get the bookings for the singer.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}