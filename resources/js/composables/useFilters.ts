import { router } from '@inertiajs/vue3';
import { debounce } from 'lodash-es';
import { ref } from 'vue';

export interface FilterConfig {
    [key: string]: string | number | null;
}

export function useFilters(baseUrl: string, initialFilters: FilterConfig = {}) {
    const filters = ref<FilterConfig>({ ...initialFilters });

    // Apply filters to the URL
    const applyFilters = (): void => {
        const params: Record<string, string> = {};

        Object.entries(filters.value).forEach(([key, value]) => {
            if (value !== null && value !== '' && value !== undefined) {
                params[key] = String(value);
            }
        });

        router.get(baseUrl, params, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    // Debounced version of applyFilters for search inputs
    const debouncedApplyFilters = debounce(applyFilters, 150);

    // Reset all filters to initial state
    const resetFilters = (): void => {
        filters.value = { ...initialFilters };
        router.get(
            baseUrl,
            {},
            {
                preserveState: true,
                preserveScroll: true,
            },
        );
    };

    // Update a specific filter value
    const updateFilter = (
        key: string,
        value: string | number | null,
        immediate: boolean = false,
    ): void => {
        filters.value[key] = value;

        if (immediate) {
            applyFilters();
        } else {
            debouncedApplyFilters();
        }
    };

    return {
        filters,
        applyFilters,
        resetFilters,
        updateFilter,
    };
}
