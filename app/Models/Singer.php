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
        'genre',
        'hourly_rate',
        'bio',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'hourly_rate' => 'float',
    ];

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