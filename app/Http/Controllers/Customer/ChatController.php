<?php

namespace App\Http\Controllers\Customer;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Chat\StoreChatRequest;
use App\Services\ChatService;
use Illuminate\Support\Facades\Auth;

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
     * Get customer's conversation
     */
    public function index()
    {
        $conversation = $this->chatService->getOrCreateConversation(Auth::id());
        $messages = $this->chatService->getMessages($conversation->id);

        // Mark messages as read
        $this->chatService->markMessagesAsRead($conversation->id, Auth::id());

        return response()->json([
            'conversation' => $conversation,
            'messages' => $messages,
        ]);
    }

    /**
     * Send message to admin
     */
    public function store(StoreChatRequest $request)
    {
        $conversation = $this->chatService->getOrCreateConversation(Auth::id());

        $message = $this->chatService->sendMessage(
            $conversation->id,
            Auth::id(),
            UserRole::Customer->value,
            $request->validated()['message'],
            $request->validated()['item_id'] ?? null
        );

        return response()->json([
            'message' => $message->load(['user', 'item']),
        ], 201);
    }

    /**
     * Mark as messages as read
     */
    public function markAsRead()
    {
        $conversation = $this->chatService->getOrCreateConversation(Auth::id());
        $count = $this->chatService->markMessagesAsRead($conversation->id, Auth::id());

        return response()->json([
            'marked_count' => $count
        ]);
    }

    /**
     * Get unread count
     */
    public function unreadCount()
    {
        $count = $this->chatService->getUnreadCount(Auth::id(), UserRole::Customer->value);

        return response()->json([
            'unread_count' => $count
        ]);
    }
}
