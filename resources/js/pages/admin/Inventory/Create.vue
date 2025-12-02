<script setup lang="ts">
import LinkButton from '@/components/LinkButton.vue';
import InputError from '@/components/InputError.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import inventory from '@/routes/admin/inventory';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import Select from '@/components/Select.vue';

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

const form = useForm({
    supplier_id: '' as string | number,
    brand_name: '',
    item_name: '',
    description: '',
    category: '',
    unit_price: '' as string | number,
    quantity: '' as string | number,
    restock_threshold: 10,
    item_image_1: null as File | null,
    item_image_2: null as File | null,
    item_image_3: null as File | null,
});

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

            <form @submit.prevent="form.post(inventory.store().url)" class="grid gap-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Item Name -->
                    <div class="space-y-2">
                        <Label for="item_name">Item Name</Label>
                        <Input id="item_name" v-model="form.item_name" placeholder="Enter item name" required />
                        <InputError :message="form.errors.item_name" />
                    </div>

                    <!-- Brand Name -->
                    <div class="space-y-2">
                        <Label for="brand_name">Brand Name</Label>
                        <Input id="brand_name" v-model="form.brand_name" placeholder="Enter brand name" />
                        <InputError :message="form.errors.brand_name" />
                    </div>

                    <!-- Category -->
                    <div class="space-y-2">
                        <Label for="category">Category</Label>
                        <Select v-model="form.category" :options="categories" placeholder="Select a category"
                            required />
                        <InputError :message="form.errors.category" />
                    </div>

                    <!-- Supplier ID (Placeholder) -->
                    <div class="space-y-2">
                        <Label for="supplier_id">Supplier ID</Label>
                        <Input id="supplier_id" v-model="form.supplier_id" placeholder="Enter supplier ID"
                            type="number" />
                        <InputError :message="form.errors.supplier_id" />
                    </div>

                    <!-- Unit Price -->
                    <div class="space-y-2">
                        <Label for="unit_price">Unit Price</Label>
                        <Input id="unit_price" v-model="form.unit_price" type="number" step="0.01" placeholder="0.00"
                            required />
                        <InputError :message="form.errors.unit_price" />
                    </div>

                    <!-- Quantity -->
                    <div class="space-y-2">
                        <Label for="quantity">Quantity</Label>
                        <Input id="quantity" v-model="form.quantity" type="number" placeholder="0" required />
                        <InputError :message="form.errors.quantity" />
                    </div>

                    <!-- Restock Threshold -->
                    <div class="space-y-2">
                        <Label for="restock_threshold">Restock Threshold</Label>
                        <Input id="restock_threshold" v-model="form.restock_threshold" type="number" placeholder="10" />
                        <InputError :message="form.errors.restock_threshold" />
                    </div>
                </div>

                <!-- Description -->
                <div class="space-y-2">
                    <Label for="description">Description</Label>
                    <textarea id="description" v-model="form.description"
                        class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        placeholder="Enter item description"></textarea>
                    <InputError :message="form.errors.description" />
                </div>

                <!-- Images -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="space-y-2">
                        <Label for="item_image_1">Image 1</Label>
                        <Input id="item_image_1" type="file" @input="form.item_image_1 = $event.target.files[0]"
                            accept="image/*" />
                        <InputError :message="form.errors.item_image_1" />
                    </div>
                    <div class="space-y-2">
                        <Label for="item_image_2">Image 2</Label>
                        <Input id="item_image_2" type="file" @input="form.item_image_2 = $event.target.files[0]"
                            accept="image/*" />
                        <InputError :message="form.errors.item_image_2" />
                    </div>
                    <div class="space-y-2">
                        <Label for="item_image_3">Image 3</Label>
                        <Input id="item_image_3" type="file" @input="form.item_image_3 = $event.target.files[0]"
                            accept="image/*" />
                        <InputError :message="form.errors.item_image_3" />
                    </div>
                </div>

                <div class="flex justify-end">
                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Saving...' : 'Save Item' }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>

</template>
