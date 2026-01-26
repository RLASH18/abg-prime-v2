<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { home } from '@/routes';
import { Link } from '@inertiajs/vue3';

defineProps<{
    title?: string;
    description?: string;
    header?: string;
}>();
</script>

<template>
    <div
        class="flex min-h-screen items-center justify-center bg-[var(--abg-primary)] p-4 lg:p-8"
    >
        <div
            class="flex w-full max-w-6xl flex-col lg:flex-row lg:items-start lg:justify-between lg:gap-16 xl:gap-24"
        >
            <!-- Left Side: Content & Image -->
            <div class="mb-12 flex flex-col lg:mb-0 lg:w-1/2">
                <!-- Logo Section -->
                <Link
                    :href="home()"
                    class="mb-8 flex items-center gap-4 transition-opacity hover:opacity-80"
                >
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-[var(--abg-secondary)] p-2 shadow-lg"
                    >
                        <AppLogoIcon class="size-full object-contain" />
                    </div>
                    <div class="flex flex-col">
                        <span
                            class="font-display text-xl font-bold tracking-widest text-[var(--abg-secondary)]"
                        >
                            ABG PRIME
                        </span>
                        <span
                            class="font-body text-xs font-medium tracking-widest text-[var(--abg-secondary)]/80"
                        >
                            BUILDERS SUPPLIES INC.
                        </span>
                    </div>
                </Link>

                <!-- Header Text -->
                <div class="mb-6">
                    <h2
                        class="font-display text-5xl leading-[0.85] font-bold tracking-tight text-[var(--abg-secondary)] uppercase md:text-6xl lg:text-7xl"
                    >
                        {{ header || 'Login' }}
                    </h2>
                    <p
                        class="mt-2 font-body text-lg font-medium text-[var(--abg-secondary)]/90 md:text-xl"
                    >
                        {{
                            description ||
                            'Welcome back to ABG Prime Builders Supplies Inc.'
                        }}
                    </p>
                </div>

                <!-- Image Container -->
                <div
                    class="relative aspect-square w-full max-w-sm overflow-hidden rounded-[2.5rem] bg-[var(--abg-secondary)]/10 shadow-xl lg:max-w-md"
                >
                    <img
                        src="/img/auth.jpg"
                        alt="Tools on pegboard"
                        class="h-full w-full object-cover"
                    />
                    <!-- Overlay to tint slightly if needed, purely for aesthetics -->
                    <div
                        class="absolute inset-0 bg-[var(--abg-primary)]/10 mix-blend-overlay"
                    ></div>
                </div>
            </div>

            <!-- Right Side: Form Card -->
            <div
                class="flex w-full items-center justify-center lg:w-1/2 lg:justify-end"
            >
                <div
                    class="w-full max-w-md rounded-[3rem] bg-[var(--abg-secondary)] px-8 py-12 shadow-2xl md:px-12 md:py-16"
                >
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
