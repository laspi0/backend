<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\Listing;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function like(Request $request, $listingId)
    {
        // Récupère l'annonce correspondant à l'ID fourni
        $listing = Listing::findOrFail($listingId);
        
        // Récupère l'utilisateur authentifié
        $user = $request->user();

        // Vérifie si l'utilisateur a déjà aimé cette annonce
        $existingLike = $listing->likes()->where('user_id', $user->id)->first();

        if ($existingLike) {
            // Si un "like" existe déjà, on le supprime (unlike)
            $existingLike->delete();
            return response()->json(['message' => 'Like removed']);
        }

        // Si aucun "like" n'existe, on en crée un nouveau
        $like = new Like(['user_id' => $user->id]);
        $listing->likes()->save($like);

        // Retourne une réponse indiquant que l'annonce a été aimée
        return response()->json(['message' => 'Listing liked']);
    }
}