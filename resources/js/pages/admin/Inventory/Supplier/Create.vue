<script setup lang="ts">
import LinkButton from '@/components/LinkButton.vue';
import InputError from '@/components/InputError.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import suppliersRoutes from '@/routes/admin/suppliers';
import { type BreadcrumbItem } from '@/types';
import { Form, Head } from '@inertiajs/vue3';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import Select from '@/components/Select.vue';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import { Spinner } from '@/components/ui/spinner';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'suppliersRoutes',
        href: suppliersRoutes.index().url,
    },
    {
        title: 'Add supplier',
        href: suppliersRoutes.create().url,
    },
];

const statusOptions = ['active', 'inactive'];
</script>

<template>

    <Head title="suppliersRoutes - Add a suppliersRoutes" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h1 class="text-2xl font-bold">Add New suppliersRoutes</h1>
                    <p class="text-sm text-muted-foreground">Fill in the details to add a new suppliersRoutes.</p>
                </div>
                <LinkButton :href="suppliersRoutes.index().url" mode="back" label="Back to list" />
            </div>

            <Form :action="suppliersRoutes.store()" v-slot="{ errors, processing }" :reset-on-success="[
                'supplier_name',
                'email',
                'phone',
                'address',
                'status'
            ]" class="grid gap-6">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- suppliersRoutes Name -->
                    <div class="space-y-2">
                        <Label for="supplier_name">suppliersRoutes Name<span class="text-red-500">*</span></Label>
                        <Input id="supplier_name" name="supplier_name" placeholder="Enter suppliersRoutes name" required />
                        <InputError :message="errors.supplier_name" />
                    </div>

                    <!-- Email -->
                    <div class="space-y-2">
                        <Label for="email">Email</Label>
                        <Input id="email" name="email" type="email" placeholder="Enter email address" />
                        <InputError :message="errors.email" />
                    </div>

                    <!-- Phone -->
                    <div class="space-y-2">
                        <Label for="phone">Phone</Label>
                        <Input id="phone" name="phone" type="tel" placeholder="Enter phone number" />
                        <InputError :message="errors.phone" />
                    </div>

                    <!-- Status -->
                    <div class="space-y-2">
                        <Label for="status">Status<span class="text-red-500">*</span></Label>
                        <Select name="status" :options="statusOptions" placeholder="Select status" required />
                        <InputError :message="errors.status" />
                    </div>
                </div>

                <!-- Address -->
                <div class="space-y-2">
                    <Label for="address">Address</Label>
                    <Textarea id="address" name="address" placeholder="Enter suppliersRoutes address"></Textarea>
                    <InputError :message="errors.address" />
                </div>

                <div class="flex justify-end">
                    <Button type="submit" :disabled="processing">
                        <Spinner v-if="processing" />
                        Save supplier
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
