<?php

namespace App\Repositories;

use App\Models\Conversation;
use App\Repositories\Interfaces\ConversationRepositoryInterface;

class ConversationRepository extends BaseRepository implements ConversationRepositoryInterface
{
    /**
     * Inject the Conversation model
     *
     * @param Conversation $conversation
     */
    public function __construct(Conversation $conversation)
    {
        parent::__construct($conversation);
    }

    /**
     * Get or create conversation for a customer
     *
     * @param int $customerId
     * @return Conversation
     */
    public function getOrCreateForCustomer(int $customerId): Conversation
    {
        return $this->model->firstOrCreate(
            ['customer_id' => $customerId],
            ['last_message_at' => now()]
        );
    }

    /**
     * Get all conversations with latest message
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllWithLatestMessage()
    {
        return $this->query()
            ->with(['userCustomer', 'userAdmin', 'messages' => function ($query) {
                $query->latest()->limit(1);
            }])
            ->orderBy('last_message_at', 'desc')
            ->get();
    }

    /**
     * Get conversation with messages
     *
     * @param int $conversationId
     * @return Conversation|null
     */
    public function getWithMessages(int $conversationId): ?Conversation
    {
        return $this->query()
            ->with(['userCustomer', 'userAdmin', 'messages.user', 'messages.item'])
            ->find($conversationId);
    }
}
