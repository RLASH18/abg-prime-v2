<?php

namespace App\Repositories\Interfaces;

use App\Models\Conversation;

interface ConversationRepositoryInterface extends BaseRepositoryInterface
{
    public function getOrCreateForCustomer(int $customerId): Conversation;
    public function getAllWithLatestMessage();
    public function getWithMessages(int $conversationId): ?Conversation;
}
