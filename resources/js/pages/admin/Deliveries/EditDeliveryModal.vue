<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import BaseModal from '@/components/BaseModal.vue';
import { type Delivery } from '@/types/admin';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import Select from '@/components/Select.vue';
import { Button } from '@/components/ui/button';
import InputError from '@/components/InputError.vue';
import { Spinner } from '@/components/ui/spinner';
import deliveriesRoutes from '@/routes/admin/deliveries';
import { ref, watch } from 'vue';

interface Props {
    open: boolean;
    delivery: Delivery | null;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    'update:open': [value: boolean];
}>();

const statusOptions = [
    { value: 'scheduled', label: 'Scheduled' },
    { value: 'rescheduled', label: 'Rescheduled' },
    { value: 'in_transit', label: 'In Transit' },
    { value: 'delivered', label: 'Delivered' },
    { value: 'failed', label: 'Failed' },
];

// Reactive form data
const formData = ref({
    status: props.delivery?.status || 'scheduled',
    driver_name: props.delivery?.driver_name || '',
    scheduled_date: props.delivery?.scheduled_date || '',
    remarks: props.delivery?.remarks || '',
});

// Watch for delivery changes to update form
watch(() => props.delivery, (newDelivery) => {
    if (newDelivery) {
        formData.value = {
            status: newDelivery.status,
            driver_name: newDelivery.driver_name || '',
            scheduled_date: newDelivery.scheduled_date,
            remarks: newDelivery.remarks || '',
        };
    }
}, { immediate: true });

const close = () => {
    emit('update:open', false);
};
</script>

<template>
    <BaseModal :open="open" @update:open="emit('update:open', $event)" :title="`Edit Delivery #${delivery?.id}`"
        size="lg">
        <template #default>
            <div v-if="delivery">
                <!-- Order Info -->
                <div class="bg-muted/30 p-3 rounded-lg border mb-4">
                    <div class="text-sm">
                        <span class="font-medium text-muted-foreground">Order ID:</span>
                        <span class="ml-2 font-semibold">{{ delivery.order_id }}</span>
                    </div>
                    <div class="text-sm mt-1" v-if="delivery.order?.user?.name">
                        <span class="font-medium text-muted-foreground">Customer:</span>
                        <span class="ml-2">{{ delivery.order.user.name }}</span>
                    </div>
                </div>

                <!-- Actual Delivery Date (Read Only) -->
                <div v-if="delivery.actual_delivery_date"
                    class="bg-green-50/50 p-3 rounded-lg border border-green-200 mb-4">
                    <div class="text-sm">
                        <span class="font-medium text-green-900">Delivered on:</span>
                        <span class="ml-2 font-semibold text-green-700">{{ delivery.actual_delivery_date }}</span>
                    </div>
                </div>

                <Form :action="deliveriesRoutes.updateStatus(delivery.id)" method="patch"
                    v-slot="{ errors, processing }" @success="close" class="space-y-4">

                    <!-- Status -->
                    <div class="space-y-2">
                        <Label for="status">Status<span class="text-red-500">*</span></Label>
                        <Select name="status" :options="statusOptions" placeholder="Select status"
                            v-model="formData.status" required />
                        <InputError :message="errors.status" />
                    </div>

                    <!-- Driver Name -->
                    <div class="space-y-2">
                        <Label for="driver_name">Driver Name</Label>
                        <Input id="driver_name" name="driver_name" v-model="formData.driver_name"
                            placeholder="Enter driver name" />
                        <InputError :message="errors.driver_name" />
                    </div>

                    <!-- Scheduled Date -->
                    <div class="space-y-2">
                        <Label for="scheduled_date">Scheduled Date<span class="text-red-500">*</span></Label>
                        <Input id="scheduled_date" name="scheduled_date" type="date" v-model="formData.scheduled_date"
                            required />
                        <InputError :message="errors.scheduled_date" />
                    </div>

                    <!-- Remarks -->
                    <div class="space-y-2">
                        <Label for="remarks">Remarks</Label>
                        <Textarea id="remarks" name="remarks" v-model="formData.remarks"
                            placeholder="Add any notes or remarks..." rows="3" />
                        <InputError :message="errors.remarks" />
                    </div>

                    <!-- Footer Buttons -->
                    <div class="flex justify-end gap-2 pt-4">
                        <Button variant="outline" type="button" @click="close">Cancel</Button>
                        <Button type="submit" :disabled="processing">
                            <Spinner v-if="processing" />
                            Update Delivery
                        </Button>
                    </div>
                </Form>
            </div>
        </template>
    </BaseModal>
</template>
