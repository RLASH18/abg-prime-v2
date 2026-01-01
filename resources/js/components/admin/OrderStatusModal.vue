<script setup lang="ts">
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import ordersRoutes from '@/routes/admin/orders';
import { type Order } from '@/types/admin';
import { useForm } from '@inertiajs/vue3';
import {
    Box,
    CheckCircle2,
    Clock,
    CreditCard,
    MapPin,
    Truck,
    XCircle,
} from 'lucide-vue-next';
import { computed } from 'vue';

interface Props {
    open: boolean;
    order: Order | null;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    'update:open': [value: boolean];
}>();

const statusSteps = [
    { id: 'pending', label: 'Pending', icon: Clock },
    { id: 'confirmed', label: 'Confirmed', icon: CheckCircle2 },
    { id: 'assembled', label: 'Assembled', icon: Box },
    { id: 'shipped', label: 'Shipped', icon: Truck },
    { id: 'delivered', label: 'Delivered', icon: MapPin },
    { id: 'paid', label: 'Paid', icon: CreditCard },
];

const currentStatusIndex = computed(() => {
    if (!props.order) return -1;
    return statusSteps.findIndex((step) => step.id === props.order?.status);
});

const nextStatus = computed(() => {
    if (!props.order || currentStatusIndex.value === -1) return null;
    const nextIndex = currentStatusIndex.value + 1;
    if (nextIndex >= statusSteps.length) return null;
    return statusSteps[nextIndex];
});

const canCancel = computed(() => {
    if (!props.order) return false;
    return props.order.status === 'pending';
});

const form = useForm({
    status: '',
});

const updateToNextStatus = () => {
    if (!props.order || !nextStatus.value) return;

    form.status = nextStatus.value.id;
    form.patch(ordersRoutes.updateStatus(props.order.id).url, {
        onSuccess: () => {
            emit('update:open', false);
        },
    });
};

const cancelOrder = () => {
    if (!props.order) return;

    form.status = 'cancelled';
    form.patch(ordersRoutes.updateStatus(props.order.id).url, {
        onSuccess: () => {
            emit('update:open', false);
        },
    });
};

const getStatusColor = (status: string) => {
    switch (status) {
        case 'pending':
            return 'bg-yellow-100 text-yellow-800 border-yellow-200';
        case 'confirmed':
            return 'bg-blue-100 text-blue-800 border-blue-200';
        case 'assembled':
            return 'bg-indigo-100 text-indigo-800 border-indigo-200';
        case 'shipped':
            return 'bg-purple-100 text-purple-800 border-purple-200';
        case 'delivered':
            return 'bg-green-100 text-green-800 border-green-200';
        case 'paid':
            return 'bg-emerald-100 text-emerald-800 border-emerald-200';
        case 'cancelled':
            return 'bg-red-100 text-red-800 border-red-200';
        default:
            return 'bg-gray-100 text-gray-800 border-gray-200';
    }
};
</script>

<template>
    <Dialog :open="open" @update:open="$emit('update:open', $event)">
        <DialogContent class="sm:max-w-lg">
            <DialogHeader>
                <DialogTitle>Update Order #{{ order?.id }}</DialogTitle>
            </DialogHeader>

            <div class="py-4" v-if="order">
                <!-- Current Status Display -->
                <div class="mb-8 flex items-center justify-between px-2">
                    <div class="text-sm font-medium text-muted-foreground">
                        Current Status:
                        <span
                            class="ml-2 rounded-full px-2.5 py-0.5 text-xs font-semibold tracking-wide uppercase"
                            :class="getStatusColor(order.status)"
                        >
                            {{ order.status }}
                        </span>
                    </div>
                </div>

                <!-- Status Progress Tracker (Read Only) -->
                <div class="relative mb-8">
                    <div
                        class="absolute top-4 left-[8%] -z-10 h-0.5 w-[90%] bg-gray-200"
                    ></div>
                    <div class="relative z-0 flex justify-between">
                        <div
                            v-for="(step, index) in statusSteps"
                            :key="step.id"
                            class="group flex flex-col items-center"
                        >
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-full border-2 bg-background transition-all duration-200"
                                :class="[
                                    index <= currentStatusIndex
                                        ? 'border-primary bg-primary/5 text-primary'
                                        : 'border-gray-300 text-gray-400',
                                ]"
                            >
                                <component :is="step.icon" class="h-4 w-4" />
                            </div>

                            <span
                                class="mt-2 text-xs font-medium transition-colors duration-200"
                                :class="[
                                    index <= currentStatusIndex
                                        ? 'text-primary'
                                        : 'text-gray-500',
                                ]"
                            >
                                {{ step.label }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Status Update Action -->
                <div
                    v-if="nextStatus"
                    class="rounded-lg border bg-muted/30 p-4"
                >
                    <h4 class="mb-3 text-sm font-medium text-foreground">
                        Update Status
                    </h4>
                    <div class="flex gap-3">
                        <button
                            @click="updateToNextStatus"
                            :disabled="form.processing"
                            class="inline-flex h-10 flex-1 items-center justify-center rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground ring-offset-background transition-colors hover:bg-primary/90 focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:pointer-events-none disabled:opacity-50"
                        >
                            <component
                                :is="nextStatus.icon"
                                class="mr-2 h-4 w-4"
                            />
                            Mark as {{ nextStatus.label }}
                        </button>
                        <button
                            v-if="canCancel"
                            @click="cancelOrder"
                            :disabled="form.processing"
                            class="inline-flex h-10 items-center justify-center rounded-md border border-red-200 bg-white px-4 text-sm font-medium text-red-600 ring-offset-background transition-colors hover:bg-red-50 focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:pointer-events-none disabled:opacity-50"
                        >
                            <XCircle class="mr-2 h-4 w-4" />
                            Cancel Order
                        </button>
                    </div>
                </div>

                <!-- Order Completed Message -->
                <div
                    v-else
                    class="rounded-lg border border-green-200 bg-green-50/50 p-4"
                >
                    <p class="text-sm font-medium text-green-900">
                        This order has been completed and paid.
                    </p>
                </div>
            </div>

            <DialogFooter>
                <button
                    @click="$emit('update:open', false)"
                    class="inline-flex h-10 items-center justify-center rounded-md border border-input bg-background px-4 py-2 text-sm font-medium ring-offset-background transition-colors hover:bg-accent hover:text-accent-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:pointer-events-none disabled:opacity-50"
                >
                    Close
                </button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
