<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;

interface MessageRepositoryInterface extends BaseRepositoryInterface
{
    public function getByConversation(int $conversationId): Collection;
    public function markAsRead(int $conversationId, int $userId): int;
    public function getUnreadCount(int $userId, string $userType): int;
}
