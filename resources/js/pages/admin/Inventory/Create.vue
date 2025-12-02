<script setup lang="ts">
import LinkButton from '@/components/LinkButton.vue';
import InputError from '@/components/InputError.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import inventory from '@/routes/admin/inventory';
import { type BreadcrumbItem } from '@/types';
import { Form, Head, useForm } from '@inertiajs/vue3';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import Select from '@/components/Select.vue';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import { Spinner } from '@/components/ui/spinner';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Inventory',
        href: inventory.index().url,
    },
    {
        title: 'Add Item',
        href: inventory.create().url,
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
    'Chemicals'
];
</script>

<template>

    <Head title="Inventory - Add an Item" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold">Add New Inventory Item</h1>
                    <p class="text-sm text-muted-foreground">Fill in the details to add a new item to the inventory.</p>
                </div>
                <LinkButton :href="inventory.index().url" mode="back" label="Back to list" />
            </div>

            <Form :action="inventory.store()" v-slot="{ errors, processing }" :reset-on-success="[
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
                'item_image_3'
            ]" class="grid gap-6">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Item Name -->
                    <div class="space-y-2">
                        <Label for="item_name">Item Name</Label>
                        <Input id="item_name" name="item_name" placeholder="Enter item name" required />
                        <InputError :message="errors.item_name" />
                    </div>

                    <!-- Brand Name -->
                    <div class="space-y-2">
                        <Label for="brand_name">Brand Name</Label>
                        <Input id="brand_name" name="brand_name" placeholder="Enter brand name" />
                        <InputError :message="errors.brand_name" />
                    </div>

                    <!-- Category -->
                    <div class="space-y-2">
                        <Label for="category">Category</Label>
                        <Select name="category" :options="categories" placeholder="Select a category" required />
                        <InputError :message="errors.category" />
                    </div>

                    <!-- Supplier ID (Placeholder) -->
                    <div class="space-y-2">
                        <Label for="supplier_id">Supplier ID</Label>
                        <Input id="supplier_id" name="supplier_id" placeholder="Enter supplier ID" type="number" />
                        <InputError :message="errors.supplier_id" />
                    </div>

                    <!-- Unit Price -->
                    <div class="space-y-2">
                        <Label for="unit_price">Unit Price</Label>
                        <Input id="unit_price" name="unit_price" type="number" step="0.01" placeholder="0.00"
                            required />
                        <InputError :message="errors.unit_price" />
                    </div>

                    <!-- Quantity -->
                    <div class="space-y-2">
                        <Label for="quantity">Quantity</Label>
                        <Input id="quantity" name="quantity" type="number" placeholder="0" required />
                        <InputError :message="errors.quantity" />
                    </div>

                    <!-- Restock Threshold -->
                    <div class="space-y-2">
                        <Label for="restock_threshold">Restock Threshold</Label>
                        <Input id="restock_threshold" name="restock_threshold" type="number" placeholder="10" />
                        <InputError :message="errors.restock_threshold" />
                    </div>
                </div>

                <!-- Description -->
                <div class="space-y-2">
                    <Label for="description">Description</Label>
                    <Textarea id="description" name="description" placeholder="Enter item description"></Textarea>
                    <InputError :message="errors.description" />
                </div>

                <!-- Images -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="space-y-2">
                        <Label for="item_image_1">Image 1</Label>
                        <Input id="item_image_1" name="item_image_1" type="file" accept="image/*" />
                        <InputError :message="errors.item_image_1" />
                    </div>
                    <div class="space-y-2">
                        <Label for="item_image_2">Image 2</Label>
                        <Input id="item_image_2" name="item_image_2" type="file" accept="image/*" />
                        <InputError :message="errors.item_image_2" />
                    </div>
                    <div class="space-y-2">
                        <Label for="item_image_3">Image 3</Label>
                        <Input id="item_image_3" name="item_image_3" type="file" accept="image/*" />
                        <InputError :message="errors.item_image_3" />
                    </div>
                </div>

                <div class="flex justify-end">
                    <Button type="submit" :disabled="processing">
                        <Spinner v-if="processing" />
                        Save Item
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
