import itemsRoutes from '@/routes/admin/items';
import axios from 'axios';
import { ref } from 'vue';

export function useItemForecast() {
    const loading = ref(false);
    const error = ref<string | null>(null);

    // Request an API forecast report for a specific item by ID
    const generateForecast = async (itemID: number): Promise<string> => {
        loading.value = true;
        error.value = null;

        try {
            const response = await axios.post(itemsRoutes.forecast(itemID).url);

            loading.value = false;
            return response.data.report as string;
        } catch (err) {
            error.value = 'Failed to generate forecast.';
            loading.value = false;
            throw err;
        }
    };

    return {
        loading,
        error,
        generateForecast,
    };
}
