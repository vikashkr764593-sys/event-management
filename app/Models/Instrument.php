<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Instrument
 * @package App\Models
 *
 * @property int $id
 * @property string $name
 * @property string|null $category
 * @property float $price
 * @property int $stock
 * @property string|null $image
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Instrument extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'category_id',
        'price',
        'stock',
        'reserved_stock',
        'image',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'float',
        'stock' => 'integer',
    ];

    /**
     * Get the URL for the instrument's image.
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            if (str_starts_with($this->image, 'http')) {
                return $this->image;
            }
            return asset('storage/' . $this->image);
        }

        // Return a default placeholder if no image exists
        $name = urlencode($this->name ?: 'Instrument');
        return "https://ui-avatars.com/api/?name={$name}&background=3B82F6&color=ffffff&size=128&font-size=0.33&bold=true";
    }

    /**
     * Get the category that owns the instrument.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
