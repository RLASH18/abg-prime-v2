<script setup lang="ts">
import gsap from 'gsap';
import ScrollTrigger from 'gsap/dist/ScrollTrigger';
import { ChevronLeft, ChevronRight, Star } from 'lucide-vue-next';
import { nextTick, onMounted, onUnmounted, ref } from 'vue';

gsap.registerPlugin(ScrollTrigger);

const scrollRef = ref<HTMLDivElement | null>(null);
const sectionHeader = ref<HTMLElement | null>(null);
const isHovering = ref(false);
let autoScrollInterval: number | null = null;
let countdownInterval: number | null = null;

// Countdown timer state
const countdown = ref({
    days: 0,
    hours: 0,
    minutes: 0,
    seconds: 0,
});

// Carousel configuration
const CARD_WIDTH = 300; // Width of each card
const GAP = 32; // Gap between cards (gap-8 = 2rem = 32px)
const SCROLL_AMOUNT = CARD_WIDTH + GAP;
const AUTO_SCROLL_INTERVAL = 5000; // 5 seconds

// Set sale end date (7 days from now as example)
const saleEndDate = new Date();
saleEndDate.setDate(saleEndDate.getDate() + 7);

const updateCountdown = () => {
    const now = new Date().getTime();
    const distance = saleEndDate.getTime() - now;

    if (distance < 0) {
        countdown.value = { days: 0, hours: 0, minutes: 0, seconds: 0 };
        if (countdownInterval) {
            clearInterval(countdownInterval);
        }
        return;
    }

    countdown.value = {
        days: Math.floor(distance / (1000 * 60 * 60 * 24)),
        hours: Math.floor(
            (distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60),
        ),
        minutes: Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60)),
        seconds: Math.floor((distance % (1000 * 60)) / 1000),
    };
};

const startCountdown = () => {
    updateCountdown();
    countdownInterval = window.setInterval(updateCountdown, 1000);
};

const scroll = (direction: 'left' | 'right') => {
    if (!scrollRef.value) return;

    const container = scrollRef.value;
    const scrollAmount = direction === 'left' ? -SCROLL_AMOUNT : SCROLL_AMOUNT;

    container.scrollBy({ left: scrollAmount, behavior: 'smooth' });

    // Handle infinite loop
    setTimeout(() => {
        if (!container) return;

        const maxScroll = container.scrollWidth - container.clientWidth;

        // If scrolled to the end, jump back to start
        if (direction === 'right' && container.scrollLeft >= maxScroll - 10) {
            setTimeout(() => {
                container.scrollTo({ left: 0, behavior: 'smooth' });
            }, 300);
        }

        // If scrolled to the start, jump to end
        if (direction === 'left' && container.scrollLeft <= 10) {
            setTimeout(() => {
                container.scrollTo({ left: maxScroll, behavior: 'smooth' });
            }, 300);
        }
    }, 500);
};

const startAutoScroll = () => {
    if (autoScrollInterval) return;

    autoScrollInterval = window.setInterval(() => {
        if (!isHovering.value) {
            scroll('right');
        }
    }, AUTO_SCROLL_INTERVAL);
};

const stopAutoScroll = () => {
    if (autoScrollInterval) {
        clearInterval(autoScrollInterval);
        autoScrollInterval = null;
    }
};

const handleMouseEnter = () => {
    isHovering.value = true;
};

const handleMouseLeave = () => {
    isHovering.value = false;
};

// Format number with leading zero
const formatTime = (num: number): string => {
    return num.toString().padStart(2, '0');
};

onMounted(() => {
    nextTick(() => {
        // Animate Header
        if (sectionHeader.value) {
            gsap.from(sectionHeader.value, {
                scrollTrigger: {
                    trigger: sectionHeader.value,
                    start: 'top 90%',
                    toggleActions: 'play none none reverse',
                },
                y: 30,
                opacity: 0,
                duration: 0.8,
                ease: 'power3.out',
            });
        }

        // Animate Product Cards Staggered
        gsap.from('.product-card', {
            scrollTrigger: {
                trigger: sectionHeader.value,
                start: 'top 85%',
                toggleActions: 'play none none reverse',
            },
            y: 40,
            opacity: 0,
            duration: 1,
            stagger: 0.1,
            ease: 'power3.out',
            clearProps: 'all',
        });

        // Refresh ScrollTrigger
        ScrollTrigger.refresh();

        // Start countdown timer
        startCountdown();

        // Start auto-scroll after animations
        setTimeout(() => {
            startAutoScroll();
        }, 1000);
    });
});

