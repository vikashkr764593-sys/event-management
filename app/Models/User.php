<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Class User
 * @package App\Models
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property string|null $profile_picture
 * @property string|null $email_verified_at
 * @property string $password
 * @property string $role
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'profile_picture',
        'password',
        'role',
        'status',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at'     => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /**
     * Get the URL for the user's profile picture.
     * Falls back to a generated avatar using UI Avatars service.
     */
    public function getProfilePictureUrlAttribute(): string
    {
        if ($this->profile_picture) {
            // Support both full URLs and local storage paths
            if (str_starts_with($this->profile_picture, 'http')) {
                return $this->profile_picture;
            }

            return asset('storage/' . $this->profile_picture);
        }

        // Generate a deterministic avatar from the user's initials
        $name = urlencode($this->name);
        return "https://ui-avatars.com/api/?name={$name}&background=4f46e5&color=ffffff&size=128&font-size=0.4&bold=true";
    }

    /**
     * A user can have many bookings.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * A user can have one cart.
     */
    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    /**
     * A user can have many orders.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}