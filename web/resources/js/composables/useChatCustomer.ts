import chatsRoutes from '@/routes/customer/chats';
import type { Message } from '@/types';
import axios from 'axios';
import { ref } from 'vue';

export function useChatCustomer() {
    const loading = ref(false);
    const error = ref<string | null>(null);

    // Fetch customer's conversation and messages
    const fetchConversation = async (): Promise<any> => {
        loading.value = true;
        error.value = null;

        try {
            const response = await axios.get(chatsRoutes.index().url);
            loading.value = false;
            return response.data;
        } catch (err) {
            error.value = 'Failed to fetch conversation';
            loading.value = false;
            throw err;
        }
    };

    // Send a message (with optional item attachment)
    const sendMessage = async (
        message: string,
        itemId?: number,
    ): Promise<Message> => {
        loading.value = true;
        error.value = null;

        try {
            const response = await axios.post(chatsRoutes.store().url, {
                message,
                item_id: itemId,
            });
            loading.value = false;
            return response.data.message;
        } catch (err) {
            error.value = 'Failed to send message';
            loading.value = false;
            throw err;
        }
    };

    // Mark messages as read
    const markAsRead = async (): Promise<void> => {
        try {
            await axios.post(chatsRoutes.read().url);
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
        fetchConversation,
        sendMessage,
        markAsRead,
        getUnreadCount,
    };
}
