<script setup lang="ts">
import LinkButton from '@/components/LinkButton.vue';
import InputError from '@/components/InputError.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import itemsRoutes from '@/routes/admin/items';
import { type BreadcrumbItem } from '@/types';
import { Form, Head, useForm } from '@inertiajs/vue3';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import Select from '@/components/Select.vue';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import { Spinner } from '@/components/ui/spinner';
import { X, Upload } from 'lucide-vue-next';
import { useImagePreviews } from '@/composables/useImagePreviews';
import { type InventoryItem, type Supplier } from '@/types/admin';

const props = defineProps<{
    item: InventoryItem;
    suppliers: Supplier[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Items',
        href: itemsRoutes.index().url
    },
    {
        title: 'Edit Item',
        href: itemsRoutes.edit(props.item.id).url
    },
];

const categories = [
    'Hand Tools',
    'Power Tools',
    'Construction Materials',
    'Locks and Security',
    'Plumbing',
    'Electrical',
    'Paint and Finishes',
    'Chemicals',
];

const { imagePreviews, handleImageSelect, removeImage } = useImagePreviews(3);

// Populate the preview array with existing images (if any)
imagePreviews.value = [
    props.item.item_image_1 ? `/storage/${props.item.item_image_1}` : null,
    props.item.item_image_2 ? `/storage/${props.item.item_image_2}` : null,
    props.item.item_image_3 ? `/storage/${props.item.item_image_3}` : null,
];

const form = useForm({
    item_name: props.item.item_name,
    brand_name: props.item.brand_name,
    category: props.item.category,
    supplier_id: props.item.supplier_id?.toString() ?? '',
    unit_price: props.item.unit_price,
    quantity: props.item.quantity,
    restock_threshold: props.item.restock_threshold,
    description: props.item.description ?? '',
    item_image_1: null,
    item_image_2: null,
    item_image_3: null,
});
</script>

