<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Booking extends Model {
    protected $guarded = [];
    public function user() { return $this->belongsTo(User::class); }
    public function singer() { return $this->belongsTo(Singer::class); }
}