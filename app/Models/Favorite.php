<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modèle Favorite représentant les favoris des utilisateurs pour les annonces.
 */
class Favorite extends Model
{
    use HasFactory;

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',   // ID de l'utilisateur qui a créé le favori
        'listing_id', // ID de l'annonce mise en favori
    ];

    /**
     * Obtient l'utilisateur propriétaire du favori.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtient l'annonce qui est mise en favori.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
}