<template>

    <Head title="Inventory - Edit Item" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <!-- Header -->
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h1 class="text-2xl font-bold">Edit Inventory Item</h1>
                    <p class="text-sm text-muted-foreground">
                        Update the details for this item.
                    </p>
                </div>
                <LinkButton :href="itemsRoutes.index().url" mode="back" label="Back to list" />
            </div>

            <!-- Form -->
            <Form :action="itemsRoutes.update(props.item.id).url" method="patch" v-slot="{ errors, processing }"
                class="grid gap-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Item Name -->
                    <div class="space-y-2">
                        <Label for="item_name">Item Name<span class="text-red-500">*</span></Label>
                        <Input id="item_name" name="item_name" placeholder="Enter item name" v-model="form.item_name"
                            required />
                        <InputError :message="errors.item_name" />
                    </div>

                    <!-- Brand Name -->
                    <div class="space-y-2">
                        <Label for="brand_name">Brand Name<span class="text-red-500">*</span></Label>
                        <Input id="brand_name" name="brand_name" placeholder="Enter brand name"
                            v-model="form.brand_name" />
                        <InputError :message="errors.brand_name" />
                    </div>

                    <!-- Category -->
                    <div class="space-y-2">
                        <Label for="category">Category<span class="text-red-500">*</span></Label>
                        <Select name="category" :options="categories" placeholder="Select a category"
                            v-model="form.category" required />
                        <InputError :message="errors.category" />
                    </div>

                    <!-- Supplier -->
                    <div class="space-y-2">
                        <Label for="supplier_id">Supplier</Label>
                        <Select v-if="suppliers.length > 0" name="supplier_id"
                            :options="suppliers.map(s => ({ value: s.id.toString(), label: s.supplier_name }))"
                            placeholder="Select a supplier (optional)" v-model="form.supplier_id" />
                        <div v-else
                            class="flex items-center gap-2 p-3 text-sm text-muted-foreground bg-muted rounded-md border border-border">
                            <span>No suppliers available. Please add suppliers first.</span>
                        </div>
                        <InputError :message="errors.supplier_id" />
                    </div>

                    <!-- Unit Price -->
                    <div class="space-y-2">
                        <Label for="unit_price">Unit Price<span class="text-red-500">*</span></Label>
                        <Input id="unit_price" name="unit_price" type="number" step="0.01" placeholder="0.00"
                            class="[&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                            v-model="form.unit_price" required />
                        <InputError :message="errors.unit_price" />
                    </div>

                    <!-- Quantity -->
                    <div class="space-y-2">
                        <Label for="quantity">Quantity<span class="text-red-500">*</span></Label>
                        <Input id="quantity" name="quantity" type="number" placeholder="0"
                            class="[&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                            v-model="form.quantity" required />
                        <InputError :message="errors.quantity" />
                    </div>

                    <!-- Restock Threshold -->
                    <div class="space-y-2">
                        <Label for="restock_threshold">Restock Threshold<span class="text-red-500">*</span></Label>
                        <Input id="restock_threshold" name="restock_threshold" type="number" placeholder="10"
                            class="[&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                            v-model="form.restock_threshold" required />
                        <InputError :message="errors.restock_threshold" />
                    </div>
                </div>

                <!-- Description -->
                <div class="space-y-2">
                    <Label for="description">Description</Label>
                    <Textarea id="description" name="description" placeholder="Enter item description"
                        v-model="form.description" />
                    <InputError :message="errors.description" />
                </div>

                <!-- Images with Preview (same UI as Create.vue) -->
                <div class="space-y-2">
                    <Label>Product Images</Label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Image 1 -->
                        <div class="space-y-2">
                            <div class="relative group">
                                <div v-if="imagePreviews[0]"
                                    class="relative aspect-square rounded-lg border-2 border-border overflow-hidden bg-muted">
                                    <img :src="imagePreviews[0]" alt="Preview 1" class="w-full h-full object-cover" />
                                    <button type="button" @click="removeImage(0, 'item_image_1')"
                                        class="absolute top-2 right-2 p-1.5 bg-destructive text-destructive-foreground rounded-full shadow-lg hover:bg-destructive/90 transition-colors"
                                        aria-label="Remove image 1">
                                        <X :size="16" />
                                    </button>
                                </div>
                                <label v-else for="item_image_1"
                                    class="flex flex-col items-center justify-center aspect-square rounded-lg border-2 border-dashed border-border hover:border-primary hover:bg-accent cursor-pointer transition-colors">
                                    <Upload :size="32" class="text-muted-foreground mb-2" />
                                    <span class="text-sm text-muted-foreground">Upload Image 1</span>
                                    <span class="text-xs text-muted-foreground mt-1">Click to browse</span>
                                </label>
                                <Input id="item_image_1" name="item_image_1" type="file" accept="image/*" class="hidden"
                                    @change="(e: Event) => handleImageSelect(e, 0)" />
                            </div>
                            <InputError :message="errors.item_image_1" />
                        </div>

                        <!-- Image 2 -->
                        <div class="space-y-2">
                            <div class="relative group">
                                <div v-if="imagePreviews[1]"
                                    class="relative aspect-square rounded-lg border-2 border-border overflow-hidden bg-muted">
                                    <img :src="imagePreviews[1]" alt="Preview 2" class="w-full h-full object-cover" />
                                    <button type="button" @click="removeImage(1, 'item_image_2')"
                                        class="absolute top-2 right-2 p-1.5 bg-destructive text-destructive-foreground rounded-full shadow-lg hover:bg-destructive/90 transition-colors"
                                        aria-label="Remove image 2">
                                        <X :size="16" />
                                    </button>
                                </div>
                                <label v-else for="item_image_2"
                                    class="flex flex-col items-center justify-center aspect-square rounded-lg border-2 border-dashed border-border hover:border-primary hover:bg-accent cursor-pointer transition-colors">
                                    <Upload :size="32" class="text-muted-foreground mb-2" />
                                    <span class="text-sm text-muted-foreground">Upload Image 2 (optional)</span>
                                    <span class="text-xs text-muted-foreground mt-1">Click to browse</span>
                                </label>
                                <Input id="item_image_2" name="item_image_2" type="file" accept="image/*" class="hidden"
                                    @change="(e: Event) => handleImageSelect(e, 1)" />
                            </div>
                            <InputError :message="errors.item_image_2" />
                        </div>

                        <!-- Image 3 -->
                        <div class="space-y-2">
                            <div class="relative group">
                                <div v-if="imagePreviews[2]"
                                    class="relative aspect-square rounded-lg border-2 border-border overflow-hidden bg-muted">
                                    <img :src="imagePreviews[2]" alt="Preview 3" class="w-full h-full object-cover" />
                                    <button type="button" @click="removeImage(2, 'item_image_3')"
                                        class="absolute top-2 right-2 p-1.5 bg-destructive text-destructive-foreground rounded-full shadow-lg hover:bg-destructive/90 transition-colors"
                                        aria-label="Remove image 3">
                                        <X :size="16" />
                                    </button>
                                </div>
                                <label v-else for="item_image_3"
                                    class="flex flex-col items-center justify-center aspect-square rounded-lg border-2 border-dashed border-border hover:border-primary hover:bg-accent cursor-pointer transition-colors">
                                    <Upload :size="32" class="text-muted-foreground mb-2" />
                                    <span class="text-sm text-muted-foreground">Upload Image 3 (optional)</span>
                                    <span class="text-xs text-muted-foreground mt-1">Click to browse</span>
                                </label>
                                <Input id="item_image_3" name="item_image_3" type="file" accept="image/*" class="hidden"
                                    @change="(e: Event) => handleImageSelect(e, 2)" />
                            </div>
                            <InputError :message="errors.item_image_3" />
                        </div>
                    </div>
                </div>

                <!-- Submit button -->
                <div class="flex justify-end gap-2">
                    <Button type="submit" :disabled="processing">
                        <template v-if="processing">
                            <Spinner class="mr-2" />Saving…
                        </template>
                        <template v-else>Save Changes</template>
                    </Button>
                </div>
            </Form>
        </div>
    </AppLayout>
</template>
