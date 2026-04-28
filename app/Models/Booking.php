<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Booking
 * @package App\Models
 *
 * @property int $id
 * @property int $user_id
 * @property int $singer_id
 * @property string $event_date
 * @property string $time_slot
 * @property string $event_type
 * @property string|null $notes
 * @property string $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Booking extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'singer_id',
        'event_date',
        'time_slot',
        'event_type',
        'notes',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'event_date' => 'date',
    ];

    /**
     * Get the user that made the booking.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the singer associated with the booking.
     */
    public function singer()
    {
        return $this->belongsTo(Singer::class);
    }

    /**
     * Get the chat messages for this booking.
     */
    public function chats()
    {
        return $this->hasMany(Chat::class);
    }
}