<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
</script>

<template>
    <div
        class="flex aspect-square size-8 items-center justify-center rounded-md bg-neutral-200 text-white"
    >
        <AppLogoIcon class="size-8 fill-current text-white" />
    </div>
    <div class="ml-1 grid flex-1 text-left text-sm">
        <span class="truncate leading-tight font-semibold">ABG Prime</span>
        <span class="truncate text-xs font-normal text-muted-foreground">Builders Supplies Inc.</span>
    </div>
</template>
