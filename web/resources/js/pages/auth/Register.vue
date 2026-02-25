<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Spinner } from '@/components/ui/spinner';
import AuthBase from '@/layouts/AuthLayout.vue';
import { login } from '@/routes';
import { store } from '@/routes/register';
import { Form, Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <AuthBase
        header="Register"
        description="Join ABG Prime Builders Supplies Inc. today."
    >
        <Head title="Register" />

        <Form
            v-bind="store.form()"
            :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-5"
        >
            <!-- Name Input -->
            <div class="relative">
                <input
                    id="name"
                    type="text"
                    required
                    autofocus
                    tabindex="1"
                    autocomplete="name"
                    name="name"
                    placeholder="Full Name"
                    class="w-full rounded-full border-[1.5px] border-[var(--abg-primary)] bg-transparent px-6 py-3.5 font-body text-[var(--abg-primary)] placeholder-[var(--abg-primary)]/60 shadow-sm outline-none focus:ring-2 focus:ring-[var(--abg-primary)]/20"
                />
                <InputError :message="errors.name" class="mt-1 ml-4" />
            </div>

            <!-- Email Input -->
            <div class="relative">
                <input
                    id="email"
                    type="email"
                    required
                    tabindex="2"
                    autocomplete="email"
                    name="email"
                    placeholder="Email Address"
                    class="w-full rounded-full border-[1.5px] border-[var(--abg-primary)] bg-transparent px-6 py-3.5 font-body text-[var(--abg-primary)] placeholder-[var(--abg-primary)]/60 shadow-sm outline-none focus:ring-2 focus:ring-[var(--abg-primary)]/20"
                />
                <InputError :message="errors.email" class="mt-1 ml-4" />
            </div>

            <!-- Password Input -->
            <div class="relative">
                <input
                    id="password"
                    type="password"
                    required
                    tabindex="3"
                    autocomplete="new-password"
                    name="password"
                    placeholder="Password"
                    class="w-full rounded-full border-[1.5px] border-[var(--abg-primary)] bg-transparent px-6 py-3.5 font-body text-[var(--abg-primary)] placeholder-[var(--abg-primary)]/60 shadow-sm outline-none focus:ring-2 focus:ring-[var(--abg-primary)]/20"
                />
                <InputError :message="errors.password" class="mt-1 ml-4" />
            </div>

            <!-- Password Confirmation -->
            <div class="relative">
                <input
                    id="password_confirmation"
                    type="password"
                    required
                    tabindex="4"
                    autocomplete="new-password"
                    name="password_confirmation"
                    placeholder="Confirm Password"
                    class="w-full rounded-full border-[1.5px] border-[var(--abg-primary)] bg-transparent px-6 py-3.5 font-body text-[var(--abg-primary)] placeholder-[var(--abg-primary)]/60 shadow-sm outline-none focus:ring-2 focus:ring-[var(--abg-primary)]/20"
                />
                <InputError
                    :message="errors.password_confirmation"
                    class="mt-1 ml-4"
                />
            </div>

            <!-- Register Button -->
            <button
                type="submit"
                class="mt-2 flex w-full items-center justify-center rounded-full bg-[var(--abg-primary)] py-3.5 font-display text-xl font-bold text-[var(--abg-secondary)] shadow-lg transition-all hover:brightness-110 active:scale-[0.98] disabled:opacity-50 disabled:hover:brightness-100"
                :disabled="processing"
                tabindex="5"
            >
                <Spinner v-if="processing" class="mr-2" />
                Register
            </button>

            <!-- Divider -->
            <div class="relative my-2 flex items-center justify-center">
                <div class="absolute inset-0 flex items-center">
                    <div
                        class="w-full border-t-[3px] border-[var(--abg-primary)]/40"
                    ></div>
                </div>
                <div
                    class="relative bg-[var(--abg-secondary)] px-4 font-display text-xl text-[var(--abg-primary)]/60"
                >
                    OR
                </div>
            </div>

            <!-- Login Link -->
            <Link
                :href="login()"
                class="flex w-full items-center justify-center rounded-full border-[2px] border-[var(--abg-primary)] bg-transparent py-3 font-display text-xl font-medium text-[var(--abg-primary)] transition-colors hover:bg-[var(--abg-primary)]/5"
                tabindex="6"
            >
                Login
            </Link>

            <!-- Footer Text -->
            <p
                class="mt-4 text-center font-body text-sm leading-relaxed font-medium text-[var(--abg-primary)]/80"
            >
                Already have an account? Sign in to your account,<br />
                it takes less than a minute.
            </p>
        </Form>
    </AuthBase>
</template>
