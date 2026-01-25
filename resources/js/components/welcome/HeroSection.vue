<script setup lang="ts">
import gsap from 'gsap';
import { onMounted, ref } from 'vue';

// Template refs for animations
const heroTitle = ref<HTMLElement | null>(null);
const heroSubtitle = ref<HTMLElement | null>(null);
const heroButton = ref<HTMLElement | null>(null);
const heroBadges = ref<HTMLElement | null>(null);
const heroImage = ref<HTMLElement | null>(null);

onMounted(() => {
    const heroTimeline = gsap.timeline({
        defaults: { ease: 'power3.out' },
        delay: 0.3,
    });

    heroTimeline
        .from(heroTitle.value, {
            y: 80,
            opacity: 0,
            duration: 1,
        })
        .from(
            heroSubtitle.value,
            {
                y: 40,
                opacity: 0,
                duration: 0.8,
            },
            '-=0.6',
        )
        .from(
            heroButton.value,
            {
                y: 30,
                opacity: 0,
                duration: 0.6,
                immediateRender: false,
            },
            '-=0.4',
        )
        .from(
            heroBadges.value,
            {
                y: 20,
                opacity: 0,
                duration: 0.5,
            },
            '-=0.3',
        )
        .from(
            heroImage.value,
            {
                x: 100,
                opacity: 0,
                duration: 1,
                ease: 'power2.out',
            },
            '-=1',
        );
});
</script>

<template>
    <section
        class="relative flex min-h-[90vh] w-full items-center justify-center overflow-hidden bg-[var(--abg-primary)] px-6 py-24 md:px-12 md:py-32"
    >
        <div
            class="grid w-full max-w-7xl grid-cols-1 items-center gap-16 md:grid-cols-2"
        >
            <!-- Left Content -->
            <div class="z-10 flex flex-col items-start space-y-8">
                <div ref="heroTitle" class="flex flex-col text-white">
                    <h1
                        class="font-display text-[5rem] leading-none tracking-tight uppercase md:text-[7rem] lg:text-[8rem]"
                    >
                        ABG Prime <br />
                        Builders
                    </h1>
                </div>

                <p
                    ref="heroSubtitle"
                    class="max-w-md font-body text-lg leading-relaxed font-light text-white/90 md:text-xl"
                >
                    Shop high-quality tools and materials at unbeatable prices
                    and at your door service.
                </p>

                <a
                    ref="heroButton"
                    href="#products"
                    class="mt-6 inline-block border-[3px] border-[var(--abg-secondary)] bg-[var(--abg-secondary)] px-10 py-4 font-display text-2xl tracking-wide text-[var(--abg-primary)] uppercase shadow-lg transition-all duration-300 hover:bg-transparent hover:text-[var(--abg-secondary)] hover:shadow-xl md:text-3xl"
                >
                    Start Shopping
                </a>

                <div
                    ref="heroBadges"
                    class="mt-6 flex items-center gap-8 pt-4 text-white/80"
                >
                    <div class="flex items-center gap-2">
                        <!-- Package Icon -->
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M16.5 9.4 7.55 4.24" />
                            <path
                                d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"
                            />
                            <polyline points="3.29 7 12 12 20.71 7" />
                            <line x1="12" x2="12" y1="22" y2="12" />
                        </svg>
                        <span
                            class="font-body text-sm font-medium tracking-wide"
                            >Free Shipping</span
                        >
                    </div>
                    <div class="flex items-center gap-2">
                        <!-- RotateCcw Icon -->
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path
                                d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"
                            />
                            <path d="M3 3v5h5" />
                        </svg>
                        <span
                            class="font-body text-sm font-medium tracking-wide"
                            >Easy Returns</span
                        >
                    </div>
                </div>
            </div>

            <!-- Right Content - Image -->
            <div
                ref="heroImage"
                class="relative z-10 flex h-full items-center justify-center md:justify-end"
            >
                <div class="relative aspect-square w-full max-w-xl">
                    <img
                        src="/img/welcome/hero-img.jpg"
                        alt="Pegboard with tools"
                        class="h-full w-full rounded-3xl border-4 border-white/10 object-cover shadow-2xl grayscale-[0.2]"
                    />
                </div>
            </div>
        </div>
    </section>
</template>
