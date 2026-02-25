<script setup lang="ts">
import ChatsWidget from '@/components/customer/ChatsWidget.vue';
import LinkButton from '@/components/LinkButton.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { useFormatters } from '@/composables/useFormatters';
import AppLayout from '@/layouts/AppLayout.vue';
import cartsRoutes from '@/routes/customer/carts';
import homepageRoutes from '@/routes/customer/homepage';
import { type BreadcrumbItem } from '@/types';
import { Product } from '@/types/customer';
import { Head, router } from '@inertiajs/vue3';
import {
    AlertCircle,
    Check,
    Info,
    MessageCircle,
    Minus,
    Package,
    Percent,
    Plus,
    ShoppingCart,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface Props {
    product: Product;
}

const props = defineProps<Props>();
const { formatCurrency } = useFormatters();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Homepage',
        href: homepageRoutes.index().url,
    },
    {
        title: props.product.item_name,
        href: '#',
    },
];

// Image gallery state
const selectedImageIndex = ref(0);
const productImages = computed(() => {
    const images = [];
    if (props.product.item_image_1)
        images.push(`/storage/${props.product.item_image_1}`);
    if (props.product.item_image_2)
        images.push(`/storage/${props.product.item_image_2}`);
    if (props.product.item_image_3)
        images.push(`/storage/${props.product.item_image_3}`);
    return images;
});

const selectedImage = computed(
    () => productImages.value[selectedImageIndex.value] || null,
);

// Quantity state
const quantity = ref(1);

const incrementQuantity = () => {
    if (quantity.value < props.product.quantity) {
        quantity.value++;
    }
};

const decrementQuantity = () => {
    if (quantity.value > 1) {
        quantity.value--;
    }
};

// Stock status
const stockStatus = computed(() => {
    if (props.product.quantity <= 0) {
        return {
            label: 'Out of Stock',
            variant: 'destructive' as const,
            icon: AlertCircle,
            class: 'bg-destructive/10 text-destructive border-destructive/20',
        };
    }
    if (props.product.quantity <= props.product.restock_threshold) {
        return {
            label: 'Low Stock',
            variant: 'secondary' as const,
            icon: AlertCircle,
            class: 'bg-warning/10 text-warning border-warning/20',
        };
    }
    return {
        label: 'In Stock',
        variant: 'default' as const,
        icon: Check,
        class: 'bg-green-500/10 text-green-600 border-green-500/20',
    };
});

const isOutOfStock = computed(() => props.product.quantity <= 0);

const addToCart = () => {
    router.post(
        cartsRoutes.store().url,
        {
            item_id: props.product.id,
            quantity: quantity.value,
            damaged_item_id: props.product.damaged_item_id || null,
        },
        {
            preserveScroll: true,
        },
    );
};

const displayPrice = computed((): number => {
    if (props.product.is_damaged && props.product.discounted_price != null) {
        return props.product.discounted_price;
    }
    return props.product.unit_price;
});

// Contact product
const chatItem = ref<typeof props.product | null>(null);

const openChatWithItem = () => {
    chatItem.value = props.product;
};
</script>

