<script setup lang="ts">
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle
} from '@/components/ui/dialog';
import { type Order } from '@/types/admin';
import { Truck, CreditCard, Clock, CheckCircle2, Box, MapPin, XCircle } from 'lucide-vue-next';
import { useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import ordersRoutes from '@/routes/admin/orders';

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
    return statusSteps.findIndex(step => step.id === props.order?.status);
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
}

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
    <Dialog :open="open" @update:open="$emit('update:open', $event)">
        <DialogContent class="sm:max-w-lg">
            <DialogHeader>
                <DialogTitle>Update Order #{{ order?.id }}</DialogTitle>
            </DialogHeader>

            <div class="py-4" v-if="order">
                <!-- Current Status Display -->
                <div class="flex items-center justify-between mb-8 px-2">
                    <div class="text-sm font-medium text-muted-foreground">
                        Current Status:
                        <span class="ml-2 px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wide"
                            :class="getStatusColor(order.status)">
                            {{ order.status }}
                        </span>
                    </div>
                </div>

                <!-- Status Progress Tracker (Read Only) -->
                <div class="relative mb-8">
                    <div class="absolute top-4 left-[8%] w-[90%] h-0.5 bg-gray-200 -z-10"></div>
                    <div class="flex justify-between relative z-0">
                        <div v-for="(step, index) in statusSteps" :key="step.id"
                            class="flex flex-col items-center group">

                            <div class="w-8 h-8 rounded-full flex items-center justify-center border-2 transition-all duration-200 bg-background"
                                :class="[
                                    index <= currentStatusIndex
                                        ? 'border-primary text-primary bg-primary/5'
                                        : 'border-gray-300 text-gray-400'
                                ]">
                                <component :is="step.icon" class="w-4 h-4" />
                            </div>

                            <span class="mt-2 text-xs font-medium transition-colors duration-200" :class="[
                                index <= currentStatusIndex ? 'text-primary' : 'text-gray-500'
                            ]">
                                {{ step.label }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Status Update Action -->
                <div v-if="nextStatus" class="bg-muted/30 p-4 rounded-lg border">
                    <h4 class="text-sm font-medium text-foreground mb-3">Update Status</h4>
                    <div class="flex gap-3">
                        <button @click="updateToNextStatus" :disabled="form.processing"
                            class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 flex-1">
                            <component :is="nextStatus.icon" class="w-4 h-4 mr-2" />
                            Mark as {{ nextStatus.label }}
                        </button>
                        <button v-if="canCancel" @click="cancelOrder" :disabled="form.processing"
                            class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-red-200 bg-white text-red-600 hover:bg-red-50 h-10 px-4">
                            <XCircle class="w-4 h-4 mr-2" />
                            Cancel Order
                        </button>
                    </div>
                </div>

                <!-- Order Completed Message -->
                <div v-else class="bg-green-50/50 p-4 rounded-lg border border-green-200">
                    <p class="text-sm font-medium text-green-900">This order has been completed and paid.</p>
                </div>
            </div>

            <DialogFooter>
                <button @click="$emit('update:open', false)"
                    class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2">
                    Close
                </button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
