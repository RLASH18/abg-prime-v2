<script setup lang="ts">
import LinkButton from '@/components/LinkButton.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useFormatters } from '@/composables/useFormatters';
import AppLayout from '@/layouts/AppLayout.vue';
import homepageRoutes from '@/routes/customer/homepage';
import ordersRoutes from '@/routes/customer/orders';
import { type BreadcrumbItem, type PaginationData } from '@/types';
import { Order } from '@/types/admin';
import { Head, router } from '@inertiajs/vue3';
import { Calendar, CreditCard, Eye, Package, Truck } from 'lucide-vue-next';

interface Props {
    orders: {
        data: Order[];
    } & PaginationData;
    filters: {
        status?: string;
        payment_method?: string;
        date_from?: string;
        date_to?: string;
    };
}

defineProps<Props>();
const { formatCurrency, formatDate } = useFormatters();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Homepage',
        href: homepageRoutes.index().url,
    },
    {
        title: 'My Orders',
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

const viewOrder = (orderId: number) => {
    router.visit(ordersRoutes.show(orderId).url);
};
</script>

<template>
    <Head title="My Orders" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4 md:p-6"
        >
            <!-- Header -->
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="flex items-center gap-3 text-3xl font-bold">
                        My Orders
                    </h1>
                    <p class="mt-1 text-muted-foreground">
                        Track and manage your orders
                    </p>
                </div>
                <LinkButton
                    :href="homepageRoutes.index().url"
                    mode="back"
                    label="Continue Shopping"
                />
            </div>

            <!-- Empty State -->
            <div
                v-if="orders.data.length === 0"
                class="flex flex-col items-center justify-center px-4 py-16"
            >
                <div class="mb-6 rounded-full bg-muted p-8">
                    <Package class="h-16 w-16 text-muted-foreground" />
                </div>
                <h3 class="mb-2 text-2xl font-semibold">No orders yet</h3>
                <p class="mb-6 max-w-md text-center text-muted-foreground">
                    You haven't placed any orders yet. Start shopping to see
                    your orders here!
                </p>
                <LinkButton
                    :href="homepageRoutes.index().url"
                    label="Start Shopping"
                    size="lg"
                />
            </div>

            <!-- Orders List -->
            <div v-else class="space-y-4">
                <Card
                    v-for="order in orders.data"
                    :key="order.id"
                    class="overflow-hidden border-2 transition-all duration-200 hover:border-primary/50"
                >
                    <CardHeader class="border-b bg-muted/30">
                        <div
                            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                        >
                            <div class="space-y-1">
                                <CardTitle class="text-lg">
                                    Order #{{
                                        order.id.toString().padStart(4, '0')
                                    }}
                                </CardTitle>
                                <div
                                    class="flex flex-wrap items-center gap-2 text-sm text-muted-foreground"
                                >
                                    <div class="flex items-center gap-1">
                                        <Calendar class="h-4 w-4" />
                                        {{ formatDate(order.created_at) }}
                                    </div>
                                    <span>•</span>
                                    <div class="flex items-center gap-1">
                                        <Package class="h-4 w-4" />
                                        {{ order.order_items?.length || 0 }}
                                        {{
                                            order.order_items?.length === 1
                                                ? 'item'
                                                : 'items'
                                        }}
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <Badge
                                    :class="getStatusBadge(order.status).class"
                                    class="border"
                                >
                                    {{ getStatusBadge(order.status).label }}
                                </Badge>
                            </div>
                        </div>
                    </CardHeader>

                    <CardContent class="p-4">
                        <div class="space-y-4">
                            <!-- Order Items Preview -->
                            <div class="space-y-2">
                                <div
                                    v-for="item in order.order_items?.slice(
                                        0,
                                        3,
                                    )"
                                    :key="item.id"
                                    class="flex items-center gap-3 text-sm"
                                >
                                    <div
                                        class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-md border bg-muted"
                                    >
                                        <img
                                            v-if="item.item?.item_image_1"
                                            :src="`/storage/${item.item.item_image_1}`"
                                            :alt="item.item.item_name"
                                            class="h-full w-full object-contain p-1"
                                        />
                                        <Package
                                            v-else
                                            class="h-6 w-6 text-muted-foreground"
                                        />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="line-clamp-1 font-medium">
                                            {{ item.item?.item_name || 'Item' }}
                                        </p>
                                        <p class="text-muted-foreground">
                                            Qty: {{ item.quantity }} ×
                                            {{
                                                formatCurrency(
                                                    Number(item.unit_price),
                                                )
                                            }}
                                        </p>
                                    </div>
                                    <p class="font-semibold">
                                        {{
                                            formatCurrency(
                                                Number(item.unit_price) *
                                                    item.quantity,
                                            )
                                        }}
                                    </p>
                                </div>
                                <p
                                    v-if="
                                        order.order_items &&
                                        order.order_items.length > 3
                                    "
                                    class="text-sm text-muted-foreground"
                                >
                                    +{{ order.order_items.length - 3 }}
                                    more
                                    {{
                                        order.order_items.length - 3 === 1
                                            ? 'item'
                                            : 'items'
                                    }}
                                </p>
                            </div>

                            <!-- Order Details -->
                            <div
                                class="grid grid-cols-1 gap-3 border-t pt-4 sm:grid-cols-2"
                            >
                                <div class="flex items-start gap-2">
                                    <CreditCard
                                        class="mt-0.5 h-4 w-4 text-muted-foreground"
                                    />
                                    <div>
                                        <p
                                            class="text-xs text-muted-foreground"
                                        >
                                            Payment Method
                                        </p>
                                        <p class="font-medium capitalize">
                                            {{
                                                order.payment_method.replace(
                                                    '_',
                                                    ' ',
                                                )
                                            }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-2">
                                    <Truck
                                        class="mt-0.5 h-4 w-4 text-muted-foreground"
                                    />
                                    <div>
                                        <p
                                            class="text-xs text-muted-foreground"
                                        >
                                            Delivery Method
                                        </p>
                                        <p class="font-medium capitalize">
                                            {{ order.delivery_method }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Total and Actions -->
                            <div
                                class="flex items-center justify-between border-t pt-4"
                            >
                                <div>
                                    <p class="text-sm text-muted-foreground">
                                        Total Amount
                                    </p>
                                    <p class="text-2xl font-bold text-primary">
                                        {{
                                            formatCurrency(
                                                Number(order.total_amount),
                                            )
                                        }}
                                    </p>
                                </div>
                                <Button @click="viewOrder(order.id)" size="lg">
                                    <Eye class="mr-2 h-4 w-4" />
                                    View Details
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
