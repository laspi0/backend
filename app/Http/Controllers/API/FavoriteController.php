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
        $userId = Auth::id();

        // Check if the listing exists
        $listing = Listing::findOrFail($listingId);

        // Check if the listing is already favorited
        if (Favorite::where('user_id', $userId)->where('listing_id', $listingId)->exists()) {
            return response()->json(['message' => 'Listing already favorited'], 400);
        }

        // Create a new favorite
        Favorite::create([
            'user_id' => $userId,
            'listing_id' => $listingId,
        ]);

        return response()->json(['message' => 'Listing added to favorites'], 201);
    }

    /**
     * Remove an item from the user's favorites.
     */
    public function destroy($listingId)
    {
        $userId = Auth::id();

        // Find and delete the favorite
        $favorite = Favorite::where('user_id', $userId)->where('listing_id', $listingId)->firstOrFail();
        $favorite->delete();

        return response()->json(['message' => 'Listing removed from favorites'], 200);
    }

    /**
     * List all favorites of the authenticated user.
     */
    public function index()
    {
        $userId = Auth::id();
        $favorites = Favorite::where('user_id', $userId)
            ->with('listing.photos') // Inclut les photos des articles
            ->get();

        return response()->json($favorites);
    }
}