<template>
    <Head :title="product.item_name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4 md:p-6"
        >
            <!-- Header -->
            <div class="mb-2 flex items-start justify-between">
                <div>
                    <h1 class="text-3xl font-bold">{{ product.item_name }}</h1>
                    <p
                        class="mt-1 text-sm tracking-wide text-muted-foreground uppercase"
                    >
                        {{ product.brand_name }}
                    </p>
                </div>
                <LinkButton
                    :href="homepageRoutes.index().url"
                    mode="back"
                    label="Back to Products"
                />
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Left Column - Image Gallery -->
                <div class="space-y-4 lg:col-span-1">
                    <!-- Main Image -->
                    <Card class="overflow-hidden border-2">
                        <CardContent class="p-0">
                            <div class="relative aspect-square p-4">
                                <img
                                    v-if="selectedImage"
                                    :src="selectedImage"
                                    :alt="product.item_name"
                                    class="h-full w-full object-contain transition-transform duration-500 hover:scale-110"
                                />
                                <div
                                    v-else
                                    class="flex h-full w-full items-center justify-center rounded-xl bg-gradient-to-br from-muted to-muted/50"
                                >
                                    <Package
                                        class="h-24 w-24 text-muted-foreground"
                                    />
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Image Thumbnails -->
                    <div
                        v-if="productImages.length > 1"
                        class="grid grid-cols-3 gap-2"
                    >
                        <button
                            v-for="(image, index) in productImages"
                            :key="index"
                            @click="selectedImageIndex = index"
                            :class="[
                                'relative aspect-square overflow-hidden rounded-lg border-2 p-2 transition-all duration-200',
                                selectedImageIndex === index
                                    ? 'border-primary ring-2 ring-primary/20'
                                    : 'border-border hover:border-primary/50',
                            ]"
                        >
                            <img
                                :src="image"
                                :alt="`${product.item_name} - Image ${index + 1}`"
                                class="h-full w-full object-contain"
                            />
                        </button>
                    </div>
                </div>

                <!-- Right Column - Product Info & Purchase -->
                <div class="space-y-6 lg:col-span-2">
                    <!-- Product Information Card -->
                    <Card>
                        <CardHeader>
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="mb-2 flex items-center gap-2">
                                        <Badge
                                            variant="secondary"
                                            class="text-xs"
                                            >{{ product.category }}</Badge
                                        >
                                        <Badge
                                            v-if="
                                                product.is_damaged &&
                                                product.discount_percentage
                                            "
                                            class="border bg-red-500 text-white"
                                        >
                                            <Percent class="mr-1 h-3 w-3" />
                                            {{ product.discount_percentage }}%
                                            OFF
                                        </Badge>
                                        <Badge
                                            :class="stockStatus.class"
                                            class="border"
                                        >
                                            <component
                                                :is="stockStatus.icon"
                                                class="mr-1 h-3 w-3"
                                            />
                                            {{ stockStatus.label }}
                                        </Badge>
                                    </div>
                                    <CardTitle
                                        class="text-4xl font-bold"
                                        :class="
                                            product.is_damaged
                                                ? 'text-red-500'
                                                : 'text-primary'
                                        "
                                    >
                                        {{ formatCurrency(displayPrice) }}
                                    </CardTitle>
                                    <p
                                        v-if="
                                            product.is_damaged &&
                                            product.discounted_price !== null
                                        "
                                        class="mt-1 text-lg text-muted-foreground line-through"
                                    >
                                        {{ formatCurrency(product.unit_price) }}
                                    </p>
                                    <p
                                        class="mt-1 text-sm text-muted-foreground"
                                    >
                                        per unit
                                    </p>
                                </div>
                            </div>
                        </CardHeader>

                        <CardContent class="space-y-4">
                            <div>
                                <p class="mb-1 text-sm text-muted-foreground">
                                    Item Code
                                </p>
                                <p class="font-mono font-medium">
                                    {{ product.item_code }}
                                </p>
                            </div>

                            <div v-if="product.description">
                                <p class="mb-2 text-sm text-muted-foreground">
                                    Description
                                </p>
                                <p class="text-base leading-relaxed">
                                    {{ product.description }}
                                </p>
                            </div>

                            <!-- Damage Remarks (for damaged items) -->
                            <div
                                v-if="product.is_damaged && product.remarks"
                                class="rounded-lg border border-orange-200 bg-orange-50 p-3 dark:border-orange-900 dark:bg-orange-950"
                            >
                                <p
                                    class="mb-1 text-sm font-medium text-orange-700 dark:text-orange-400"
                                >
                                    Damage Information
                                </p>
                                <p
                                    class="text-sm text-orange-600 dark:text-orange-300"
                                >
                                    {{ product.remarks }}
                                </p>
                            </div>

                            <div class="grid grid-cols-2 gap-4 border-t pt-4">
                                <div>
                                    <p
                                        class="mb-1 text-sm text-muted-foreground"
                                    >
                                        Available Stock
                                    </p>
                                    <p
                                        class="text-lg font-semibold"
                                        :class="{
                                            'text-destructive':
                                                product.quantity <= 0,
                                            'text-warning':
                                                product.quantity > 0 &&
                                                product.quantity <=
                                                    product.restock_threshold,
                                            'text-green-600':
                                                product.quantity >
                                                product.restock_threshold,
                                        }"
                                    >
                                        {{ product.quantity }} units
                                    </p>
                                </div>
                                <div>
                                    <p
                                        class="mb-1 text-sm text-muted-foreground"
                                    >
                                        Brand
                                    </p>
                                    <p class="text-lg font-semibold">
                                        {{ product.brand_name }}
                                    </p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Purchase Card -->
                    <Card class="border-2 border-primary/20">
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <ShoppingCart class="h-5 w-5" />
                                Add to Cart
                            </CardTitle>
                        </CardHeader>

                        <CardContent class="space-y-4">
                            <!-- Quantity Selector -->
                            <div>
                                <label class="mb-2 block text-sm font-medium"
                                    >Quantity</label
                                >
                                <div class="flex items-center gap-3">
                                    <Button
                                        @click="decrementQuantity"
                                        :disabled="
                                            quantity <= 1 || isOutOfStock
                                        "
                                        variant="outline"
                                        size="icon"
                                        class="h-10 w-10"
                                    >
                                        <Minus class="h-4 w-4" />
                                    </Button>
                                    <Input
                                        v-model.number="quantity"
                                        type="number"
                                        :min="1"
                                        :max="product.quantity"
                                        :disabled="isOutOfStock"
                                        class="h-10 w-20 text-center text-lg font-semibold [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none"
                                    />
                                    <Button
                                        @click="incrementQuantity"
                                        :disabled="
                                            quantity >= product.quantity ||
                                            isOutOfStock
                                        "
                                        variant="outline"
                                        size="icon"
                                        class="h-10 w-10"
                                    >
                                        <Plus class="h-4 w-4" />
                                    </Button>
                                    <div class="flex-1 text-right">
                                        <p
                                            class="text-sm text-muted-foreground"
                                        >
                                            Subtotal
                                        </p>
                                        <p
                                            class="text-xl font-bold"
                                            :class="
                                                product.is_damaged
                                                    ? 'text-red-500'
                                                    : 'text-primary'
                                            "
                                        >
                                            {{
                                                formatCurrency(
                                                    displayPrice * quantity,
                                                )
                                            }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Add to Cart Button -->
                            <Button
                                :disabled="isOutOfStock"
                                @click="addToCart"
                                size="lg"
                                class="h-12 w-full text-lg"
                            >
                                <ShoppingCart class="mr-2 h-5 w-5" />
                                {{
                                    isOutOfStock
                                        ? 'Out of Stock'
                                        : 'Add to Cart'
                                }}
                            </Button>

                            <!-- Contact Us Button -->
                            <Button
                                @click="openChatWithItem"
                                variant="outline"
                                size="lg"
                                class="h-12 w-full text-lg"
                            >
                                <MessageCircle class="mr-2 h-5 w-5" />
                                Contact Us About This Item
                            </Button>

                            <!-- Stock Warning -->
                            <div
                                v-if="
                                    !isOutOfStock &&
                                    product.quantity <=
                                        product.restock_threshold
                                "
                                class="flex items-start gap-2 rounded-lg border border-warning/20 bg-warning/10 p-3"
                            >
                                <AlertCircle
                                    class="mt-0.5 h-5 w-5 flex-shrink-0 text-warning"
                                />
                                <div class="text-sm">
                                    <p class="font-semibold text-warning">
                                        Low Stock Alert
                                    </p>
                                    <p class="text-muted-foreground">
                                        Only {{ product.quantity }} units
                                        remaining. Order soon!
                                    </p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Product Details Card -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <Info class="h-5 w-5" />
                                Product Details
                            </CardTitle>
                        </CardHeader>

                        <CardContent>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div class="flex items-start gap-3">
                                    <div class="rounded-lg bg-primary/10 p-2">
                                        <Package class="h-5 w-5 text-primary" />
                                    </div>
                                    <div>
                                        <p
                                            class="text-sm text-muted-foreground"
                                        >
                                            Category
                                        </p>
                                        <p class="font-medium">
                                            {{ product.category }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <div class="rounded-lg bg-primary/10 p-2">
                                        <Package class="h-5 w-5 text-primary" />
                                    </div>
                                    <div>
                                        <p
                                            class="text-sm text-muted-foreground"
                                        >
                                            Item Code
                                        </p>
                                        <p class="font-mono font-medium">
                                            {{ product.item_code }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <div class="rounded-lg bg-primary/10 p-2">
                                        <Check class="h-5 w-5 text-primary" />
                                    </div>
                                    <div>
                                        <p
                                            class="text-sm text-muted-foreground"
                                        >
                                            Availability
                                        </p>
                                        <p class="font-medium">
                                            {{ stockStatus.label }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <div class="rounded-lg bg-primary/10 p-2">
                                        <Package class="h-5 w-5 text-primary" />
                                    </div>
                                    <div>
                                        <p
                                            class="text-sm text-muted-foreground"
                                        >
                                            Stock Quantity
                                        </p>
                                        <p class="font-medium">
                                            {{ product.quantity }} units
                                            available
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>

        <ChatsWidget :attached-item="chatItem" />
    </AppLayout>
</template>
