<script setup lang="ts">
import Select from '@/components/Select.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import type { FilterConfig } from '@/types';
import { Search, X } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

interface Props {
    searchPlaceholder?: string;
    searchValue?: string;
    filters?: FilterConfig[];
    showReset?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    searchPlaceholder: 'Search...',
    searchValue: '',
    filters: () => [],
    showReset: true,
});

const emit = defineEmits<{
    'update:search': [value: string];
    'update:filter': [key: string, value: string];
    reset: [];
}>();

// Local search state for immediate UI updates
const localSearch = ref(props.searchValue);

// Local filter values to track selections
const localFilterValues = ref<Record<string, string>>({});

// Initialize local filter values from props
props.filters.forEach((filter) => {
    if (filter.value) {
        localFilterValues.value[filter.key] = filter.value;
    }
});

// Watch for external changes (like reset)
watch(
    () => props.searchValue,
    (newValue) => {
        localSearch.value = newValue;
    },
);

// Watch for filter value changes from props
watch(
    () => props.filters,
    (newFilters) => {
        newFilters.forEach((filter) => {
            if (filter.value) {
                localFilterValues.value[filter.key] = filter.value;
            } else {
                delete localFilterValues.value[filter.key];
            }
        });
    },
    { deep: true },
);

// Check if any filters are active
const hasActiveFilters = computed(() => {
    // Check if search has value
    if (localSearch.value && localSearch.value.trim() !== '') {
        return true;
    }

    // Check if any filter dropdown has a value
    return Object.keys(localFilterValues.value).length > 0;
});

const handleSearchInput = (event: Event) => {
    const target = event.target as HTMLInputElement;
    localSearch.value = target.value;
    emit('update:search', target.value);
};

const handleFilterChange = (key: string, value: string) => {
    if (value) {
        localFilterValues.value[key] = value;
    } else {
        delete localFilterValues.value[key];
    }
    emit('update:filter', key, value);
};

const handleReset = () => {
    localSearch.value = '';
    localFilterValues.value = {};
    emit('reset');
};
</script>

<template>
    <div class="mb-4 rounded-lg border border-border bg-card p-4 shadow-sm">
        <div class="flex flex-col gap-4">
            <div class="flex flex-col gap-4 md:flex-row">
                <!-- Search Input -->
                <div class="flex-1">
                    <div class="relative">
                        <Search
                            :size="18"
                            class="absolute top-1/2 left-3 -translate-y-1/2 text-muted-foreground"
                        />
                        <Input
                            v-model="localSearch"
                            @input="handleSearchInput"
                            :placeholder="searchPlaceholder"
                            class="pl-10"
                        />
                    </div>
                </div>

                <!-- Filter Dropdowns -->
                <template v-for="filter in filters" :key="filter.key">
                    <div class="w-full md:w-48">
                        <Select
                            v-model="localFilterValues[filter.key]"
                            :options="filter.options"
                            :placeholder="
                                filter.placeholder ||
                                `Filter by ${filter.label}`
                            "
                            @update:modelValue="
                                (value: string) =>
                                    handleFilterChange(filter.key, value)
                            "
                        />
                    </div>
                </template>

                <!-- Reset Button (only show if filters are active) -->
                <Button
                    v-if="showReset && hasActiveFilters"
                    variant="outline"
                    @click="handleReset"
                    class="w-full md:w-auto"
                >
                    <X :size="16" class="mr-2" />
                    Reset
                </Button>
            </div>
        </div>
    </div>
</template>
