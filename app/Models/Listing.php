<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Modèle Listing représentant une annonce dans l'application
class Listing extends Model
{
    use HasFactory;

    // Liste des attributs qui peuvent être assignés en masse
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

    // Relation avec l'utilisateur qui a créé l'annonce
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation avec les photos associées à l'annonce
    public function photos()
    {
        return $this->hasMany(ListingPhoto::class);
    }

    // Relation avec les favoris de l'annonce
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    // Relation avec les likes de l'annonce
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    // Relation avec les commentaires de l'annonce
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
