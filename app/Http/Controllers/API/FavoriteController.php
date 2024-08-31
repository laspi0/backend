<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Favorite;
use App\Models\Listing;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Add an item to the user's favorites.
     */
    public function store(Request $request, $listingId)
    {
        // Récupère l'ID de l'utilisateur authentifié
        $userId = Auth::id();

        // Vérifie si l'annonce existe, sinon lance une exception
        $listing = Listing::findOrFail($listingId);

        // Vérifie si l'annonce est déjà dans les favoris de l'utilisateur
        if (Favorite::where('user_id', $userId)->where('listing_id', $listingId)->exists()) {
            return response()->json(['message' => 'Listing already favorited'], 400);
        }

        // Crée un nouveau favori
        Favorite::create([
            'user_id' => $userId,
            'listing_id' => $listingId,
        ]);

        // Retourne une réponse de succès
        return response()->json(['message' => 'Listing added to favorites'], 201);
    }

    /**
     * Remove an item from the user's favorites.
     */
    public function destroy($listingId)
    {
        // Récupère l'ID de l'utilisateur authentifié
        $userId = Auth::id();

        // Trouve et supprime le favori, lance une exception si non trouvé
        $favorite = Favorite::where('user_id', $userId)->where('listing_id', $listingId)->firstOrFail();
        $favorite->delete();

        // Retourne une réponse de succès
        return response()->json(['message' => 'Listing removed from favorites'], 200);
    }

    /**
     * List all favorites of the authenticated user.
     */
    public function index()
    {
        // Récupère l'ID de l'utilisateur authentifié
        $userId = Auth::id();

        // Récupère tous les favoris de l'utilisateur avec les annonces et leurs photos
        $favorites = Favorite::where('user_id', $userId)
            ->with('listing.photos') // Inclut les photos des articles
            ->get();

        // Retourne les favoris en JSON
        return response()->json($favorites);
    }
}