onUnmounted(() => {
    stopAutoScroll();
    if (countdownInterval) {
        clearInterval(countdownInterval);
    }
});

const products = [
    {
        id: 1,
        name: 'Wokin Rotary Hammer',
        price: '2,280',
        original: '4,560',
        discount: 50,
        sold: 21,
        image: 'https://images.unsplash.com/photo-1586864387967-d02ef85d93e8?q=80&w=400&auto=format&fit=crop',
    },
    {
        id: 2,
        name: 'Pro Cordless Drill',
        price: '3,150',
        original: '4,500',
        discount: 30,
        sold: 45,
        image: 'https://images.unsplash.com/photo-1504148455328-c376907d081c?q=80&w=400&auto=format&fit=crop',
    },
    {
        id: 3,
        name: 'Heavy Duty Sander',
        price: '1,800',
        original: '2,400',
        discount: 25,
        sold: 12,
        image: 'https://images.unsplash.com/photo-1617103996702-96ff29b1c467?q=80&w=400&auto=format&fit=crop',
    },
    {
        id: 4,
        name: 'Impact Wrench Set',
        price: '5,200',
        original: '6,500',
        discount: 20,
        sold: 8,
        image: 'https://images.unsplash.com/photo-1530124566582-a618bc2615dc?q=80&w=400&auto=format&fit=crop',
    },
    {
        id: 5,
        name: 'Precision Jigsaw',
        price: '2,990',
        original: '3,800',
        discount: 21,
        sold: 34,
        image: 'https://images.unsplash.com/photo-1572981779307-38b8cabb2407?q=80&w=400&auto=format&fit=crop',
    },
    {
        id: 6,
        name: 'Industrial Grinder',
        price: '4,100',
        original: '5,125',
        discount: 20,
        sold: 15,
        image: 'https://images.unsplash.com/photo-1566937169390-7be4c63b8a0e?q=80&w=400&auto=format&fit=crop',
    },
    {
        id: 7,
        name: 'Laser Level Kit',
        price: '1,550',
        original: '3,100',
        discount: 50,
        sold: 67,
        image: 'https://images.unsplash.com/photo-1581244277943-fe4a9c777189?q=80&w=400&auto=format&fit=crop',
    },
];
</script>

