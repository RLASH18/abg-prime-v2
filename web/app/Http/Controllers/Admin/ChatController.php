<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Chat\StoreChatRequest;
use App\Services\ChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ChatController extends Controller
{
    /**
     * Inject ChatService
     *
     * @param ChatService $chatService
     */
    public function __construct(
        protected ChatService $chatService
    ) {}

    /**
     * Get all conversations
     */
    public function index()
    {
        $conversations = $this->chatService->getAllConversations(Auth::id());

        return Inertia::render('admin/Chats', [
            'conversations' => $conversations,
        ]);
    }

    /**
     * Get conversation messages
     */
    public function show(int $conversationId)
    {
        $conversation = $this->chatService->getConversation($conversationId);

        if (! $conversation) {
            return response()->json(['error' => 'Conversation not found'], 404);
        }

        $messages = $this->chatService->getMessages($conversationId);

        // Mark messages as read
        $this->chatService->markMessagesAsRead($conversationId, Auth::id());

        return response()->json([
            'conversation' => $conversation,
            'messages' => $messages,
        ]);
    }

    /**
     * Send message to customer
     */
    public function store(StoreChatRequest $request, int $conversationId)
    {
        $message = $this->chatService->sendMessage(
            $conversationId,
            Auth::id(),
            UserRole::Admin->value,
            $request->validated()['message']
        );

        return response()->json([
            'message' => $message->load(['user', 'item']),
        ], 201);
    }

    /**
     * Mark messages as read
     */
    public function markAsRead(int $conversationId)
    {
        $count = $this->chatService->markMessagesAsRead($conversationId, Auth::id());

        return response()->json([
            'marked_count' => $count
        ]);
    }

    /**
     * Get unread count
     */
    public function unreadCount()
    {
        $count = $this->chatService->getUnreadCount(Auth::id(), UserRole::Admin->value);

        return response()->json([
            'unread_count' => $count,
        ]);
    }
}
