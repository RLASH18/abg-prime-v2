<?php

use App\Enums\UserRole;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::routes(['middleware' => ['web', 'auth:web']]);

Broadcast::channel('conversation.{conversationId}', function (User $user, int $conversationId) {
    // Admins can access all conversations
    if ($user->role === UserRole::Admin) {
        return true;
    }

    // Customers can only access their own conversation
    return Conversation::where('id', $conversationId)
        ->where('customer_id', $user->id)
        ->exists();
});