<template>
    <section id="products" class="w-full overflow-hidden bg-white py-24">
        <div class="mx-auto flex max-w-7xl flex-col gap-12 px-6 md:px-12">
            <!-- Header Section -->
            <div
                ref="sectionHeader"
                class="flex flex-col items-center justify-between gap-8 border-b border-[var(--abg-primary)]/10 pb-10 md:flex-row md:gap-0"
            >
                <div
                    class="flex flex-col items-center gap-8 md:flex-row md:gap-14"
                >
                    <h2
                        class="font-display text-5xl tracking-wide text-[var(--abg-primary)] uppercase"
                    >
                        SALE NOW
                    </h2>

                    <div class="flex items-center gap-4">
                        <span
                            class="font-body text-xs font-black tracking-widest text-[var(--abg-primary)]/60 uppercase"
                            >Ending in</span
                        >
                        <div
                            class="flex gap-2 font-display text-2xl text-white"
                        >
                            <div
                                class="rounded-lg bg-[var(--abg-primary)] px-4 py-1.5 shadow-md"
                            >
                                {{ formatTime(countdown.days) }}
                            </div>
                            <div
                                class="py-1.5 font-bold text-[var(--abg-primary)]"
                            >
                                :
                            </div>
                            <div
                                class="rounded-lg bg-[var(--abg-primary)] px-4 py-1.5 shadow-md"
                            >
                                {{ formatTime(countdown.hours) }}
                            </div>
                            <div
                                class="py-1.5 font-bold text-[var(--abg-primary)]"
                            >
                                :
                            </div>
                            <div
                                class="rounded-lg bg-[var(--abg-primary)] px-4 py-1.5 shadow-md"
                            >
                                {{ formatTime(countdown.minutes) }}
                            </div>
                            <div
                                class="py-1.5 font-bold text-[var(--abg-primary)]"
                            >
                                :
                            </div>
                            <div
                                class="rounded-lg bg-[var(--abg-primary)] px-4 py-1.5 shadow-md"
                            >
                                {{ formatTime(countdown.seconds) }}
                            </div>
                        </div>
                    </div>
                </div>

                <button
                    class="rounded-full border-2 border-[var(--abg-primary)] px-10 py-3 font-body text-xs font-black tracking-[0.2em] text-[var(--abg-primary)] uppercase transition-all duration-300 hover:bg-[var(--abg-primary)] hover:text-white"
                >
                    More Deals
                </button>
            </div>

            <!-- Carousel Section -->
            <div class="group relative">
                <!-- Left Arrow -->
                <button
                    @click="scroll('left')"
                    class="absolute top-1/2 -left-4 z-20 hidden h-14 w-14 -translate-y-1/2 items-center justify-center rounded-full border border-gray-100 bg-white text-[var(--abg-primary)] opacity-0 shadow-2xl transition-all duration-300 group-hover:opacity-100 hover:scale-110 hover:bg-[var(--abg-primary)] hover:text-white md:-left-8 md:flex"
                >
                    <ChevronLeft :stroke-width="3" class="ml-[-2px] h-6 w-6" />
                </button>

                <!-- Product List -->
                <div
                    ref="scrollRef"
                    @mouseenter="handleMouseEnter"
                    @mouseleave="handleMouseLeave"
                    class="scrollbar-none -mx-6 flex snap-x snap-mandatory gap-8 overflow-x-auto scroll-smooth px-6 pb-12"
                    style="scrollbar-width: none; -ms-overflow-style: none"
                >
                    <div
                        v-for="product in products"
                        :key="product.id"
                        class="product-card group/card w-[300px] flex-shrink-0 snap-center rounded-[2rem] border border-gray-50 bg-white p-5 shadow-sm transition-all duration-500 hover:shadow-2xl"
                    >
                        <!-- Image Container -->
                        <div
                            class="relative mb-6 h-[240px] w-full overflow-hidden rounded-3xl bg-[var(--abg-secondary)]/40"
                        >
                            <img
                                :src="product.image"
                                :alt="product.name"
                                class="h-full w-full object-cover opacity-90 mix-blend-multiply transition-transform duration-700 ease-out group-hover/card:scale-110"
                            />
                            <div
                                class="absolute top-3 right-3 rounded-full bg-[var(--abg-primary)] px-3 py-1.5 font-body text-[10px] font-black tracking-wider text-white uppercase shadow-lg"
                            >
                                {{ product.discount }}% OFF
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="flex flex-col gap-3 px-1">
                            <div class="flex items-baseline gap-3">
                                <span
                                    class="font-display text-3xl text-[var(--abg-primary)]"
                                    >PHP {{ product.price }}</span
                                >
                                <span
                                    class="font-body text-sm font-bold text-[var(--abg-primary)]/30 line-through"
                                    >₱{{ product.original }}</span
                                >
                            </div>

                            <h3
                                class="line-clamp-2 h-14 font-body text-lg leading-tight font-black tracking-tight text-[var(--abg-primary)] uppercase"
                            >
                                {{ product.name }}
                            </h3>

                            <div class="mt-3 flex items-center justify-between">
                                <div class="flex items-center gap-1.5">
                                    <Star
                                        v-for="i in 5"
                                        :key="i"
                                        :class="[
                                            'h-3.5 w-3.5',
                                            i <= 4
                                                ? 'fill-[var(--abg-primary)] text-[var(--abg-primary)]'
                                                : 'fill-gray-200 text-gray-200',
                                        ]"
                                    />
                                </div>
                                <span
                                    class="font-body text-[10px] font-black tracking-widest text-nowrap text-[var(--abg-primary)]/50 uppercase"
                                >
                                    {{ product.sold }} sold
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Arrow -->
                <button
                    @click="scroll('right')"
                    class="absolute top-1/2 -right-4 z-20 hidden h-14 w-14 -translate-y-1/2 items-center justify-center rounded-full border border-gray-100 bg-white text-[var(--abg-primary)] opacity-0 shadow-2xl transition-all duration-300 group-hover:opacity-100 hover:scale-110 hover:bg-[var(--abg-primary)] hover:text-white md:-right-8 md:flex"
                >
                    <ChevronRight :stroke-width="3" class="mr-[-2px] h-6 w-6" />
                </button>
            </div>

            <!-- Bottom Button -->
            <div class="mt-6 flex justify-center">
                <button
                    class="group relative overflow-hidden rounded-lg bg-[var(--abg-primary)] px-14 py-5 font-body text-sm font-black tracking-[0.2em] text-white uppercase shadow-2xl transition-all duration-300 hover:-translate-y-1 hover:shadow-[var(--abg-primary)]/40"
                >
                    <span class="relative z-10">See More Products</span>
                    <div
                        class="absolute inset-0 bg-white/10 opacity-0 transition-opacity duration-300 group-hover:opacity-100"
                    ></div>
                </button>
            </div>
        </div>
    </section>
</template>

<style scoped>
.scrollbar-none::-webkit-scrollbar {
    display: none;
}
</style>
