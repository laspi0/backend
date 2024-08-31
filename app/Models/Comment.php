<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Modèle Comment représentant un commentaire dans l'application
class Comment extends Model
{
    use HasFactory;

    // Liste des attributs qui peuvent être assignés en masse
    protected $fillable = ['user_id', 'listing_id', 'content'];

    // Relation avec l'utilisateur qui a créé le commentaire
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation avec l'annonce à laquelle le commentaire est associé
    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
}