<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import itemsRoutes from '@/routes/admin/items';
import type { InventoryItem } from '@/types/admin';
import { router } from '@inertiajs/vue3';
import {
    AlertTriangle,
    ChevronDown,
    ChevronUp,
    Package,
    X,
} from 'lucide-vue-next';
import {
    ScrollAreaCorner,
    ScrollAreaRoot,
    ScrollAreaScrollbar,
    ScrollAreaThumb,
    ScrollAreaViewport,
} from 'reka-ui';
import { computed, ref } from 'vue';

interface Props {
    items: InventoryItem[];
}

const props = defineProps<Props>();

const isExpanded = ref(false);
const isDismissed = ref(false);

const lowStockItems = computed(() =>
    props.items.filter(
        (item) => item.quantity > 0 && item.quantity <= item.restock_threshold,
    ),
);

const outOfStockItems = computed(() =>
    props.items.filter((item) => item.quantity <= 0),
);

const totalAlerts = computed(
    () => lowStockItems.value.length + outOfStockItems.value.length,
);

const goToItem = (itemId: number) => {
    router.visit(itemsRoutes.show(itemId).url);
};
</script>

<template>
    <div
        v-if="totalAlerts > 0 && !isDismissed"
        class="rounded-lg border-2 border-warning/30 bg-warning/10 p-4"
    >
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="rounded-full bg-warning/20 p-2">
                    <AlertTriangle class="h-5 w-5 text-warning" />
                </div>
                <div>
                    <h3 class="font-semibold text-warning">Stock Alert</h3>
                    <p class="text-sm text-muted-foreground">
                        {{ outOfStockItems.length }} out of stock,
                        {{ lowStockItems.length }} low stock
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <Button
                    variant="ghost"
                    size="sm"
                    @click="isExpanded = !isExpanded"
                    class="text-warning hover:bg-warning/20"
                >
                    {{ isExpanded ? 'Collapse' : 'View Items' }}
                    <ChevronUp v-if="isExpanded" class="ml-1 h-4 w-4" />
                    <ChevronDown v-else class="ml-1 h-4 w-4" />
                </Button>
                <Button
                    variant="ghost"
                    size="icon"
                    @click="isDismissed = true"
                    class="h-8 w-8 text-muted-foreground hover:bg-warning/20"
                >
                    <X class="h-4 w-4" />
                </Button>
            </div>
        </div>

        <!-- Expanded Content -->
        <ScrollAreaRoot
            v-if="isExpanded"
            class="relative mt-4 h-64 overflow-hidden"
        >
            <ScrollAreaViewport class="h-full w-full rounded-[inherit]">
                <div class="space-y-2 pr-4">
                    <!-- Out of Stock Items -->
                    <div v-if="outOfStockItems.length > 0">
                        <p
                            class="mb-2 text-xs font-semibold text-destructive uppercase"
                        >
                            Out of Stock
                        </p>
                        <div
                            v-for="item in outOfStockItems"
                            :key="item.id"
                            @click="goToItem(item.id)"
                            class="flex cursor-pointer items-center justify-between rounded-md border border-destructive/20 bg-destructive/5 p-3 transition-colors hover:bg-destructive/10"
                        >
                            <div class="flex items-center gap-3">
                                <Package class="h-4 w-4 text-destructive" />
                                <div>
                                    <p class="text-sm font-medium">
                                        {{ item.item_name }}
                                    </p>
                                    <p class="text-xs text-muted-foreground">
                                        {{ item.item_code }}
                                    </p>
                                </div>
                            </div>
                            <Badge variant="destructive" class="text-xs">
                                0 units
                            </Badge>
                        </div>
                    </div>

                    <!-- Low Stock Items -->
                    <div v-if="lowStockItems.length > 0">
                        <p
                            class="mb-2 text-xs font-semibold text-warning uppercase"
                        >
                            Low Stock
                        </p>
                        <div
                            v-for="item in lowStockItems"
                            :key="item.id"
                            @click="goToItem(item.id)"
                            class="flex cursor-pointer items-center justify-between rounded-md border border-warning/20 bg-warning/5 p-3 transition-colors hover:bg-warning/10"
                        >
                            <div class="flex items-center gap-3">
                                <Package class="h-4 w-4 text-warning" />
                                <div>
                                    <p class="text-sm font-medium">
                                        {{ item.item_name }}
                                    </p>
                                    <p class="text-xs text-muted-foreground">
                                        {{ item.item_code }}
                                    </p>
                                </div>
                            </div>
                            <Badge
                                variant="secondary"
                                class="border border-warning/20 bg-warning/10 text-xs text-warning"
                            >
                                {{ item.quantity }} /
                                {{ item.restock_threshold }}
                            </Badge>
                        </div>
                    </div>
                </div>
            </ScrollAreaViewport>
            <ScrollAreaScrollbar
                orientation="vertical"
                class="flex h-full w-2.5 touch-none border-l border-l-transparent p-px transition-colors select-none"
            >
                <ScrollAreaThumb
                    class="relative flex-1 rounded-full bg-warning/60 hover:bg-warning/80"
                />
            </ScrollAreaScrollbar>
            <ScrollAreaCorner />
        </ScrollAreaRoot>
    </div>
</template>
