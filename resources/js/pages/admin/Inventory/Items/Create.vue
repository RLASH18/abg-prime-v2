<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import LinkButton from '@/components/LinkButton.vue';
import Select from '@/components/Select.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import { useImagePreviews } from '@/composables/useImagePreviews';
import AppLayout from '@/layouts/AppLayout.vue';
import items from '@/routes/admin/items';
import { type BreadcrumbItem } from '@/types';
import { Supplier } from '@/types/admin';
import { Form, Head } from '@inertiajs/vue3';
import { Upload, X } from 'lucide-vue-next';

defineProps<{
    suppliers: Supplier[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Items',
        href: items.index().url,
    },
    {
        title: 'Add Item',
        href: items.create().url,
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
</script>

<template>
    <Head title="Items - Add item" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Add New Item</h1>
                    <p class="text-sm text-muted-foreground">
                        Fill in the details to add a new item to the inventory.
                    </p>
                </div>
                <LinkButton
                    :href="items.index().url"
                    mode="back"
                    label="Back to list"
                />
            </div>

            <Form
                :action="items.store()"
                v-slot="{ errors, processing }"
                :reset-on-success="[
                    'item_name',
                    'brand_name',
                    'category',
                    'supplier_id',
                    'unit_price',
                    'quantity',
                    'restock_threshold',
                    'description',
                    'item_image_1',
                    'item_image_2',
                    'item_image_3',
                ]"
                class="grid gap-6"
            >
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <!-- Item Name -->
                    <div class="space-y-2">
                        <Label for="item_name"
                            >Item Name<span class="text-red-500">*</span></Label
                        >
                        <Input
                            id="item_name"
                            name="item_name"
                            placeholder="Enter item name"
                            required
                        />
                        <InputError :message="errors.item_name" />
                    </div>

                    <!-- Brand Name -->
                    <div class="space-y-2">
                        <Label for="brand_name"
                            >Brand Name<span class="text-red-500"
                                >*</span
                            ></Label
                        >
                        <Input
                            id="brand_name"
                            name="brand_name"
                            placeholder="Enter brand name"
                        />
                        <InputError :message="errors.brand_name" />
                    </div>

                    <!-- Category -->
                    <div class="space-y-2">
                        <Label for="category"
                            >Category<span class="text-red-500">*</span></Label
                        >
                        <Select
                            name="category"
                            :options="categories"
                            placeholder="Select a category"
                            required
                        />
                        <InputError :message="errors.category" />
                    </div>

                    <!-- Supplier -->
                    <div class="space-y-2">
                        <Label for="supplier_id">Supplier</Label>
                        <Select
                            v-if="suppliers.length > 0"
                            name="supplier_id"
                            :options="
                                suppliers.map((s) => ({
                                    value: s.id.toString(),
                                    label: s.supplier_name,
                                }))
                            "
                            placeholder="Select a supplier (optional)"
                        />
                        <div
                            v-else
                            class="flex items-center gap-2 rounded-md border border-border bg-muted p-3 text-sm text-muted-foreground"
                        >
                            <span
                                >No suppliers available. Please add suppliers
                                first.</span
                            >
                        </div>
                        <InputError :message="errors.supplier_id" />
                    </div>

                    <!-- Unit Price -->
                    <div class="space-y-2">
                        <Label for="unit_price"
                            >Unit Price<span class="text-red-500"
                                >*</span
                            ></Label
                        >
                        <Input
                            id="unit_price"
                            name="unit_price"
                            type="number"
                            step="0.01"
                            placeholder="0.00"
                            class="[&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none"
                            required
                        />
                        <InputError :message="errors.unit_price" />
                    </div>

                    <!-- Quantity -->
                    <div class="space-y-2">
                        <Label for="quantity"
                            >Quantity<span class="text-red-500">*</span></Label
                        >
                        <Input
                            id="quantity"
                            name="quantity"
                            type="number"
                            placeholder="0"
                            class="[&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none"
                            required
                        />
                        <InputError :message="errors.quantity" />
                    </div>

                    <!-- Restock Threshold -->
                    <div class="space-y-2">
                        <Label for="restock_threshold"
                            >Restock Threshold<span class="text-red-500"
                                >*</span
                            ></Label
                        >
                        <Input
                            id="restock_threshold"
                            name="restock_threshold"
                            type="number"
                            placeholder="10"
                            class="[&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none"
                        />
                        <InputError :message="errors.restock_threshold" />
                    </div>
                </div>

                <!-- Description -->
                <div class="space-y-2">
                    <Label for="description">Description</Label>
                    <Textarea
                        id="description"
                        name="description"
                        placeholder="Enter item description"
                    ></Textarea>
                    <InputError :message="errors.description" />
                </div>

                <!-- Images with Preview -->
                <div class="space-y-2">
                    <Label>Product Images</Label>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <!-- Image 1 -->
                        <div class="space-y-2">
                            <div class="group relative">
                                <div
                                    v-if="imagePreviews[0]"
                                    class="relative aspect-square overflow-hidden rounded-lg border-2 border-border bg-muted"
                                >
                                    <img
                                        :src="imagePreviews[0]"
                                        alt="Preview 1"
                                        class="h-full w-full object-cover"
                                    />
                                    <button
                                        type="button"
                                        @click="removeImage(0, 'item_image_1')"
                                        class="absolute top-2 right-2 rounded-full bg-destructive p-1.5 text-destructive-foreground shadow-lg transition-colors hover:bg-destructive/90"
                                        aria-label="Remove image 1"
                                    >
                                        <X :size="16" />
                                    </button>
                                </div>
                                <label
                                    v-else
                                    for="item_image_1"
                                    class="flex aspect-square cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-border transition-colors hover:border-primary hover:bg-accent"
                                >
                                    <Upload
                                        :size="32"
                                        class="mb-2 text-muted-foreground"
                                    />
                                    <span class="text-sm text-muted-foreground"
                                        >Upload Image 1</span
                                    >
                                    <span
                                        class="mt-1 text-xs text-muted-foreground"
                                        >Click to browse</span
                                    >
                                </label>
                                <Input
                                    id="item_image_1"
                                    name="item_image_1"
                                    type="file"
                                    accept="image/*"
                                    class="hidden"
                                    @change="
                                        (e: Event) => handleImageSelect(e, 0)
                                    "
                                />
                            </div>
                            <InputError :message="errors.item_image_1" />
                        </div>

                        <!-- Image 2 -->
                        <div class="space-y-2">
                            <div class="group relative">
                                <div
                                    v-if="imagePreviews[1]"
                                    class="relative aspect-square overflow-hidden rounded-lg border-2 border-border bg-muted"
                                >
                                    <img
                                        :src="imagePreviews[1]"
                                        alt="Preview 2"
                                        class="h-full w-full object-cover"
                                    />
                                    <button
                                        type="button"
                                        @click="removeImage(1, 'item_image_2')"
                                        class="absolute top-2 right-2 rounded-full bg-destructive p-1.5 text-destructive-foreground shadow-lg transition-colors hover:bg-destructive/90"
                                        aria-label="Remove image 2"
                                    >
                                        <X :size="16" />
                                    </button>
                                </div>
                                <label
                                    v-else
                                    for="item_image_2"
                                    class="flex aspect-square cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-border transition-colors hover:border-primary hover:bg-accent"
                                >
                                    <Upload
                                        :size="32"
                                        class="mb-2 text-muted-foreground"
                                    />
                                    <span class="text-sm text-muted-foreground"
                                        >Upload Image 2 (optional)</span
                                    >
                                    <span
                                        class="mt-1 text-xs text-muted-foreground"
                                        >Click to browse</span
                                    >
                                </label>
                                <Input
                                    id="item_image_2"
                                    name="item_image_2"
                                    type="file"
                                    accept="image/*"
                                    class="hidden"
                                    @change="
                                        (e: Event) => handleImageSelect(e, 1)
                                    "
                                />
                            </div>
                            <InputError :message="errors.item_image_2" />
                        </div>

                        <!-- Image 3 -->
                        <div class="space-y-2">
                            <div class="group relative">
                                <div
                                    v-if="imagePreviews[2]"
                                    class="relative aspect-square overflow-hidden rounded-lg border-2 border-border bg-muted"
                                >
                                    <img
                                        :src="imagePreviews[2]"
                                        alt="Preview 3"
                                        class="h-full w-full object-cover"
                                    />
                                    <button
                                        type="button"
                                        @click="removeImage(2, 'item_image_3')"
                                        class="absolute top-2 right-2 rounded-full bg-destructive p-1.5 text-destructive-foreground shadow-lg transition-colors hover:bg-destructive/90"
                                        aria-label="Remove image 3"
                                    >
                                        <X :size="16" />
                                    </button>
                                </div>
                                <label
                                    v-else
                                    for="item_image_3"
                                    class="flex aspect-square cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-border transition-colors hover:border-primary hover:bg-accent"
                                >
                                    <Upload
                                        :size="32"
                                        class="mb-2 text-muted-foreground"
                                    />
                                    <span class="text-sm text-muted-foreground"
                                        >Upload Image 3 (optional)</span
                                    >
                                    <span
                                        class="mt-1 text-xs text-muted-foreground"
                                        >Click to browse</span
                                    >
                                </label>
                                <Input
                                    id="item_image_3"
                                    name="item_image_3"
                                    type="file"
                                    accept="image/*"
                                    class="hidden"
                                    @change="
                                        (e: Event) => handleImageSelect(e, 2)
                                    "
                                />
                            </div>
                            <InputError :message="errors.item_image_3" />
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <Button type="submit" :disabled="processing">
                        <Spinner v-if="processing" />
                        Save Item
                    </Button>
                </div>
            </Form>
        </div>
    </AppLayout>
</template>
