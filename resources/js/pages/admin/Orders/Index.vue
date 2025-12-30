<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type FilterConfig, type PaginationData, type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { type Order } from '@/types/admin';
import { Package, User } from 'lucide-vue-next';
import Pagination from '@/components/Pagination.vue';
import { useFilters } from '@/composables/useFilters';
import Filters from '@/components/Filters.vue';
import OrderStatusModal from '@/components/admin/OrderStatusModal.vue';
import { ref } from 'vue';
import ordersRoutes from '@/routes/admin/orders';
import { useFormatters } from '@/composables/useFormatters';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Orders',
        href: ordersRoutes.index().url,
    },
];

interface Props {
    orders: PaginationData & {
        data: Order[];
    };
    filters: {
        search?: string;
        status?: string;
        payment_method?: string;
        delivery_method?: string;
    };
}

const props = defineProps<Props>();
const { formatCurrency, formatDate } = useFormatters();

// Modal state
const showStatusModal = ref(false);
const selectedOrder = ref<Order | null>(null);

const openStatusModal = (order: Order) => {
    selectedOrder.value = order;
    showStatusModal.value = true;
};

// Initialize filters
const { filters, updateFilter, resetFilters } = useFilters(
    ordersRoutes.index().url,
    {
        search: props.filters.search || '',
        status: props.filters.status || '',
        payment_method: props.filters.payment_method || '',
        delivery_method: props.filters.delivery_method || '',
    }
);

// Filter configurations
const filterConfigs: FilterConfig[] = [
    {
        label: 'Status',
        key: 'status',
        options: [
            { label: 'Pending', value: 'pending' },
            { label: 'Confirmed', value: 'confirmed' },
            { label: 'Assembled', value: 'assembled' },
            { label: 'Shipped', value: 'shipped' },
            { label: 'Delivered', value: 'delivered' },
            { label: 'Paid', value: 'paid' },
            { label: 'Cancelled', value: 'cancelled' },
        ],
        placeholder: 'All Statuses',
    },
    {
        label: 'Payment Method',
        key: 'payment_method',
        options: [
            { label: 'Cash', value: 'cash' },
            { label: 'GCash', value: 'gcash' },
            { label: 'Bank Transfer', value: 'bank_transfer' },
        ],
        placeholder: 'All Payments',
    },
    {
        label: 'Delivery Method',
        key: 'delivery_method',
        options: [
            { label: 'Pickup', value: 'pickup' },
            { label: 'Delivery', value: 'delivery' },
        ],
        placeholder: 'All Methods',
    },
];

const getStatusColor = (status: string) => {
    switch (status) {
        case 'pending': return 'bg-yellow-100 text-yellow-800 border-yellow-200';
        case 'confirmed': return 'bg-blue-100 text-blue-800 border-blue-200';
        case 'assembled': return 'bg-indigo-100 text-indigo-800 border-indigo-200';
        case 'shipped': return 'bg-purple-100 text-purple-800 border-purple-200';
        case 'delivered': return 'bg-green-100 text-green-800 border-green-200';
        case 'paid': return 'bg-emerald-100 text-emerald-800 border-emerald-200';
        case 'cancelled': return 'bg-red-100 text-red-800 border-red-200';
        default: return 'bg-gray-100 text-gray-800 border-gray-200';
    }
};
</script>

<template>

    <Head title="Orders" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h1 class="text-2xl font-bold">Orders</h1>
                    <p class="text-sm text-muted-foreground">Manage and track customer orders</p>
                </div>
            </div>

            <!-- Filters -->
            <Filters :search-value="filters.search as string" :filters="filterConfigs"
                search-placeholder="Search by Order ID or Customer..."
                @update:search="(value) => updateFilter('search', value)"
                @update:filter="(key, value) => updateFilter(key, value, true)" @reset="resetFilters" />

            <!-- Orders Grid -->
            <div v-if="orders.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div v-for="order in orders.data" :key="order.id"
                    class="bg-card rounded-lg border shadow-sm hover:shadow-md transition-shadow duration-200 flex flex-col">

                    <!-- Card Header -->
                    <div class="p-4 border-b bg-muted/5 flex justify-between items-start">
                        <div>
                            <div class="font-mono text-sm font-medium">Order #{{ order.id.toString().padStart(4, '0') }}
                            </div>
                            <div class="text-xs text-muted-foreground mt-1">
                                {{ formatDate(order.created_at) }}
                            </div>
                        </div>
                        <span
                            :class="['px-2.5 py-0.5 rounded-full text-xs font-medium border capitalize', getStatusColor(order.status)]">
                            {{ order.status }}
                        </span>
                    </div>

                    <!-- Card Body -->
                    <div class="p-4 flex-1 space-y-3">
                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-primary/10 rounded-full">
                                <User class="w-4 h-4 text-primary" />
                            </div>
                            <div>
                                <div class="text-sm font-medium">{{ order.user?.name || 'Unknown User' }}</div>
                                <div class="text-xs text-muted-foreground">{{ order.user?.email }}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-sm mt-3">
                            <div class="flex flex-col gap-1">
                                <span class="text-xs text-muted-foreground">Amount</span>
                                <span class="font-medium">{{ formatCurrency(order.total_amount) }}</span>
                            </div>
                            <div class="flex flex-col gap-1">
                                <span class="text-xs text-muted-foreground">Payment</span>
                                <span class="capitalize">{{ order.payment_method.replace('_', ' ') }}</span>
                            </div>
                            <div class="flex flex-col gap-1">
                                <span class="text-xs text-muted-foreground">Delivery</span>
                                <span class="capitalize">{{ order.delivery_method.replace('_', ' ') }}</span>
                            </div>
                            <div class="flex flex-col gap-1">
                                <span class="text-xs text-muted-foreground">Items</span>
                                <span class="font-medium">{{ order.order_items?.length || 0 }} items</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card Footer -->
                    <div class="p-4 border-t bg-muted/5 flex justify-end gap-2">
                        <button @click="router.visit(ordersRoutes.show(order.id).url)"
                            class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-3">
                            View Details
                        </button>
                        <button @click="openStatusModal(order)"
                            class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-9 px-3">
                            Update Status
                        </button>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else
                class="flex flex-col items-center justify-center py-12 text-center border rounded-lg bg-muted/5 border-dashed">
                <Package class="w-12 h-12 text-muted-foreground/50 mb-4" />
                <h3 class="text-lg font-semibold">No orders found</h3>
                <p class="text-muted-foreground text-sm max-w-sm mt-2">
                    Try adjusting your search or filters to find what you're looking for.
                </p>
            </div>

            <!-- Pagination -->
            <div v-if="orders.total > 9">
                <Pagination :pagination="orders" />
            </div>
        </div>
    </AppLayout>

    <!-- Status Update Modal -->
    <OrderStatusModal v-model:open="showStatusModal" :order="selectedOrder" />
</template>
