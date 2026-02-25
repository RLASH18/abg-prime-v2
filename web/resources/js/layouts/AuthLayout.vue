<script setup lang="ts">
import AuthLayout from '@/layouts/auth/AuthSplitLayout.vue';

defineProps<{
    title?: string;
    description?: string;
    header?: string;
}>();
</script>

<template>
    <AuthLayout :title="title" :description="description" :header="header">
        <slot />
    </AuthLayout>
</template>
