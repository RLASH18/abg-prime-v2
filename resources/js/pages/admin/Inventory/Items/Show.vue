<script setup lang="ts">
import LinkButton from '@/components/LinkButton.vue';
import { Badge } from '@/components/ui/badge';
import { useFormatters } from '@/composables/useFormatters';
import AppLayout from '@/layouts/AppLayout.vue';
import itemsRoutes from '@/routes/admin/items';
import { type BreadcrumbItem } from '@/types';
import type { InventoryItem, Supplier } from '@/types/admin';
import { Head } from '@inertiajs/vue3';
import {
    AlertCircle,
    Boxes,
    Building2,
    Hash,
    Package,
    PhilippinePeso,
    Tag,
} from 'lucide-vue-next';
import { computed } from 'vue';

interface Props {
    item: InventoryItem;
    supplier?: Supplier | null;
}

const props = defineProps<Props>();
const { formatCurrency } = useFormatters();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Items',
        href: itemsRoutes.index().url,
    },
    {
        title: 'Item Information',
        href: itemsRoutes.show(props.item.id).url,
    },
];

// Compute stock status
const stockStatus = computed(() => {
    if (props.item.quantity <= 0) {
        return {
            label: 'Out of Stock',
            color: 'destructive' as const,
            textClass: 'text-destructive',
        };
    } else if (props.item.quantity <= props.item.restock_threshold) {
        return {
            label: 'Low Stock',
            color: 'secondary' as const,
            textClass: 'text-warning',
        };
    }
    return {
        label: 'In Stock',
        color: 'default' as const,
        textClass: 'text-primary',
    };
});

// Get all available images
const images = computed(() => {
    return [
        props.item.item_image_1,
        props.item.item_image_2,
        props.item.item_image_3,
    ]
        .filter(Boolean)
        .map((path) => `/storage/${path}`);
});
</script>

