<script setup lang="ts">
import gsap from 'gsap';
import ScrollTrigger from 'gsap/dist/ScrollTrigger';
import { onMounted, ref } from 'vue';

gsap.registerPlugin(ScrollTrigger);

// Template refs for animations
const aboutTitle = ref<HTMLElement | null>(null);
const aboutDescription = ref<HTMLElement | null>(null);
const aboutImage = ref<HTMLElement | null>(null);
const aboutTagline = ref<HTMLElement | null>(null);

onMounted(() => {
    // About Title - Slide in from left
    gsap.from(aboutTitle.value, {
        scrollTrigger: {
            trigger: aboutTitle.value,
            start: 'top 85%',
            toggleActions: 'play none none reverse',
        },
        x: -100,
        opacity: 0,
        duration: 1,
        ease: 'power3.out',
    });

    // About Description - Fade in
    gsap.from(aboutDescription.value, {
        scrollTrigger: {
            trigger: aboutDescription.value,
            start: 'top 85%',
            toggleActions: 'play none none reverse',
        },
        y: 30,
        opacity: 0,
        duration: 0.8,
        delay: 0.2,
        ease: 'power3.out',
    });

    // About Image - Scale and fade in
    gsap.from(aboutImage.value, {
        scrollTrigger: {
            trigger: aboutImage.value,
            start: 'top 85%',
            toggleActions: 'play none none reverse',
        },
        scale: 0.9,
        opacity: 0,
        duration: 1,
        ease: 'power2.out',
    });

    // About Tagline - Fade in from right
    gsap.from(aboutTagline.value, {
        scrollTrigger: {
            trigger: aboutTagline.value,
            start: 'top 85%',
            toggleActions: 'play none none reverse',
        },
        x: 50,
        opacity: 0,
        duration: 0.8,
        ease: 'power3.out',
    });

    // Feature Items - Staggered entrance
    gsap.from('.feature-item', {
        scrollTrigger: {
            trigger: '.feature-item',
            start: 'top 85%',
            toggleActions: 'play none none reverse',
        },
        y: 40,
        opacity: 0,
        duration: 0.6,
        stagger: 0.15,
        ease: 'power3.out',
    });
});
</script>

