<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import Select from '@/components/Select.vue';
import InputError from '@/components/InputError.vue';
import { Spinner } from '@/components/ui/spinner';
import damagedItemsRoutes from '@/routes/admin/damaged-items';
import type { InventoryItem } from '@/types/admin';

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

const close = () => {
    emit('update:open', false);
};
</script>

<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>Mark Item as Damaged</DialogTitle>
                <DialogDescription>Record damaged inventory and reduce stock.</DialogDescription>
            </DialogHeader>

            <Form :action="damagedItemsRoutes.store()" v-slot="{ errors, processing }" @success="close"
                class="space-y-4">
                <!-- Hidden item_id field -->
                <input type="hidden" name="item_id" :value="item?.id" />

                <!-- Quantity Damaged -->
                <div class="space-y-2">
                    <Label for="quantity">Quantity Damaged<span class="text-red-500">*</span></Label>
                    <Input id="quantity" name="quantity" type="number" placeholder="1" min="1" :max="item?.quantity"
                        class="[&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                        required />
                    <InputError :message="errors.quantity" />
                </div>

                <!-- Remarks -->
                <div class="space-y-2">
                    <Label for="remarks">Remarks</Label>
                    <Textarea id="remarks" name="remarks" placeholder="Describe the damage (optional)..." />
                    <InputError :message="errors.remarks" />
                </div>

                <!-- Discount -->
                <div class="space-y-2">
                    <Label for="discount">Discount (if reselling)</Label>
                    <Input id="discount" name="discount" type="number" step="0.01" min="0" placeholder="0.00"
                        class="[&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" />
                    <InputError :message="errors.discount" />
                </div>

                <!-- Status -->
                <div class="space-y-2">
                    <Label for="status">Status<span class="text-red-500">*</span></Label>
                    <Select name="status" :options="statusOptions" placeholder="Select status" required />
                    <InputError :message="errors.status" />
                </div>

                <!-- Footer Buttons -->
                <div class="flex justify-end gap-2 pt-4">
                    <Button variant="outline" type="button" @click="close">Cancel</Button>
                    <Button type="submit" :disabled="processing">
                        <Spinner v-if="processing" />
                        Mark as Damaged
                    </Button>
                </div>
            </Form>
        </DialogContent>
    </Dialog>
</template>
