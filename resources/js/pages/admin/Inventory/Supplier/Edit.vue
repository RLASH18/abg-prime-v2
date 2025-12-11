<script setup lang="ts">
import LinkButton from '@/components/LinkButton.vue';
import InputError from '@/components/InputError.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import suppliersRoutes from '@/routes/admin/suppliers';
import { type BreadcrumbItem } from '@/types';
import { Form, Head, useForm } from '@inertiajs/vue3';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import Select from '@/components/Select.vue';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import { Spinner } from '@/components/ui/spinner';
import { type Supplier } from '@/types/admin';

const props = defineProps<{
    suppliers: Supplier;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Suppliers',
        href: suppliersRoutes.index().url
    },
    {
        title: 'Edit supplier',
        href: suppliersRoutes.edit(props.suppliers.id).url
    },
];

const statusOptions = ['active', 'inactive'];

const form = useForm({
    supplier_name: props.suppliers.supplier_name,
    email: props.suppliers.email ?? '',
    phone: props.suppliers.phone ?? '',
    address: props.suppliers.address ?? '',
    status: props.suppliers.status,
});
</script>

<template>

    <Head title="Suppliers - Edit suppliers" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <!-- Header -->
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h1 class="text-2xl font-bold">Edit suppliers</h1>
                    <p class="text-sm text-muted-foreground">
                        Update the details for this suppliers.
                    </p>
                </div>
                <LinkButton :href="suppliersRoutes.index().url" mode="back" label="Back to list" />
            </div>

            <!-- Form -->
            <Form :action="suppliersRoutes.update(props.suppliers.id).url" method="patch" v-slot="{ errors, processing }"
                class="grid gap-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- suppliers Name -->
                    <div class="space-y-2">
                        <Label for="supplier_name">suppliers Name<span class="text-red-500">*</span></Label>
                        <Input id="supplier_name" name="supplier_name" placeholder="Enter suppliers name"
                            v-model="form.supplier_name" required />
                        <InputError :message="errors.supplier_name" />
                    </div>

                    <!-- Email -->
                    <div class="space-y-2">
                        <Label for="email">Email</Label>
                        <Input id="email" name="email" type="email" placeholder="Enter email address"
                            v-model="form.email" />
                        <InputError :message="errors.email" />
                    </div>

                    <!-- Phone -->
                    <div class="space-y-2">
                        <Label for="phone">Phone</Label>
                        <Input id="phone" name="phone" type="tel" placeholder="Enter phone number"
                            v-model="form.phone" />
                        <InputError :message="errors.phone" />
                    </div>

                    <!-- Status -->
                    <div class="space-y-2">
                        <Label for="status">Status<span class="text-red-500">*</span></Label>
                        <Select name="status" :options="statusOptions" placeholder="Select status" v-model="form.status"
                            required />
                        <InputError :message="errors.status" />
                    </div>
                </div>

                <!-- Address -->
                <div class="space-y-2">
                    <Label for="address">Address</Label>
                    <Textarea id="address" name="address" placeholder="Enter suppliers address" v-model="form.address" />
                    <InputError :message="errors.address" />
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
