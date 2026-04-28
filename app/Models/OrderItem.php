<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class OrderItem extends Model {
    protected $guarded = [];
    public function instrument() { return $this->belongsTo(Instrument::class); }
}