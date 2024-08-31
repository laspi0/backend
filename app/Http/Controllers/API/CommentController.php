<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    // Méthode pour créer un nouveau commentaire
    public function store(Request $request, $listingId)
    {
        // Valide le contenu du commentaire
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:1000',
        ]);

        // Renvoie les erreurs de validation si la validation échoue
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Récupère l'annonce associée au commentaire
        $listing = Listing::findOrFail($listingId);
        $user = $request->user();

        // Crée un nouveau commentaire
        $comment = new Comment([
            'user_id' => $user->id,
            'content' => $request->content,
        ]);

        // Sauvegarde le commentaire et l'associe à l'annonce
        $listing->comments()->save($comment);

        // Renvoie une réponse JSON avec le commentaire créé
        return response()->json([
            'message' => 'Comment added successfully',
            'comment' => $comment
        ], 201);
    }

    // Méthode pour supprimer un commentaire
    public function destroy($id)
    {
        // Récupère le commentaire à supprimer
        $comment = Comment::findOrFail($id);
        $user = auth()->user();

        // Vérifie si l'utilisateur est autorisé à supprimer le commentaire
        if ($user->id !== $comment->user_id && $user->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Supprime le commentaire
        $comment->delete();

        // Renvoie une réponse JSON confirmant la suppression
        return response()->json(['message' => 'Comment deleted successfully']);
    }
}