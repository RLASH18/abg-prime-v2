<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <img
        src="/img/abg-logo.png"
        alt="ABG Logo"
        :class="className"
        v-bind="$attrs"
    />
</template>
