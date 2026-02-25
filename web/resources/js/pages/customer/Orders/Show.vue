<script setup lang="ts">
import LinkButton from '@/components/LinkButton.vue';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { useFormatters } from '@/composables/useFormatters';
import AppLayout from '@/layouts/AppLayout.vue';
import ordersRoutes from '@/routes/customer/orders';
import { type BreadcrumbItem } from '@/types';
import { Order } from '@/types/admin';
import { Head } from '@inertiajs/vue3';
import {
    Calendar,
    CreditCard,
    MapPin,
    Package,
    Truck,
    User,
} from 'lucide-vue-next';

interface Props {
    order: Order;
}

const props = defineProps<Props>();
const { formatCurrency, formatDate } = useFormatters();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'My Orders',
        href: ordersRoutes.index().url,
    },
    {
        title: `Order #${props.order.id.toString().padStart(4, '0')}`,
        href: '#',
    },
];

const getStatusBadge = (status: Order['status']) => {
    const badges = {
        pending: {
            label: 'Pending',
            class: 'bg-yellow-500/10 text-yellow-600 border-yellow-500/20',
        },
        confirmed: {
            label: 'Confirmed',
            class: 'bg-blue-500/10 text-blue-600 border-blue-500/20',
        },
        assembled: {
            label: 'Assembled',
            class: 'bg-purple-500/10 text-purple-600 border-purple-500/20',
        },
        shipped: {
            label: 'Shipped',
            class: 'bg-indigo-500/10 text-indigo-600 border-indigo-500/20',
        },
        delivered: {
            label: 'Delivered',
            class: 'bg-green-500/10 text-green-600 border-green-500/20',
        },
        paid: {
            label: 'Paid',
            class: 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20',
        },
        cancelled: {
            label: 'Cancelled',
            class: 'bg-destructive/10 text-destructive border-destructive/20',
        },
    };
    return badges[status] || badges.pending;
};
</script>

<template>
    <Head :title="`Order #${order.id.toString().padStart(4, '0')}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4 md:p-6"
        >
            <!-- Header -->
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="flex items-center gap-3 text-3xl font-bold">
                        Order #{{ order.id.toString().padStart(4, '0') }}
                    </h1>
                    <p class="mt-1 text-muted-foreground">
                        Placed on {{ formatDate(order.created_at) }}
                    </p>
                </div>
                <LinkButton
                    :href="ordersRoutes.index().url"
                    mode="back"
                    label="Back to Orders"
                />
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Left Column - Order Details -->
                <div class="space-y-6 lg:col-span-2">
                    <!-- Order Status -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Order Status</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <Badge
                                :class="getStatusBadge(order.status).class"
                                class="border text-base"
                            >
                                {{ getStatusBadge(order.status).label }}
                            </Badge>
                        </CardContent>
                    </Card>

                    <!-- Order Items -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <Package class="h-5 w-5" />
                                Order Items
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div
                                v-for="item in order.order_items"
                                :key="item.id"
                                class="flex gap-4 rounded-lg border p-4"
                            >
                                <div
                                    class="flex h-20 w-20 flex-shrink-0 items-center justify-center rounded-lg border bg-muted"
                                >
                                    <img
                                        v-if="item.item?.item_image_1"
                                        :src="`/storage/${item.item.item_image_1}`"
                                        :alt="item.item.item_name"
                                        class="h-full w-full object-contain p-2"
                                    />
                                    <Package
                                        v-else
                                        class="h-10 w-10 text-muted-foreground"
                                    />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="font-semibold">
                                        {{ item.item?.item_name || 'Item' }}
                                    </h4>
                                    <p class="text-sm text-muted-foreground">
                                        Code:
                                        {{ item.item?.item_code || 'N/A' }}
                                    </p>
                                    <p class="mt-2 text-sm">
                                        <span class="text-muted-foreground"
                                            >Quantity:</span
                                        >
                                        {{ item.quantity }}
                                    </p>
                                    <p class="text-sm">
                                        <span class="text-muted-foreground"
                                            >Unit Price:</span
                                        >
                                        {{
                                            formatCurrency(
                                                Number(item.unit_price),
                                            )
                                        }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-muted-foreground">
                                        Subtotal
                                    </p>
                                    <p class="text-xl font-bold text-primary">
                                        {{
                                            formatCurrency(
                                                Number(item.unit_price) *
                                                    item.quantity,
                                            )
                                        }}
                                    </p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Delivery Information -->
                    <Card v-if="order.delivery_method === 'delivery'">
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <MapPin class="h-5 w-5" />
                                Delivery Address
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p class="whitespace-pre-line">
                                {{ order.delivery_address || 'N/A' }}
                            </p>
                        </CardContent>
                    </Card>
                </div>

                <!-- Right Column - Order Summary -->
                <div class="lg:col-span-1">
                    <Card class="sticky top-6">
                        <CardHeader>
                            <CardTitle>Order Summary</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <!-- Customer Info -->
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <User
                                        class="h-4 w-4 text-muted-foreground"
                                    />
                                    <p class="text-sm font-medium">Customer</p>
                                </div>
                                <p class="text-sm">
                                    {{ order.user?.name || 'N/A' }}
                                </p>
                                <p class="text-sm text-muted-foreground">
                                    {{ order.user?.email || 'N/A' }}
                                </p>
                            </div>

                            <Separator />

                            <!-- Order Date -->
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <Calendar
                                        class="h-4 w-4 text-muted-foreground"
                                    />
                                    <p class="text-sm font-medium">
                                        Order Date
                                    </p>
                                </div>
                                <p class="text-sm">
                                    {{ formatDate(order.created_at) }}
                                </p>
                            </div>

                            <Separator />

                            <!-- Payment Method -->
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <CreditCard
                                        class="h-4 w-4 text-muted-foreground"
                                    />
                                    <p class="text-sm font-medium">
                                        Payment Method
                                    </p>
                                </div>
                                <p class="text-sm capitalize">
                                    {{ order.payment_method.replace('_', ' ') }}
                                </p>
                            </div>

                            <Separator />

                            <!-- Delivery Method -->
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <Truck
                                        class="h-4 w-4 text-muted-foreground"
                                    />
                                    <p class="text-sm font-medium">
                                        Delivery Method
                                    </p>
                                </div>
                                <p class="text-sm capitalize">
                                    {{ order.delivery_method }}
                                </p>
                            </div>

                            <Separator />

                            <!-- Total -->
                            <div class="space-y-2 pt-2">
                                <div
                                    class="flex items-baseline justify-between"
                                >
                                    <span class="text-lg font-semibold"
                                        >Total Amount</span
                                    >
                                    <span
                                        class="text-2xl font-bold text-primary"
                                    >
                                        {{
                                            formatCurrency(
                                                Number(order.total_amount),
                                            )
                                        }}
                                    </span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
