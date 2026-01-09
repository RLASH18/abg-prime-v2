<?php

namespace App\Services;

use App\Events\MessageSent;
use App\Repositories\Interfaces\ConversationRepositoryInterface;
use App\Repositories\Interfaces\MessageRepositoryInterface;

class ChatService
{
    /**
     * Inject repositories
     *
     * @param ConversationRepositoryInterface $conversationRepo
     * @param MessageRepositoryInterface $messageRepo
     */
    public function __construct(
        protected ConversationRepositoryInterface $conversationRepo,
        protected MessageRepositoryInterface $messageRepo
    ) {}

    /**
     * Get or create conversation for customer
     *
     * @param int $customerId
     * @return \App\Models\Conversation
     */
    public function getOrCreateConversation(int $customerId)
    {
        return $this->conversationRepo->getOrCreateForCustomer($customerId);
    }

    /**
     * Get all conversations (for admin)
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllConversations()
    {
        return $this->conversationRepo->getAllWithLatestMessage();
    }

    /**
     * Get conversation with messages
     *
     * @param int $conversationId
     * @return \App\Models\Conversation|null
     */
    public function getConversation(int $conversationId)
    {
        return $this->conversationRepo->getWithMessages($conversationId);
    }

    /**
     * Get all messages for conversation
     *
     * @param int $conversationId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getMessages(int $conversationId)
    {
        return $this->messageRepo->getByConversation($conversationId);
    }

    /**
     * Send a message
     *
     * @param int $conversationId
     * @param int $senderId
     * @param string $senderType
     * @param string $message
     * @param int|null $itemId
     * @return \App\Models\Message
     */
    public function sendMessage(
        int $conversationId,
        int $senderId,
        string $senderType,
        string $message,
        ?int $itemId = null
    ) {
        // Create message
        $messageData = [
            'conversation_id' => $conversationId,
            'sender_id' => $senderId,
            'sender_type' => $senderType,
            'message' => $message,
            'item_id' => $itemId,
        ];

        $newMessage = $this->messageRepo->create($messageData);

        // Update conversation last_message_at
        $this->conversationRepo->update($conversationId, [
            'last_message_at' => now(),
        ]);

        // Load relationships for broadcasting
        $newMessage->load(['user', 'item']);

        // Broadcast the message
        broadcast(new MessageSent($newMessage))->toOthers();

        return $newMessage;
    }

    /**
     * Mark messages as read
     *
     * @param int $conversationId
     * @param int $userId
     * @return int
     */
    public function markMessagesAsRead(int $conversationId, int $userId): int
    {
        return $this->messageRepo->markAsRead($conversationId, $userId);
    }

    /**
     * Get unread message count
     *
     * @param int $userId
     * @param string $userType
     * @return int
     */
    public function getUnreadCount(int $userId, string $userType): int
    {
        return $this->messageRepo->getUnreadCount($userId, $userType);
    }
}
