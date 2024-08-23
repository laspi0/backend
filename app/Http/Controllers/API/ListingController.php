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
    public function index()
    {
        $listings = Listing::with('photos')->get();
        return response()->json($listings);
    }

    public function show($id)
    {
        $listing = Listing::with('photos')->findOrFail($id);
        return response()->json($listing);
    }

    public function store(Request $request)
    {
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

    public function update(Request $request, $id)
    {
        $listing = Listing::findOrFail($id);

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

        $listing->update($request->all());

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

    public function destroy($id)
    {
        $listing = Listing::findOrFail($id);

        foreach ($listing->photos as $photo) {
            Storage::disk('public')->delete($photo->path);
            $photo->delete();
        }

        $listing->delete();

        return response()->json(['message' => 'Listing deleted successfully']);
    }

    public function checkConversation(Request $request, $id)
    {
        $listing = Listing::findOrFail($id);
        $user = $request->user();

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

    public function deletePhoto($id)
    {
        $photo = ListingPhoto::findOrFail($id);
        Storage::disk('public')->delete($photo->path);
        $photo->delete();

        return response()->json(['message' => 'Photo deleted successfully']);
    }
}
