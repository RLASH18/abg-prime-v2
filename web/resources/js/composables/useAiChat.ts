import aiRoutes from '@/routes/customer/ai';
import type { AiMessage } from '@/types';
import axios from 'axios';
import { ref } from 'vue';

const STORE_IMAGE_MARKER = '[SHOW_STORE_IMAGE]';

export function useAiChat() {
    const loading = ref(false);
    const error = ref<string | null>(null);

    // Send a message to the AI and return the reply text and optional image flag
    const sendMessage = async (
        message: string,
        history: Pick<AiMessage, 'role' | 'content'>[],
    ): Promise<{ reply: string; showStoreImage: boolean }> => {
        loading.value = true;
        error.value = null;

        try {
            const response = await axios.post(aiRoutes.ask().url, {
                message,
                history,
            });

            const raw: string = response.data.reply;
            const showStoreImage = raw.includes(STORE_IMAGE_MARKER);
            const reply = raw.replace(STORE_IMAGE_MARKER, '').trim();

            loading.value = false;
            return { reply, showStoreImage };
        } catch (err) {
            error.value = 'Failed to get AI response';
            loading.value = false;
            throw err;
        }
    };

    return {
        loading,
        error,
        sendMessage,
    };
}