<template>
    <Head :title="`Admin - Item: ${item.item_name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4"
        >
            <!-- Header -->
            <div class="mb-4 flex items-start justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Item Information</h1>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Item Code: {{ item.item_code }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <LinkButton
                        :href="itemsRoutes.edit(item.id).url"
                        label="Edit Item"
                    />
                    <LinkButton
                        :href="itemsRoutes.index().url"
                        mode="back"
                        label="Back to list"
                    />
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Left Column - Images -->
                <div class="lg:col-span-1">
                    <div
                        class="sticky top-4 rounded-lg border border-border bg-card p-6 shadow-sm"
                    >
                        <h2 class="mb-4 text-lg font-semibold">
                            Product Images
                        </h2>

                        <div v-if="images.length > 0" class="space-y-4">
                            <!-- Main Image -->
                            <div
                                class="aspect-square overflow-hidden rounded-lg border-2 border-border bg-muted"
                            >
                                <img
                                    :src="images[0]"
                                    :alt="item.item_name"
                                    class="h-full w-full object-cover"
                                />
                            </div>

                            <!-- Thumbnail Images -->
                            <div
                                v-if="images.length > 1"
                                class="grid grid-cols-3 gap-2"
                            >
                                <div
                                    v-for="(image, index) in images"
                                    :key="index"
                                    class="aspect-square cursor-pointer overflow-hidden rounded-md border border-border bg-muted transition-colors hover:border-primary"
                                >
                                    <img
                                        :src="image"
                                        :alt="`${item.item_name} - ${index + 1}`"
                                        class="h-full w-full object-cover"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- No Images Placeholder -->
                        <div
                            v-else
                            class="flex aspect-square flex-col items-center justify-center rounded-lg border-2 border-dashed border-border bg-muted"
                        >
                            <Package
                                :size="48"
                                class="mb-2 text-muted-foreground"
                            />
                            <p class="text-sm text-muted-foreground">
                                No images available
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Details -->
                <div class="space-y-6 lg:col-span-2">
                    <!-- Stock Status Card -->
                    <div
                        class="rounded-lg border border-border bg-card p-6 shadow-sm"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="mb-1 text-sm text-muted-foreground">
                                    Stock Status
                                </p>
                                <div class="flex items-center gap-2">
                                    <Badge
                                        :variant="stockStatus.color"
                                        class="rounded-full border border-primary/10"
                                    >
                                        {{ stockStatus.label }}
                                    </Badge>
                                    <span
                                        :class="[
                                            'text-2xl font-bold',
                                            stockStatus.textClass,
                                        ]"
                                    >
                                        {{ item.quantity }} units
                                    </span>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="mb-1 text-sm text-muted-foreground">
                                    Unit Price
                                </p>
                                <p class="text-2xl font-bold text-primary">
                                    {{ formatCurrency(item.unit_price) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Item Details Card -->
                    <div
                        class="rounded-lg border border-border bg-card p-6 shadow-sm"
                    >
                        <h2 class="mb-4 text-lg font-semibold">Item Details</h2>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <!-- Item Name -->
                            <div class="flex items-start gap-3">
                                <div class="rounded-lg bg-primary/10 p-2">
                                    <Package :size="20" class="text-primary" />
                                </div>
                                <div>
                                    <p class="text-sm text-muted-foreground">
                                        Item Name
                                    </p>
                                    <p class="font-medium">
                                        {{ item.item_name }}
                                    </p>
                                </div>
                            </div>

                            <!-- Brand -->
                            <div class="flex items-start gap-3">
                                <div class="rounded-lg bg-primary/10 p-2">
                                    <Tag :size="20" class="text-primary" />
                                </div>
                                <div>
                                    <p class="text-sm text-muted-foreground">
                                        Brand
                                    </p>
                                    <p class="font-medium">
                                        {{ item.brand_name }}
                                    </p>
                                </div>
                            </div>

                            <!-- Category -->
                            <div class="flex items-start gap-3">
                                <div class="rounded-lg bg-primary/10 p-2">
                                    <Boxes :size="20" class="text-primary" />
                                </div>
                                <div>
                                    <p class="text-sm text-muted-foreground">
                                        Category
                                    </p>
                                    <p class="font-medium">
                                        {{ item.category }}
                                    </p>
                                </div>
                            </div>

                            <!-- Item Code -->
                            <div class="flex items-start gap-3">
                                <div class="rounded-lg bg-primary/10 p-2">
                                    <Hash :size="20" class="text-primary" />
                                </div>
                                <div>
                                    <p class="text-sm text-muted-foreground">
                                        Item Code
                                    </p>
                                    <p class="font-mono font-medium">
                                        {{ item.item_code }}
                                    </p>
                                </div>
                            </div>

                            <!-- Restock Threshold -->
                            <div class="flex items-start gap-3">
                                <div class="rounded-lg bg-primary/10 p-2">
                                    <AlertCircle
                                        :size="20"
                                        class="text-primary"
                                    />
                                </div>
                                <div>
                                    <p class="text-sm text-muted-foreground">
                                        Restock Threshold
                                    </p>
                                    <p class="font-medium">
                                        {{ item.restock_threshold }} units
                                    </p>
                                </div>
                            </div>

                            <!-- Total Value -->
                            <div class="flex items-start gap-3">
                                <div class="rounded-lg bg-primary/10 p-2">
                                    <PhilippinePeso
                                        :size="20"
                                        class="text-primary"
                                    />
                                </div>
                                <div>
                                    <p class="text-sm text-muted-foreground">
                                        Total Value
                                    </p>
                                    <p class="font-medium">
                                        {{
                                            formatCurrency(
                                                item.unit_price * item.quantity,
                                            )
                                        }}
                                    </p>
                                </div>
                            </div>

                            <!-- Supplier -->
                            <div v-if="supplier" class="flex items-start gap-3">
                                <div class="rounded-lg bg-primary/10 p-2">
                                    <Building2
                                        :size="20"
                                        class="text-primary"
                                    />
                                </div>
                                <div>
                                    <p class="text-sm text-muted-foreground">
                                        Supplier
                                    </p>
                                    <p class="font-medium">
                                        {{ supplier.supplier_name }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description Card -->
                    <div
                        v-if="item.description"
                        class="rounded-lg border border-border bg-card p-6 shadow-sm"
                    >
                        <h2 class="mb-3 text-lg font-semibold">Description</h2>
                        <p class="leading-relaxed text-muted-foreground">
                            {{ item.description }}
                        </p>
                    </div>

                    <!-- Supplier Details Card -->
                    <div
                        v-if="supplier"
                        class="rounded-lg border border-border bg-card p-6 shadow-sm"
                    >
                        <h2 class="mb-4 text-lg font-semibold">
                            Supplier Information
                        </h2>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div v-if="supplier.supplier_name">
                                <p class="text-sm text-muted-foreground">
                                    Supplier name
                                </p>
                                <p class="font-medium">
                                    {{ supplier.supplier_name }}
                                </p>
                            </div>
                            <div v-if="supplier.phone">
                                <p class="text-sm text-muted-foreground">
                                    Phone Number
                                </p>
                                <p class="font-medium">{{ supplier.phone }}</p>
                            </div>
                            <div v-if="supplier.email">
                                <p class="text-sm text-muted-foreground">
                                    Email
                                </p>
                                <p class="font-medium">{{ supplier.email }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
