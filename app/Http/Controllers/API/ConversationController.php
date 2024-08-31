<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Listing;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConversationController extends Controller
{
    // Démarre une nouvelle conversation pour une annonce
    public function startConversation(Request $request, $listingId)
    {
        $listing = Listing::findOrFail($listingId);
        $sender = $request->user();

        // Vérifie que l'utilisateur ne démarre pas une conversation avec lui-même
        if ($sender->id === $listing->user_id) {
            return response()->json(['message' => 'You cannot start a conversation with yourself'], 400);
        }

        // Vérifie si une conversation existe déjà
        $existingConversation = Conversation::where('listing_id', $listing->id)
            ->where('sender_id', $sender->id)
            ->where('recipient_id', $listing->user_id)
            ->first();

        if ($existingConversation) {
            return response()->json(['message' => 'Conversation already exists', 'conversation' => $existingConversation], 200);
        }

        // Crée une nouvelle conversation
        $conversation = Conversation::create([
            'listing_id' => $listing->id,
            'sender_id' => $sender->id,
            'recipient_id' => $listing->user_id,
        ]);

        return response()->json(['message' => 'Conversation started', 'conversation' => $conversation], 201);
    }

    // Envoie un message dans une conversation existante
    public function sendMessage(Request $request, $conversationId)
    {
        // Valide le contenu du message
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $conversation = Conversation::findOrFail($conversationId);
        $user = $request->user();

        // Vérifie que l'utilisateur est autorisé à envoyer un message dans cette conversation
        if ($user->id !== $conversation->sender_id && $user->id !== $conversation->recipient_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Crée et enregistre le nouveau message
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => $user->id,
            'content' => $request->content,
            'is_read' => false,
        ]);

        return response()->json(['message' => 'Message sent', 'data' => $message], 201);
    }

    // Récupère toutes les conversations de l'utilisateur
    public function getConversations(Request $request)
    {
        $user = $request->user();
        $conversations = Conversation::where('sender_id', $user->id)
            ->orWhere('recipient_id', $user->id)
            ->with(['listing:id,title', 'sender:id,name', 'recipient:id,name'])
            ->withCount(['messages as unread_messages' => function ($query) use ($user) {
                $query->where('user_id', '!=', $user->id)->where('is_read', false);
            }])
            ->get();

        return response()->json($conversations);
    }

    // Récupère tous les messages d'une conversation
    public function getMessages($conversationId)
    {
        $conversation = Conversation::findOrFail($conversationId);
        $user = auth()->user();

        // Vérifie que l'utilisateur est autorisé à voir les messages de cette conversation
        if ($user->id !== $conversation->sender_id && $user->id !== $conversation->recipient_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $messages = $conversation->messages()->with('user:id,name')->get();

        return response()->json($messages);
    }

    // Marque tous les messages non lus d'une conversation comme lus
    public function markAsRead(Request $request, $conversationId)
    {
        $user = $request->user();
        $conversation = Conversation::findOrFail($conversationId);
    
        // Vérifie que l'utilisateur est autorisé à marquer les messages comme lus
        if ($user->id !== $conversation->sender_id && $user->id !== $conversation->recipient_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
    
        // Met à jour les messages non lus
        $updatedCount = $conversation->messages()
            ->where('user_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    
        if ($updatedCount > 0) {
            return response()->json(['message' => "{$updatedCount} messages marked as read"]);
        } else {
            return response()->json(['message' => 'No unread messages to mark as read']);
        }
    }
}