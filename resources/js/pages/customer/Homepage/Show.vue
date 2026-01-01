<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Product } from '@/types/customer';
import {
    Package,
    ShoppingCart,
    Info,
    Minus,
    Plus,
    Check,
    AlertCircle,
} from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import LinkButton from '@/components/LinkButton.vue';
import { Input } from '@/components/ui/input';
import { useFormatters } from '@/composables/useFormatters';
import homepageRoutes from '@/routes/customer/homepage';
import { ref, computed } from 'vue';
import cartsRoutes from '@/routes/customer/carts';

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
    if (props.product.item_image_1) images.push(`/storage/${props.product.item_image_1}`);
    if (props.product.item_image_2) images.push(`/storage/${props.product.item_image_2}`);
    if (props.product.item_image_3) images.push(`/storage/${props.product.item_image_3}`);
    return images;
});

const selectedImage = computed(() => productImages.value[selectedImageIndex.value] || null);

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
    router.post(cartsRoutes.store().url, {
        item_id: props.product.id,
        quantity: quantity.value,
    }, {
        preserveScroll: true
    });
}
</script>

<template>

    <Head :title="product.item_name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4 md:p-6">
            <!-- Header -->
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h1 class="text-3xl font-bold">{{ product.item_name }}</h1>
                    <p class="text-sm text-muted-foreground mt-1 uppercase tracking-wide">{{ product.brand_name }}</p>
                </div>
                <LinkButton :href="homepageRoutes.index().url" mode="back" label="Back to Products" />
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column - Image Gallery -->
                <div class="lg:col-span-1 space-y-4">
                    <!-- Main Image -->
                    <Card class="overflow-hidden border-2">
                        <CardContent class="p-0">
                            <div class="relative aspect-square p-4">
                                <img v-if="selectedImage" :src="selectedImage" :alt="product.item_name"
                                    class="w-full h-full object-contain hover:scale-110 transition-transform duration-500" />
                                <div v-else
                                    class="w-full h-full flex items-center justify-center rounded-xl bg-gradient-to-br from-muted to-muted/50">
                                    <Package class="h-24 w-24 text-muted-foreground" />
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Image Thumbnails -->
                    <div v-if="productImages.length > 1" class="grid grid-cols-3 gap-2">
                        <button v-for="(image, index) in productImages" :key="index" @click="selectedImageIndex = index"
                            :class="[
                                'relative aspect-square rounded-lg border-2 overflow-hidden transition-all duration-200 p-2',
                                selectedImageIndex === index
                                    ? 'border-primary ring-2 ring-primary/20'
                                    : 'border-border hover:border-primary/50'
                            ]">
                            <img :src="image" :alt="`${product.item_name} - Image ${index + 1}`"
                                class="w-full h-full object-contain" />
                        </button>
                    </div>
                </div>

                <!-- Right Column - Product Info & Purchase -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Product Information Card -->
                    <Card>
                        <CardHeader>
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <Badge variant="secondary" class="text-xs">{{ product.category }}</Badge>
                                        <Badge :class="stockStatus.class" class="border">
                                            <component :is="stockStatus.icon" class="h-3 w-3 mr-1" />
                                            {{ stockStatus.label }}
                                        </Badge>
                                    </div>
                                    <CardTitle class="text-4xl font-bold text-primary">
                                        {{ formatCurrency(product.unit_price) }}
                                    </CardTitle>
                                    <p class="text-sm text-muted-foreground mt-1">per unit</p>
                                </div>
                            </div>
                        </CardHeader>

                        <CardContent class="space-y-4">
                            <div>
                                <p class="text-sm text-muted-foreground mb-1">Item Code</p>
                                <p class="font-mono font-medium">{{ product.item_code }}</p>
                            </div>

                            <div v-if="product.description">
                                <p class="text-sm text-muted-foreground mb-2">Description</p>
                                <p class="text-base leading-relaxed">{{ product.description }}</p>
                            </div>

                            <div class="grid grid-cols-2 gap-4 pt-4 border-t">
                                <div>
                                    <p class="text-sm text-muted-foreground mb-1">Available Stock</p>
                                    <p class="font-semibold text-lg" :class="{
                                        'text-destructive': product.quantity <= 0,
                                        'text-warning': product.quantity > 0 && product.quantity <= product.restock_threshold,
                                        'text-green-600': product.quantity > product.restock_threshold
                                    }">
                                        {{ product.quantity }} units
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-muted-foreground mb-1">Brand</p>
                                    <p class="font-semibold text-lg">{{ product.brand_name }}</p>
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
                                <label class="text-sm font-medium mb-2 block">Quantity</label>
                                <div class="flex items-center gap-3">
                                    <Button
                                        @click="decrementQuantity"
                                        :disabled="quantity <= 1 || isOutOfStock"
                                        variant="outline"
                                        size="icon"
                                        class="h-10 w-10"
                                    >
                                        <Minus class="h-4 w-4" />
                                    </Button>
                                    <Input v-model.number="quantity" type="number" :min="1" :max="product.quantity"
                                        :disabled="isOutOfStock"
                                        class="text-center font-semibold text-lg h-10 w-20 [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" />
                                    <Button
                                        @click="incrementQuantity"
                                        :disabled="quantity >= product.quantity || isOutOfStock"
                                        variant="outline"
                                        size="icon"
                                        class="h-10 w-10"
                                    >
                                        <Plus class="h-4 w-4" />
                                    </Button>
                                    <div class="flex-1 text-right">
                                        <p class="text-sm text-muted-foreground">Subtotal</p>
                                        <p class="text-xl font-bold text-primary">
                                            {{ formatCurrency(product.unit_price * quantity) }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Add to Cart Button -->
                            <Button
                                :disabled="isOutOfStock"
                                @click="addToCart"
                                size="lg"
                                class="w-full text-lg h-12"
                            >
                                <ShoppingCart class="h-5 w-5 mr-2" />
                                {{ isOutOfStock ? 'Out of Stock' : 'Add to Cart' }}
                            </Button>

                            <!-- Stock Warning -->
                            <div v-if="!isOutOfStock && product.quantity <= product.restock_threshold"
                                class="flex items-start gap-2 p-3 rounded-lg bg-warning/10 border border-warning/20">
                                <AlertCircle class="h-5 w-5 text-warning flex-shrink-0 mt-0.5" />
                                <div class="text-sm">
                                    <p class="font-semibold text-warning">Low Stock Alert</p>
                                    <p class="text-muted-foreground">Only {{ product.quantity }} units remaining. Order
                                        soon!</p>
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
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex items-start gap-3">
                                    <div class="p-2 rounded-lg bg-primary/10">
                                        <Package class="h-5 w-5 text-primary" />
                                    </div>
                                    <div>
                                        <p class="text-sm text-muted-foreground">Category</p>
                                        <p class="font-medium">{{ product.category }}</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <div class="p-2 rounded-lg bg-primary/10">
                                        <Package class="h-5 w-5 text-primary" />
                                    </div>
                                    <div>
                                        <p class="text-sm text-muted-foreground">Item Code</p>
                                        <p class="font-medium font-mono">{{ product.item_code }}</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <div class="p-2 rounded-lg bg-primary/10">
                                        <Check class="h-5 w-5 text-primary" />
                                    </div>
                                    <div>
                                        <p class="text-sm text-muted-foreground">Availability</p>
                                        <p class="font-medium">{{ stockStatus.label }}</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <div class="p-2 rounded-lg bg-primary/10">
                                        <Package class="h-5 w-5 text-primary" />
                                    </div>
                                    <div>
                                        <p class="text-sm text-muted-foreground">Stock Quantity</p>
                                        <p class="font-medium">{{ product.quantity }} units available</p>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                </div>
            </div>
        </div>
    </AppLayout>
</template>
