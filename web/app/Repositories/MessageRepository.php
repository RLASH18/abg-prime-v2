<?php

namespace App\Repositories;

use App\Enums\UserRole;
use App\Models\Message;
use App\Repositories\Interfaces\MessageRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class MessageRepository extends BaseRepository implements MessageRepositoryInterface
{
    /**
     * Inject the Message model
     *
     * @param Message $message
     */
    public function __construct(Message $message)
    {
        parent::__construct($message);
    }

    /**
     * Get all messages for a conversation
     *
     * @param int $conversationId
     * @return Collection
     */
    public function getByConversation(int $conversationId): Collection
    {
        return $this->query()
            ->where('conversation_id', $conversationId)
            ->with(['user', 'item'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Mark messages as read
     *
     * @param int $conversationId
     * @param int $userId
     * @return int
     */
    public function markAsRead(int $conversationId, int $userId): int
    {
        return $this->query()
            ->where('conversation_id', $conversationId)
            ->where('sender_id', '!=', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * Get unread count for user
     *
     * @param int $userId
     * @param string $userType
     * @return int
     */
    public function getUnreadCount(int $userId, string $userType): int
    {
        $oppositeType = $userType === UserRole::Admin->value
            ? UserRole::Customer->value
            : UserRole::Admin->value;

        return $this->query()
            ->whereHas('conversation', function ($q) use ($userId, $userType) {
                if ($userType === UserRole::Admin->value) {
                    // For admin, count unread from all customers
                    $q->whereNotNull('customer_id');
                } else {
                    // For customer, only their conversation
                    $q->where('customer_id', $userId);
                }
            })
            ->where('sender_type', $oppositeType)
            ->whereNull('read_at')
            ->count();
    }
}
