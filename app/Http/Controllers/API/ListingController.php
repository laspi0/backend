<?php

namespace App\Http\Controllers\API;

use App\Models\Listing;
use App\Models\Conversation;
use App\Models\ListingPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class ListingController extends Controller
{
    // Récupère toutes les annonces avec leurs photos
    public function index()
    {
        $listings = Listing::with('photos')->get();
        return response()->json($listings);
    }

    // Récupère une annonce spécifique avec ses photos
    public function show($id)
    {
        $listing = Listing::with('photos')->findOrFail($id);
        return response()->json($listing);
    }

    // Crée une nouvelle annonce
    public function store(Request $request)
    {
        // Valide les données de la requête
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'measurement' => 'required|string',
            'type' => 'required|in:room,studio,apartment,villa',
            'address' => 'required|string',
            'photos' => 'required|array|min:1|max:10',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Crée l'annonce dans la base de données
        $listing = Listing::create([
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'measurement' => $request->measurement,
            'type' => $request->type,
            'address' => $request->address,
            'user_id' => auth()->id(),
            'status' => 'available'
        ]);

        // Gère l'upload des photos
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('listings', 'public');
                ListingPhoto::create([
                    'listing_id' => $listing->id,
                    'path' => $path
                ]);
            }
        }

        return response()->json([
            'message' => 'Listing created successfully',
            'listing' => $listing->load('photos')
        ], 201);
    }

    // Met à jour une annonce existante
    public function update(Request $request, $id)
    {
        $listing = Listing::findOrFail($id);

        // Valide les données de la requête
        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'description' => 'string',
            'price' => 'numeric',
            'measurement' => 'string',
            'type' => 'in:room,studio,apartment,villa',
            'address' => 'string',
            'status' => 'in:available,sold,rented',
            'photos' => 'array|max:10',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Met à jour l'annonce
        $listing->update($request->all());

        // Gère l'ajout de nouvelles photos
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('listings', 'public');
                ListingPhoto::create([
                    'listing_id' => $listing->id,
                    'path' => $path
                ]);
            }
        }

        return response()->json([
            'message' => 'Listing updated successfully',
            'listing' => $listing->load('photos')
        ]);
    }

    // Supprime une annonce et ses photos associées
    public function destroy($id)
    {
        $listing = Listing::findOrFail($id);

        // Supprime les photos associées à l'annonce
        foreach ($listing->photos as $photo) {
            Storage::disk('public')->delete($photo->path);
            $photo->delete();
        }

        // Supprime l'annonce
        $listing->delete();

        return response()->json(['message' => 'Listing deleted successfully']);
    }

    // Vérifie si une conversation existe pour une annonce donnée
    public function checkConversation(Request $request, $id)
    {
        $listing = Listing::findOrFail($id);
        $user = $request->user();

        // Recherche une conversation existante
        $conversation = Conversation::where('listing_id', $listing->id)
            ->where(function ($query) use ($user) {
                $query->where('sender_id', $user->id)
                    ->orWhere('recipient_id', $user->id);
            })
            ->first();

        return response()->json([
            'has_conversation' => $conversation !== null,
            'conversation_id' => $conversation ? $conversation->id : null,
        ]);
    }

    // Supprime une photo spécifique d'une annonce
    public function deletePhoto($id)
    {
        $photo = ListingPhoto::findOrFail($id);
        Storage::disk('public')->delete($photo->path);
        $photo->delete();

        return response()->json(['message' => 'Photo deleted successfully']);
    }
}
