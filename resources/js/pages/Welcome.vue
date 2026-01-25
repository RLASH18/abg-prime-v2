<script setup lang="ts">
import AboutSection from '@/components/welcome/AboutSection.vue';
import HeroSection from '@/components/welcome/HeroSection.vue';
import { login, register } from '@/routes';
import { dashboard } from '@/routes/admin';
import { Head, Link } from '@inertiajs/vue3';

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
        <nav
            class="sticky top-0 z-50 flex w-full flex-col items-center justify-between border-b border-[var(--abg-primary)]/10 bg-[var(--abg-secondary)] px-6 py-4 shadow-sm md:flex-row md:px-12"
        >
            <!-- Logo Section -->
            <div class="mb-4 flex items-center gap-3 md:mb-0">
                <img
                    src="/img/abg-logo.png"
                    alt="ABG Prime Logo"
                    class="h-14 w-14 object-contain"
                />
                <div class="flex flex-col">
                    <h1
                        class="font-body text-2xl leading-none font-black tracking-wide text-[var(--abg-primary)]"
                    >
                        ABG PRIME
                    </h1>
                    <p
                        class="font-body text-xs font-extrabold tracking-tight text-[var(--abg-primary)]/80"
                    >
                        Builders Supplies Inc.
                    </p>
                </div>
            </div>

            <!-- Navigation Links -->
            <div class="mb-4 flex items-center gap-8 md:mb-0">
                <a
                    href="#"
                    class="font-body text-sm font-extrabold tracking-widest text-[var(--abg-primary)] transition-opacity hover:opacity-70"
                    >HOME</a
                >
                <a
                    href="#about"
                    class="font-body text-sm font-extrabold tracking-widest text-[var(--abg-primary)] transition-opacity hover:opacity-70"
                    >ABOUT US</a
                >
                <a
                    href="#products"
                    class="font-body text-sm font-extrabold tracking-widest text-[var(--abg-primary)] transition-opacity hover:opacity-70"
                    >PRODUCTS</a
                >
            </div>

            <!-- Auth Buttons -->
            <div
                class="flex h-10 items-center rounded-full border border-[var(--abg-primary)] p-1"
            >
                <template v-if="$page.props.auth.user">
                    <Link
                        :href="dashboard()"
                        class="flex h-full items-center justify-center rounded-full px-6 font-body text-xs font-black tracking-widest text-[var(--abg-primary)] transition-colors hover:bg-[var(--abg-primary)]/10"
                    >
                        DASHBOARD
                    </Link>
                </template>
                <template v-else>
                    <Link
                        :href="login()"
                        class="flex h-full items-center justify-center rounded-l-full px-6 font-body text-xs font-black tracking-widest text-[var(--abg-primary)] transition-colors hover:bg-[var(--abg-primary)]/10"
                    >
                        SIGN IN
                    </Link>
                    <Link
                        v-if="canRegister"
                        :href="register()"
                        class="flex h-full items-center justify-center rounded-full bg-[var(--abg-primary)] px-6 font-body text-xs font-black tracking-widest text-white shadow-md transition-colors hover:bg-[var(--abg-primary)]/90"
                    >
                        SIGN UP
                    </Link>
                </template>
            </div>
        </nav>

        <!-- MAIN CONTENT -->
        <main class="flex-grow">
            <HeroSection />
            <AboutSection />
        </main>
    </div>
</template>

<style>
html {
    scroll-behavior: smooth;
}
</style>
