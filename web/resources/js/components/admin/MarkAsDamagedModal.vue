<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import Select from '@/components/Select.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { Textarea } from '@/components/ui/textarea';
import damagedItemsRoutes from '@/routes/admin/damaged-items';
import type { InventoryItem } from '@/types/admin';
import { Form } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface Props {
    open: boolean;
    item: InventoryItem | null;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    'update:open': [value: boolean];
}>();

const statusOptions = [
    { value: 'resellable', label: 'Resellable' },
    { value: 'disposed', label: 'Disposed' },
];

const discountAmount = ref(0);

const discountPercentage = computed(() => {
    if (!props.item?.unit_price || props.item.unit_price <= 0) return 0;
    return Math.min(
        100,
        Math.round((discountAmount.value / props.item.unit_price) * 100),
    );
});

const close = () => {
    discountAmount.value = 0;
    emit('update:open', false);
};
</script>

<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>Mark Item as Damaged</DialogTitle>
                <DialogDescription
                    >Record damaged inventory and reduce
                    stock.</DialogDescription
                >
            </DialogHeader>

            <Form
                :action="damagedItemsRoutes.store()"
                v-slot="{ errors, processing }"
                @success="close"
                class="space-y-4"
            >
                <!-- Hidden item_id field -->
                <input type="hidden" name="item_id" :value="item?.id" />

                <!-- Quantity Damaged -->
                <div class="space-y-2">
                    <Label for="quantity"
                        >Quantity Damaged<span class="text-red-500"
                            >*</span
                        ></Label
                    >
                    <Input
                        id="quantity"
                        name="quantity"
                        type="number"
                        placeholder="1"
                        min="1"
                        :max="item?.quantity"
                        class="[&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none"
                        required
                    />
                    <InputError :message="errors.quantity" />
                </div>

                <!-- Remarks -->
                <div class="space-y-2">
                    <Label for="remarks">Remarks</Label>
                    <Textarea
                        id="remarks"
                        name="remarks"
                        placeholder="Describe the damage (optional)..."
                    />
                    <InputError :message="errors.remarks" />
                </div>

                <!-- Discount Amount -->
                <div class="space-y-2">
                    <Label for="discount_amount"
                        >Discount Amount (if reselling)</Label
                    >
                    <Input
                        id="discount_amount"
                        name="discount_amount"
                        type="number"
                        step="0.01"
                        min="0"
                        :max="props.item?.unit_price"
                        placeholder="0.00"
                        v-model.number="discountAmount"
                        class="[&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none"
                    />
                    <InputError :message="errors.discount_amount" />
                </div>

                <!-- Discount Percentage (Calculated) -->
                <div class="space-y-2">
                    <Label for="discount_percentage">Discount Percentage</Label>
                    <Input
                        id="discount_percentage"
                        type="text"
                        :value="`${discountPercentage}%`"
                        disabled
                        class="cursor-not-allowed bg-muted"
                    />
                </div>

                <!-- Status -->
                <div class="space-y-2">
                    <Label for="status"
                        >Status<span class="text-red-500">*</span></Label
                    >
                    <Select
                        name="status"
                        :options="statusOptions"
                        placeholder="Select status"
                        required
                    />
                    <InputError :message="errors.status" />
                </div>

                <!-- Footer Buttons -->
                <div class="flex justify-end gap-2 pt-4">
                    <Button variant="outline" type="button" @click="close"
                        >Cancel</Button
                    >
                    <Button type="submit" :disabled="processing">
                        <Spinner v-if="processing" />
                        Mark as Damaged
                    </Button>
                </div>
            </Form>
        </DialogContent>
    </Dialog>
</template>
