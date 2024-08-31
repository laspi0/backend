<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modèle Like représentant un "j'aime" sur une annonce
 */
class Like extends Model
{
    use HasFactory; // Utilise le trait HasFactory pour la création facile d'instances de test

    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'listing_id'];

    /**
     * Obtient l'utilisateur associé à ce "j'aime".
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtient l'annonce associée à ce "j'aime".
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
}