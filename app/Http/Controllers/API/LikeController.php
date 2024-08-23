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
        $listing = Listing::findOrFail($listingId);
        $user = $request->user();

        $existingLike = $listing->likes()->where('user_id', $user->id)->first();

        if ($existingLike) {
            $existingLike->delete();
            return response()->json(['message' => 'Like removed']);
        }

        $like = new Like(['user_id' => $user->id]);
        $listing->likes()->save($like);

        return response()->json(['message' => 'Listing liked']);
    }
}