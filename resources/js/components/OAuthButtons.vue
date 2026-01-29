<script setup lang="ts">
import oauth from '@/routes/oauth';
import { Chrome, Facebook } from 'lucide-vue-next';

defineProps<{
    secondary?: boolean;
}>();
</script>

<template>
    <div class="mt-2 flex justify-center gap-4">
        <!-- Google -->
        <a
            title="Continue with Google"
            :href="oauth.redirect('google').url"
            class="flex h-12 w-12 items-center justify-center rounded-full border border-[var(--abg-primary)] text-[var(--abg-primary)] transition-colors hover:bg-[var(--abg-primary)]/10"
            aria-label="Continue with Google"
        >
            <Chrome class="size-5" />
        </a>

        <!-- Facebook -->
        <a
            title="Continue with Facebook"
            :href="oauth.redirect('facebook').url"
            class="flex h-12 w-12 items-center justify-center rounded-full border border-[var(--abg-primary)] text-[var(--abg-primary)] transition-colors hover:bg-[var(--abg-primary)]/10"
            aria-label="Continue with Facebook"
        >
            <Facebook class="size-5" />
        </a>
    </div>
</template>
