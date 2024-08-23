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
    public function startConversation(Request $request, $listingId)
    {
        $listing = Listing::findOrFail($listingId);
        $sender = $request->user();

        if ($sender->id === $listing->user_id) {
            return response()->json(['message' => 'You cannot start a conversation with yourself'], 400);
        }

        $existingConversation = Conversation::where('listing_id', $listing->id)
            ->where('sender_id', $sender->id)
            ->where('recipient_id', $listing->user_id)
            ->first();

        if ($existingConversation) {
            return response()->json(['message' => 'Conversation already exists', 'conversation' => $existingConversation], 200);
        }

        $conversation = Conversation::create([
            'listing_id' => $listing->id,
            'sender_id' => $sender->id,
            'recipient_id' => $listing->user_id,
        ]);

        return response()->json(['message' => 'Conversation started', 'conversation' => $conversation], 201);
    }

    public function sendMessage(Request $request, $conversationId)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $conversation = Conversation::findOrFail($conversationId);
        $user = $request->user();

        if ($user->id !== $conversation->sender_id && $user->id !== $conversation->recipient_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => $user->id,
            'content' => $request->content,
            'is_read' => false,
        ]);

        return response()->json(['message' => 'Message sent', 'data' => $message], 201);
    }

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

    public function getMessages($conversationId)
    {
        $conversation = Conversation::findOrFail($conversationId);
        $user = auth()->user();

        if ($user->id !== $conversation->sender_id && $user->id !== $conversation->recipient_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $messages = $conversation->messages()->with('user:id,name')->get();

        return response()->json($messages);
    }

    public function markAsRead(Request $request, $conversationId)
    {
        $user = $request->user();
        $conversation = Conversation::findOrFail($conversationId);
    
        if ($user->id !== $conversation->sender_id && $user->id !== $conversation->recipient_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
    
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