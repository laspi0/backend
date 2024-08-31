<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Définition du modèle Conversation
class Conversation extends Model
{
    // Utilisation du trait HasFactory pour la création facile d'instances de test
    use HasFactory;

    // Liste des attributs qui peuvent être assignés en masse
    protected $fillable = ['listing_id', 'sender_id', 'recipient_id'];

    // Relation avec le modèle Listing (une conversation appartient à une annonce)
    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    // Relation avec le modèle User pour l'expéditeur
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Relation avec le modèle User pour le destinataire
    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    // Relation avec le modèle Message (une conversation a plusieurs messages)
    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}