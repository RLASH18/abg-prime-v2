<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { PaginationData, PaginationLink } from '@/types';
import { Link } from '@inertiajs/vue3';
import {
    ChevronLeft,
    ChevronRight,
    ChevronsLeft,
    ChevronsRight,
} from 'lucide-vue-next';

interface Props {
    pagination: PaginationData;
    showInfo?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    showInfo: true,
});

const getPageNumber = (label: string): number | null => {
    const num = parseInt(label);
    return isNaN(num) ? null : num;
};

// Remove query parameters from URL
const removeQueryParams = (url: string | null): string | null => {
    if (!url) return null;
    return url.split('?')[0];
};

// Get first page URL (without query params)
const getFirstPageUrl = (): string | null => {
    const firstPageLink = props.pagination.links.find(
        (link) => getPageNumber(link.label) === 1,
    );
    return firstPageLink?.url ? removeQueryParams(firstPageLink.url) : null;
};

// Get last page URL
const getLastPageUrl = (): string | null => {
    const lastPageLink = props.pagination.links.find(
        (link) => getPageNumber(link.label) === props.pagination.last_page,
    );
    return lastPageLink?.url || null;
};

// Get previous page URL (first link in array)
const getPreviousPageUrl = (): string | null => {
    return props.pagination.links[0]?.url || null;
};

// Get next page URL (last link in array)
const getNextPageUrl = (): string | null => {
    return (
        props.pagination.links[props.pagination.links.length - 1]?.url || null
    );
};

// Get page URL, remove query params if it's page 1
const getPageUrl = (link: PaginationLink): string | null => {
    if (!link.url) return null;
    const pageNum = getPageNumber(link.label);
    return pageNum === 1 ? removeQueryParams(link.url) : link.url;
};
</script>

<template>
    <div class="flex items-center justify-between px-2 py-4">
        <!-- Pagination Info -->
        <div v-if="showInfo" class="text-sm text-muted-foreground">
            Showing <span class="font-medium">{{ pagination.from }}</span> to
            <span class="font-medium">{{ pagination.to }}</span> of
            <span class="font-medium">{{ pagination.total }}</span> results
        </div>
        <div v-else></div>

        <!-- Pagination Controls -->
        <div class="flex items-center gap-2">
            <!-- First Page -->
            <Button
                :as="Link"
                :href="getFirstPageUrl() || '#'"
                :disabled="pagination.current_page === 1"
                variant="outline"
                size="icon"
                class="h-8 w-8"
                :class="{
                    'pointer-events-none opacity-50':
                        pagination.current_page === 1,
                }"
            >
                <ChevronsLeft :size="16" />
            </Button>

            <!-- Previous Page -->
            <Button
                :as="Link"
                :href="getPreviousPageUrl() || '#'"
                :disabled="pagination.current_page === 1"
                variant="outline"
                size="icon"
                class="h-8 w-8"
                :class="{
                    'pointer-events-none opacity-50':
                        pagination.current_page === 1,
                }"
            >
                <ChevronLeft :size="16" />
            </Button>

            <!-- Page Numbers -->
            <template v-for="(link, index) in pagination.links" :key="index">
                <Button
                    v-if="getPageNumber(link.label) !== null"
                    :as="Link"
                    :href="getPageUrl(link) || '#'"
                    :variant="link.active ? 'default' : 'outline'"
                    size="icon"
                    class="h-8 w-8"
                    :class="{ 'pointer-events-none': !link.url }"
                >
                    {{ link.label }}
                </Button>
            </template>

            <!-- Next Page -->
            <Button
                :as="Link"
                :href="getNextPageUrl() || '#'"
                :disabled="pagination.current_page === pagination.last_page"
                variant="outline"
                size="icon"
                class="h-8 w-8"
                :class="{
                    'pointer-events-none opacity-50':
                        pagination.current_page === pagination.last_page,
                }"
            >
                <ChevronRight :size="16" />
            </Button>

            <!-- Last Page -->
            <Button
                :as="Link"
                :href="getLastPageUrl() || '#'"
                :disabled="pagination.current_page === pagination.last_page"
                variant="outline"
                size="icon"
                class="h-8 w-8"
                :class="{
                    'pointer-events-none opacity-50':
                        pagination.current_page === pagination.last_page,
                }"
            >
                <ChevronsRight :size="16" />
            </Button>
        </div>
    </div>
</template>
