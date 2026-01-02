<script setup lang="ts">
import LinkButton from '@/components/LinkButton.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { useFormatters } from '@/composables/useFormatters';
import AppLayout from '@/layouts/AppLayout.vue';
import cartsRoutes from '@/routes/customer/carts';
import checkoutRoutes from '@/routes/customer/checkout';
import homepageRoutes from '@/routes/customer/homepage';
import { type AppPageProps, type BreadcrumbItem } from '@/types';
import { CartItem } from '@/types/customer';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import {
    CreditCard,
    MapPin,
    Package,
    ShoppingBag,
    Truck,
} from 'lucide-vue-next';
import { computed } from 'vue';

interface Props {
    items: CartItem[];
    subTotal: number;
    total: number;
    item_count: number;
}

defineProps<Props>();
const { formatCurrency } = useFormatters();
const page = usePage<AppPageProps>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Homepage',
        href: homepageRoutes.index().url,
    },
    {
        title: 'My Cart',
        href: cartsRoutes.index().url,
    },
    {
        title: 'Checkout',
        href: '#',
    },
];

// Form data with default delivery address from user profile
const form = useForm({
    payment_method: '',
    delivery_method: '',
    delivery_address: page.props.auth.user.profile?.address || '',
});

const isDeliveryRequired = computed(() => form.delivery_method === 'delivery');

const processCheckout = () => {
    form.post(checkoutRoutes.process().url, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Checkout" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4 md:p-6"
        >
            <!-- Header -->
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="flex items-center gap-3 text-3xl font-bold">
                        Checkout
                    </h1>
                    <p class="mt-1 text-muted-foreground">
                        Complete your order
                    </p>
                </div>
                <LinkButton
                    :href="cartsRoutes.index().url"
                    mode="back"
                    label="Back to Cart"
                />
            </div>

            <!-- Checkout Content -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Left Column - Checkout Form -->
                <div class="space-y-6 lg:col-span-2">
                    <!-- Payment Method -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <CreditCard class="h-5 w-5" />
                                Payment Method
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="space-y-2">
                                <Label for="payment_method"
                                    >Select Payment Method</Label
                                >
                                <Select v-model="form.payment_method">
                                    <SelectTrigger id="payment_method">
                                        <SelectValue
                                            placeholder="Choose payment method"
                                        />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="cash">
                                            Cash on Delivery
                                        </SelectItem>
                                        <SelectItem value="gcash">
                                            GCash
                                        </SelectItem>
                                        <SelectItem value="bank_transfer">
                                            Bank Transfer
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <p
                                    v-if="form.errors.payment_method"
                                    class="text-sm text-destructive"
                                >
                                    {{ form.errors.payment_method }}
                                </p>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Delivery Method -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <Truck class="h-5 w-5" />
                                Delivery Method
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="space-y-2">
                                <Label for="delivery_method"
                                    >Select Delivery Method</Label
                                >
                                <Select v-model="form.delivery_method">
                                    <SelectTrigger id="delivery_method">
                                        <SelectValue
                                            placeholder="Choose delivery method"
                                        />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="pickup">
                                            Store Pickup
                                        </SelectItem>
                                        <SelectItem value="delivery">
                                            Home Delivery
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <p
                                    v-if="form.errors.delivery_method"
                                    class="text-sm text-destructive"
                                >
                                    {{ form.errors.delivery_method }}
                                </p>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Delivery Address -->
                    <Card v-if="isDeliveryRequired">
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <MapPin class="h-5 w-5" />
                                Delivery Address
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="space-y-2">
                                <Label for="delivery_address"
                                    >Complete Address</Label
                                >
                                <p class="text-sm text-muted-foreground">
                                    {{
                                        page.props.auth.user.profile?.address
                                            ? 'Pre-filled from your profile. You can edit if needed.'
                                            : 'Please enter your delivery address.'
                                    }}
                                </p>
                                <Textarea
                                    id="delivery_address"
                                    v-model="form.delivery_address"
                                    placeholder="Enter your complete delivery address"
                                    rows="4"
                                    :class="{
                                        'border-destructive':
                                            form.errors.delivery_address,
                                    }"
                                />
                                <p
                                    v-if="form.errors.delivery_address"
                                    class="text-sm text-destructive"
                                >
                                    {{ form.errors.delivery_address }}
                                </p>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Right Column - Order Summary -->
                <div class="lg:col-span-1">
                    <Card class="sticky top-6 border-2 border-primary/20">
                        <CardHeader>
                            <CardTitle>Order Summary</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <!-- Order Items -->
                            <div class="space-y-3">
                                <div
                                    v-for="item in items"
                                    :key="item.id"
                                    class="flex gap-3 rounded-lg border p-3"
                                >
                                    <div
                                        class="relative h-16 w-16 flex-shrink-0 overflow-hidden rounded-md border"
                                    >
                                        <img
                                            v-if="item.product.item_image_1"
                                            :src="`/storage/${item.product.item_image_1}`"
                                            :alt="item.product.item_name"
                                            class="h-full w-full object-contain p-1"
                                        />
                                        <div
                                            v-else
                                            class="flex h-full w-full items-center justify-center bg-muted"
                                        >
                                            <Package
                                                class="h-6 w-6 text-muted-foreground"
                                            />
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p
                                            class="line-clamp-1 text-sm font-medium"
                                            :title="item.product.item_name"
                                        >
                                            {{ item.product.item_name }}
                                        </p>
                                        <p
                                            class="text-xs text-muted-foreground"
                                        >
                                            {{ formatCurrency(item.price) }} ×
                                            {{ item.quantity }}
                                        </p>
                                        <p class="mt-1 text-sm font-semibold">
                                            {{
                                                formatCurrency(
                                                    item.price * item.quantity,
                                                )
                                            }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Summary Totals -->
                            <div class="space-y-2 border-t pt-4">
                                <div class="flex justify-between text-sm">
                                    <span class="text-muted-foreground"
                                        >Subtotal</span
                                    >
                                    <span class="font-medium">{{
                                        formatCurrency(subTotal)
                                    }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-muted-foreground"
                                        >Items</span
                                    >
                                    <span class="font-medium">{{
                                        item_count
                                    }}</span>
                                </div>
                            </div>

                            <div class="border-t pt-4">
                                <div
                                    class="flex items-baseline justify-between"
                                >
                                    <span class="text-lg font-semibold"
                                        >Total</span
                                    >
                                    <span
                                        class="text-2xl font-bold text-primary"
                                        >{{ formatCurrency(total) }}</span
                                    >
                                </div>
                            </div>
                        </CardContent>
                        <CardFooter class="flex-col gap-3">
                            <Button
                                @click="processCheckout"
                                :disabled="form.processing"
                                size="lg"
                                class="w-full"
                            >
                                <ShoppingBag class="mr-2 h-5 w-5" />
                                {{
                                    form.processing
                                        ? 'Processing...'
                                        : 'Place Order'
                                }}
                            </Button>
                            <LinkButton
                                :href="cartsRoutes.index().url"
                                mode="back"
                                label="Back to Cart"
                                class="w-full"
                            />
                        </CardFooter>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
