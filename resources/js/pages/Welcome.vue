<script setup lang="ts">
import AboutSection from '@/components/welcome/AboutSection.vue';
import FooterSection from '@/components/welcome/FooterSection.vue';
import HeroSection from '@/components/welcome/HeroSection.vue';
import NavbarSection from '@/components/welcome/NavbarSection.vue';
import ProductSection from '@/components/welcome/ProductSection.vue';

import { Head } from '@inertiajs/vue3';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);
</script>

<template>
    <Head title="ABG Prime Builders - Quality Construction Supplies">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link
            rel="preconnect"
            href="https://fonts.gstatic.com"
            crossorigin="anonymous"
        />
        <link
            href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@300;400;500;600;700;800;900&display=swap"
            rel="stylesheet"
        />
    </Head>

    <div class="flex min-h-screen w-full flex-col font-body">
        <!-- NAVBAR -->
        <NavbarSection :can-register="canRegister" />

        <!-- MAIN CONTENT -->
        <main class="flex-grow">
            <HeroSection />
            <AboutSection />
            <ProductSection />
            <FooterSection />
        </main>
    </div>
</template>

<style>
html {
    scroll-behavior: smooth;
}
</style>
