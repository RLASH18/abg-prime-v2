<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import {
    Trash2,
    Plus,
    Minus,
    Package,
    ArrowRight,
    ShoppingBag,
} from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle, CardFooter } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import LinkButton from '@/components/LinkButton.vue';
import { useFormatters } from '@/composables/useFormatters';
import homepageRoutes from '@/routes/customer/homepage';
import cartsRoutes from '@/routes/customer/carts';
import { ref } from 'vue';
import { CartItem } from '@/types/customer';

interface Props {
    cartItems: CartItem[];
    cartTotal: number;
    cartCount: number;
}

defineProps<Props>();
const { formatCurrency } = useFormatters();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Homepage',
        href: homepageRoutes.index().url,
    },
    {
        title: 'Shopping Cart',
        href: '#',
    },
];

const updatingItems = ref<Set<number>>(new Set());

const updateQuantity = (cartId: number, newQuantity: number) => {
    if (newQuantity < 1) return;

    updatingItems.value.add(cartId);

    router.put(cartsRoutes.update(cartId).url, {
        quantity: newQuantity,
    }, {
        preserveScroll: true,
        onFinish: () => {
            updatingItems.value.delete(cartId);
        },
    });
};

const removeItem = (cartId: number) => {
    if (confirm('Are you sure you want to remove this item from your cart?')) {
        router.delete(cartsRoutes.destroy(cartId).url, {
            preserveScroll: true,
        });
    }
};

const clearCart = () => {
    if (confirm('Are you sure you want to clear your entire cart?')) {
        router.delete(cartsRoutes.clear().url);
    }
};
</script>

<template>

    <Head title="Shopping Cart" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4 md:p-6">
            <!-- Header -->
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-3xl font-bold flex items-center gap-3">
                        Shopping Cart
                    </h1>
                    <p class="text-muted-foreground mt-1">
                        {{ cartCount }} {{ cartCount === 1 ? 'item' : 'items' }} in your cart
                    </p>
                </div>
                <LinkButton :href="homepageRoutes.index().url" mode="back" label="Continue Shopping" />
            </div>

            <!-- Empty Cart State -->
            <div v-if="cartItems.length === 0" class="flex flex-col items-center justify-center py-16 px-4">
                <div class="rounded-full bg-muted p-8 mb-6">
                    <ShoppingBag class="h-16 w-16 text-muted-foreground" />
                </div>
                <h3 class="text-2xl font-semibold mb-2">Your cart is empty</h3>
                <p class="text-muted-foreground text-center mb-6 max-w-md">
                    Looks like you haven't added any items to your cart yet. Start shopping to find great products!
                </p>
                <LinkButton :href="homepageRoutes.index().url" label="Start Shopping" size="lg" />
            </div>

            <!-- Cart Content -->
            <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Cart Items -->
                <div class="lg:col-span-2 space-y-4">
                    <Card v-for="cartItem in cartItems" :key="cartItem.id"
                        class="overflow-hidden border-2 hover:border-primary/50 transition-all duration-200">
                        <CardContent class="p-4">
                            <div class="flex gap-4">
                                <!-- Product Image -->
                                <div class="relative w-24 h-24 flex-shrink-0 rounded-lg overflow-hidden border">
                                    <img v-if="cartItem.product.item_image_1"
                                        :src="`/storage/${cartItem.product.item_image_1}`"
                                        :alt="cartItem.product.item_name" class="w-full h-full object-contain p-2" />
                                    <div v-else class="w-full h-full flex items-center justify-center bg-muted">
                                        <Package class="h-8 w-8 text-muted-foreground" />
                                    </div>
                                </div>

                                <!-- Product Info -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-start gap-2 mb-2">
                                        <div class="flex-1 min-w-0">
                                            <p
                                                class="text-xs text-muted-foreground font-medium uppercase tracking-wider">
                                                {{ cartItem.product.brand_name }}
                                            </p>
                                            <h3 class="font-bold text-lg line-clamp-1"
                                                :title="cartItem.product.item_name">
                                                {{ cartItem.product.item_name }}
                                            </h3>
                                            <p class="text-sm text-muted-foreground">
                                                Code: {{ cartItem.product.item_code }}
                                            </p>
                                        </div>
                                        <Button @click="removeItem(cartItem.id)" variant="ghost" size="icon"
                                            class="text-destructive hover:text-destructive hover:bg-destructive/10">
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </div>

                                    <div class="flex items-center justify-between gap-4 mt-4">
                                        <!-- Quantity Controls -->
                                        <div class="flex items-center gap-2">
                                            <Button @click="updateQuantity(cartItem.id, cartItem.quantity - 1)"
                                                :disabled="cartItem.quantity <= 1 || updatingItems.has(cartItem.id)"
                                                variant="outline" size="icon" class="h-8 w-8">
                                                <Minus class="h-3 w-3" />
                                            </Button>
                                            <Input v-model.number="cartItem.quantity" type="number" :min="1"
                                                :max="cartItem.product.quantity"
                                                @change="updateQuantity(cartItem.id, cartItem.quantity)"
                                                :disabled="updatingItems.has(cartItem.id)"
                                                class="text-center font-semibold w-16 h-8 [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" />
                                            <Button @click="updateQuantity(cartItem.id, cartItem.quantity + 1)"
                                                :disabled="cartItem.quantity >= cartItem.product.quantity || updatingItems.has(cartItem.id)"
                                                variant="outline" size="icon" class="h-8 w-8">
                                                <Plus class="h-3 w-3" />
                                            </Button>
                                        </div>

                                        <!-- Price -->
                                        <div class="text-right">
                                            <p class="text-xs text-muted-foreground">
                                                {{ formatCurrency(cartItem.price) }} × {{ cartItem.quantity }}
                                            </p>
                                            <p class="text-xl font-bold text-primary">
                                                {{ formatCurrency(cartItem.price * cartItem.quantity) }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Stock Warning -->
                                    <div v-if="cartItem.quantity > cartItem.product.quantity"
                                        class="mt-3 p-2 rounded-lg bg-destructive/10 border border-destructive/20">
                                        <p class="text-xs text-destructive font-medium">
                                            Only {{ cartItem.product.quantity }} units available
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Clear Cart Button -->
                    <Button @click="clearCart" variant="outline"
                        class="w-full text-destructive hover:bg-destructive/10">
                        <Trash2 class="h-4 w-4 mr-2" />
                        Clear Cart
                    </Button>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <Card class="sticky top-6 border-2 border-primary/20">
                        <CardHeader>
                            <CardTitle>Order Summary</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-muted-foreground">Subtotal</span>
                                    <span class="font-medium">{{ formatCurrency(cartTotal) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-muted-foreground">Items</span>
                                    <span class="font-medium">{{ cartCount }}</span>
                                </div>
                            </div>

                            <div class="border-t pt-4">
                                <div class="flex justify-between items-baseline">
                                    <span class="text-lg font-semibold">Total</span>
                                    <span class="text-2xl font-bold text-primary">{{ formatCurrency(cartTotal) }}</span>
                                </div>
                            </div>
                        </CardContent>
                        <CardFooter class="flex-col gap-3">
                            <Button size="lg" class="w-full">
                                Proceed to Checkout
                                <ArrowRight class="h-5 w-5 ml-2" />
                            </Button>
                            <LinkButton :href="homepageRoutes.index().url" mode="back" label="Continue Shopping" class="w-full" />
                        </CardFooter>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
