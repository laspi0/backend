<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'price',
        'measurement',
        'type',
        'address',
        'user_id',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function photos()
    {
        return $this->hasMany(ListingPhoto::class);
    }
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }
}