<template>
    <section
        id="about"
        class="w-full bg-[var(--abg-secondary)] px-6 py-20 text-[var(--abg-primary)] md:px-12"
    >
        <div
            class="mx-auto grid max-w-7xl grid-cols-1 items-stretch gap-16 lg:grid-cols-2"
        >
            <!-- Left Column -->
            <div class="flex h-full flex-col gap-8">
                <div>
                    <h2
                        ref="aboutTitle"
                        class="mb-6 font-display text-[6rem] leading-[0.8] text-[var(--abg-primary)] md:text-[8rem]"
                    >
                        ABOUT US
                    </h2>
                    <p
                        ref="aboutDescription"
                        class="max-w-xl text-lg leading-relaxed font-medium opacity-80 md:text-xl"
                    >
                        ABG Prime Builders' all-in-one platform drives peak
                        efficiency by merging real-time inventory tracking with
                        lightning-fast order processing.
                    </p>
                </div>
                <!-- Image container -->
                <div
                    ref="aboutImage"
                    class="relative min-h-[300px] w-full flex-grow overflow-hidden rounded-3xl shadow-lg"
                >
                    <img
                        src="/img/welcome/about-img.jpg"
                        alt="Vintage carpentry tools"
                        class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 hover:scale-105"
                    />
                </div>
            </div>

            <!-- Right Column -->
            <div class="flex h-full flex-col justify-between pt-4 lg:pt-8">
                <div>
                    <h3
                        ref="aboutTagline"
                        class="mb-12 text-center font-body text-3xl leading-tight font-extrabold text-[var(--abg-primary)] md:text-4xl"
                    >
                        Building the future, <br />
                        managing the present.
                    </h3>

                    <div class="flex flex-col">
                        <!-- Feature 1: Smart Inventory -->
                        <div
                            class="feature-item flex gap-6 border-b border-[var(--abg-primary)]/20 py-8"
                        >
                            <div
                                class="mt-1 flex-shrink-0 text-[var(--abg-primary)]"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-8 w-8"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.5"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <rect
                                        width="8"
                                        height="4"
                                        x="8"
                                        y="2"
                                        rx="1"
                                        ry="1"
                                    />
                                    <path
                                        d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"
                                    />
                                    <path d="M12 11h4" />
                                    <path d="M12 16h4" />
                                    <path d="M8 11h.01" />
                                    <path d="M8 16h.01" />
                                </svg>
                            </div>
                            <div class="flex flex-col gap-2">
                                <h4
                                    class="font-body text-xl font-bold text-[var(--abg-primary)]"
                                >
                                    Smart Inventory
                                </h4>
                                <p
                                    class="font-body leading-relaxed font-medium text-[var(--abg-primary)]/70"
                                >
                                    Monitor inventory to avoid overstock and
                                    stockouts.
                                </p>
                            </div>
                        </div>

                        <!-- Feature 2: Rapid Processing -->
                        <div
                            class="feature-item flex gap-6 border-b border-[var(--abg-primary)]/20 py-8"
                        >
                            <div
                                class="mt-1 flex-shrink-0 text-[var(--abg-primary)]"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-8 w-8"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.5"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <circle cx="18" cy="15" r="3" />
                                    <circle cx="9" cy="7" r="4" />
                                    <path d="M10 15H6a4 4 0 0 0-4 4v2" />
                                    <path d="m21.7 16.4-.9-.3" />
                                    <path d="m15.2 13.9-.9-.3" />
                                    <path d="m16.6 18.7.3-.9" />
                                    <path d="m19.1 12.2.3-.9" />
                                    <path d="m19.6 18.7-.4-1" />
                                    <path d="m16.8 12.3-.4-1" />
                                    <path d="m14.3 16.6 1-.4" />
                                    <path d="m20.7 13.8 1-.4" />
                                </svg>
                            </div>
                            <div class="flex flex-col gap-2">
                                <h4
                                    class="font-body text-xl font-bold text-[var(--abg-primary)]"
                                >
                                    Rapid Processing
                                </h4>
                                <p
                                    class="font-body leading-relaxed font-medium text-[var(--abg-primary)]/70"
                                >
                                    Process walk-in and bulk orders with speed
                                    and accuracy.
                                </p>
                            </div>
                        </div>

                        <!-- Feature 3: Detailed Reporting -->
                        <div
                            class="feature-item flex gap-6 border-b border-[var(--abg-primary)]/20 py-8"
                        >
                            <div
                                class="mt-1 flex-shrink-0 text-[var(--abg-primary)]"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-8 w-8"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.5"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path d="M3 3v18h18" />
                                    <path d="M18 17V9" />
                                    <path d="M13 17V5" />
                                    <path d="M8 17v-3" />
                                </svg>
                            </div>
                            <div class="flex flex-col gap-2">
                                <h4
                                    class="font-body text-xl font-bold text-[var(--abg-primary)]"
                                >
                                    Detailed Reporting
                                </h4>
                                <p
                                    class="font-body leading-relaxed font-medium text-[var(--abg-primary)]/70"
                                >
                                    Generate clear, detailed sales and inventory
                                    reports.
                                </p>
                            </div>
                        </div>

                        <!-- Feature 4: Secure & Accessible -->
                        <div class="feature-item flex gap-6 py-8">
                            <div
                                class="mt-1 flex-shrink-0 text-[var(--abg-primary)]"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-8 w-8"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.5"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path
                                        d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"
                                    />
                                    <path d="m9 12 2 2 4-4" />
                                </svg>
                            </div>
                            <div class="flex flex-col gap-2">
                                <h4
                                    class="font-body text-xl font-bold text-[var(--abg-primary)]"
                                >
                                    Secure & Accessible
                                </h4>
                                <p
                                    class="font-body leading-relaxed font-medium text-[var(--abg-primary)]/70"
                                >
                                    Secure, easy to access, and available
                                    anytime, anywhere.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
