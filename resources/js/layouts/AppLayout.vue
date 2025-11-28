<script setup lang="ts">
import AdminAppLayout from '@/layouts/app/AppSidebarLayout.vue';
import CustomerAppLayout from '@/layouts/app/AppHeaderLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const page = usePage();
const user = computed(() => page.props.auth.user);
</script>

<template>
    <AdminAppLayout v-if="user.role === 'admin'" :breadcrumbs="breadcrumbs">
        <slot />
    </AdminAppLayout>
    <CustomerAppLayout v-else :breadcrumbs="breadcrumbs">
        <slot />
    </CustomerAppLayout>
</template>
