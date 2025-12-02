<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import inventory from '@/routes/admin/inventory';
import LinkButton from '@/components/LinkButton.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Inventory',
        href: inventory.index().url,
    },
];
</script>

<template>

    <Head title="Inventory" />

    <AppLayout :breadcrumbs="breadcrumbs">

        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">

            <div class="flex justify-end">
                <LinkButton :href="inventory.create().url" label="Add an Item" />
            </div>

        </div>
    </AppLayout>
</template>
