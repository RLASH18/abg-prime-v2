<script setup lang="ts">
import gsap from 'gsap';
import ScrollTrigger from 'gsap/dist/ScrollTrigger';
import { Mail, MapPin, Phone } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';

gsap.registerPlugin(ScrollTrigger);

// Template refs for animations
const footerBanner = ref<HTMLElement | null>(null);
const massiveText = ref<HTMLElement | null>(null);

onMounted(() => {
    // Footer Banner animation
    gsap.from(footerBanner.value, {
        scrollTrigger: {
            trigger: footerBanner.value,
            start: 'top 90%',
            toggleActions: 'play none none reverse',
        },
        y: 50,
        opacity: 0,
        duration: 1,
        ease: 'power3.out',
    });

    // Staggered columns animation
    gsap.from('.footer-col', {
        scrollTrigger: {
            trigger: '.footer-col',
            start: 'top 90%',
            toggleActions: 'play none none reverse',
        },
        y: 40,
        opacity: 0,
        duration: 0.8,
        stagger: 0.2,
        ease: 'power3.out',
    });

    // Massive Text animation
    gsap.from(massiveText.value, {
        scrollTrigger: {
            trigger: massiveText.value,
            start: 'top 95%',
            toggleActions: 'play none none reverse',
        },
        scale: 0.8,
        opacity: 0,
        duration: 1.2,
        ease: 'elastic.out(1, 0.75)',
    });

    // Refresh ScrollTrigger to recalculate positions
    setTimeout(() => {
        ScrollTrigger.refresh();
    }, 100);
});
</script>

<template>
    <footer class="w-full">
        <!-- Top Section: Visual Banner -->
        <div class="bg-[var(--abg-primary)] pt-12 pb-16">
            <div class="mx-auto max-w-7xl px-6 md:px-12">
                <div
                    ref="footerBanner"
                    class="relative h-48 w-full overflow-hidden rounded-xl border-4 border-white/10 shadow-2xl md:h-64"
                >
                    <img
                        src="/img/welcome/footer-img.jpg"
                        alt="Construction tools banner"
                        class="h-full w-full object-cover object-center opacity-90 transition-transform duration-700 hover:scale-105"
                    />
                </div>
            </div>
        </div>

        <!-- Bottom Section: Navigation & Contact -->
        <div
            class="bg-[var(--abg-primary-dark)] pt-20 pb-10 text-[var(--abg-secondary)]"
        >
            <div class="mx-auto max-w-7xl px-6 md:px-12">
                <!-- Middle Section: 3 Columns with dividers -->
                <div
                    class="relative mb-20 grid grid-cols-1 gap-8 md:grid-cols-3 md:gap-0"
                >
                    <!-- Col 1: Quick Links -->
                    <div
                        class="footer-col flex flex-col border-[var(--abg-secondary)]/20 md:border-r md:pr-12"
                    >
                        <h3 class="mb-6 font-display text-4xl tracking-wide">
                            Quick Links
                        </h3>
                        <ul
                            class="space-y-3 font-body text-lg text-[var(--abg-secondary)]/90"
                        >
                            <li>
                                <a
                                    href="#"
                                    class="group flex items-center gap-2 font-medium transition-colors hover:text-white"
                                >
                                    <span
                                        class="text-xs font-black opacity-60 transition-opacity group-hover:opacity-100"
                                        >></span
                                    >
                                    Home
                                </a>
                            </li>
                            <li>
                                <a
                                    href="#about"
                                    class="group flex items-center gap-2 font-medium transition-colors hover:text-white"
                                >
                                    <span
                                        class="text-xs font-black opacity-60 transition-opacity group-hover:opacity-100"
                                        >></span
                                    >
                                    About Us
                                </a>
                            </li>
                            <li>
                                <a
                                    href="#products"
                                    class="group flex items-center gap-2 font-medium transition-colors hover:text-white"
                                >
                                    <span
                                        class="text-xs font-black opacity-60 transition-opacity group-hover:opacity-100"
                                        >></span
                                    >
                                    Products
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Col 2: About -->
                    <div
                        class="footer-col flex flex-col border-[var(--abg-secondary)]/20 md:border-r md:px-12"
                    >
                        <h3 class="mb-6 font-display text-4xl tracking-wide">
                            About Prime Builders
                        </h3>
                        <p
                            class="font-body text-lg leading-relaxed font-medium text-[var(--abg-secondary)]/90"
                        >
                            Your one-stop shop for tools, materials, and
                            construction essentials. Prime Builders is built to
                            serve every Filipino builder - fast, reliable, and
                            trusted by professionals and DIYers alike.
                        </p>
                    </div>

                    <!-- Col 3: Contact -->
                    <div class="footer-col flex flex-col md:pl-12">
                        <h3 class="mb-6 font-display text-4xl tracking-wide">
                            Contact Us
                        </h3>
                        <ul
                            class="space-y-6 font-body text-lg font-medium text-[var(--abg-secondary)]/90"
                        >
                            <li class="flex items-start gap-4">
                                <MapPin
                                    class="mt-1 h-6 w-6 flex-shrink-0 text-[var(--abg-primary-light)]"
                                />
                                <span class="leading-snug"
                                    >KM17 Commonwealth Ave. Brgy. Holy Spirit,
                                    Quezon City</span
                                >
                            </li>
                            <li class="flex items-center gap-4">
                                <Phone
                                    class="h-6 w-6 flex-shrink-0 text-[var(--abg-primary-light)]"
                                />
                                <span>+63 939 917 4419</span>
                            </li>
                            <li class="flex items-center gap-4">
                                <Mail
                                    class="h-6 w-6 flex-shrink-0 text-[var(--abg-primary-light)]"
                                />
                                <span class="break-all"
                                    >abgprimebuilderssuppliesinc4@gmail.com</span
                                >
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Massive Text -->
                <div
                    class="mb-12 flex w-full justify-center overflow-hidden px-4 py-4"
                >
                    <h2
                        ref="massiveText"
                        class="text-center font-display text-[14.5vw] leading-[0.8] tracking-tighter text-[var(--abg-secondary)] uppercase select-none"
                    >
                        SEE YOU THERE!
                    </h2>
                </div>

                <!-- Bottom Section: Copyright -->
                <div
                    class="border-t border-[var(--abg-secondary)]/10 pt-8 text-center"
                >
                    <p
                        class="font-body text-sm font-medium text-[var(--abg-secondary)]/60 md:text-base"
                    >
                        © 2026 ABG Prime Builders Supplies Inc. - Powered by
                        ABG. Building Better, Together.
                    </p>
                </div>
            </div>
        </div>
    </footer>
</template>
