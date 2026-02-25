import chatsRoutes from '@/routes/admin/chats';
import type { Message } from '@/types';
import axios from 'axios';
import { ref } from 'vue';

export function useChatAdmin() {
    const loading = ref(false);
    const error = ref<string | null>(null);

    // Fetch messages for a specific conversation
    const fetchMessages = async (conversationId: number): Promise<any> => {
        loading.value = true;
        error.value = null;

        try {
            const response = await axios.get(
                chatsRoutes.show(conversationId).url,
            );
            loading.value = false;
            return response.data;
        } catch (err) {
            error.value = 'Failed to fetch messages';
            loading.value = false;
            throw err;
        }
    };

    // Send a message
    const sendMessage = async (
        conversationId: number,
        message: string,
        itemId?: number,
    ): Promise<Message> => {
        loading.value = true;
        error.value = null;

        try {
            const response = await axios.post(
                chatsRoutes.store(conversationId).url,
                {
                    message,
                    item_id: itemId,
                },
            );
            loading.value = false;
            return response.data.message;
        } catch (err) {
            error.value = 'Failed to send message';
            loading.value = false;
            throw err;
        }
    };

    // Mark messages as read
    const markAsRead = async (conversationId: number): Promise<void> => {
        try {
            await axios.post(chatsRoutes.read(conversationId).url);
        } catch (err) {
            console.error('Failed to mark as read:', err);
        }
    };

    // Get unread message count
    const getUnreadCount = async (): Promise<number> => {
        try {
            const response = await axios.get(chatsRoutes.unread.count().url);
            return response.data.unread_count;
        } catch (err) {
            console.error('Failed to get unread count:', err);
            return 0;
        }
    };

    return {
        loading,
        error,
        fetchMessages,
        sendMessage,
        markAsRead,
        getUnreadCount,
    };
}
