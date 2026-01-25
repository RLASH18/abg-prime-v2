<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import OAuthButtons from '@/components/OAuthButtons.vue';
import { Checkbox } from '@/components/ui/checkbox';
import { Spinner } from '@/components/ui/spinner';
import AuthBase from '@/layouts/AuthLayout.vue';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';
import { Form, Head, Link } from '@inertiajs/vue3';

defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}>();
</script>

<template>
    <AuthBase
        header="Login"
        description="Welcome back to ABG Prime Builders Supplies Inc."
    >
        <Head title="Log in" />

        <div
            v-if="status"
            class="mb-4 text-center font-body text-sm font-medium text-green-600"
        >
            {{ status }}
        </div>

        <Form
            v-bind="store.form()"
            :reset-on-success="['password']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-5"
        >
            <!-- Email Input -->
            <div class="relative">
                <input
                    id="email"
                    type="email"
                    name="email"
                    required
                    autofocus
                    tabindex="1"
                    autocomplete="email"
                    placeholder="Email"
                    class="w-full rounded-full border-[1.5px] border-[var(--abg-primary)] bg-transparent px-6 py-4 font-body text-[var(--abg-primary)] placeholder-[var(--abg-primary)]/60 shadow-sm outline-none focus:ring-2 focus:ring-[var(--abg-primary)]/20"
                />
                <InputError :message="errors.email" class="mt-1 ml-4" />
            </div>

            <!-- Password Input -->
            <div class="relative">
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    tabindex="2"
                    autocomplete="current-password"
                    placeholder="Password"
                    class="w-full rounded-full border-[1.5px] border-[var(--abg-primary)] bg-transparent px-6 py-4 font-body text-[var(--abg-primary)] placeholder-[var(--abg-primary)]/60 shadow-sm outline-none focus:ring-2 focus:ring-[var(--abg-primary)]/20"
                />
                <InputError :message="errors.password" class="mt-1 ml-4" />
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between pl-2">
                <div class="flex items-center gap-3">
                    <Checkbox
                        id="remember"
                        name="remember"
                        tabindex="3"
                        class="border-[var(--abg-primary)] data-[state=checked]:border-[var(--abg-primary)] data-[state=checked]:bg-[var(--abg-primary)]"
                    />
                    <label
                        htmlFor="remember"
                        class="cursor-pointer font-body font-medium text-[var(--abg-primary)] select-none"
                    >
                        Remember me
                    </label>
                </div>
                <Link
                    v-if="canResetPassword"
                    :href="request()"
                    class="font-body text-sm font-bold text-[var(--abg-primary)] hover:underline"
                    tabindex="5"
                >
                    Forgot Password?
                </Link>
            </div>

            <!-- Login Button -->
            <button
                type="submit"
                class="mt-2 flex w-full items-center justify-center rounded-full bg-[var(--abg-primary)] py-4 font-display text-xl font-bold text-[var(--abg-secondary)] shadow-lg transition-all hover:brightness-110 active:scale-[0.98] disabled:opacity-50 disabled:hover:brightness-100"
                :disabled="processing"
                tabindex="4"
            >
                <Spinner v-if="processing" class="mr-2" />
                Login
            </button>

            <!-- OAuth Buttons -->
            <OAuthButtons />

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

            <!-- Register Link -->
            <Link
                v-if="canRegister"
                :href="register()"
                class="flex w-full items-center justify-center rounded-full border-[2px] border-[var(--abg-primary)] bg-transparent py-3.5 font-display text-xl font-medium text-[var(--abg-primary)] transition-colors"
                tabindex="6"
            >
                Register
            </Link>

            <!-- Footer Text -->
            <p
                v-if="canRegister"
                class="mt-4 text-center font-body text-sm leading-relaxed font-medium text-[var(--abg-primary)]/80"
            >
                Don't have an account? Create your account,<br />
                it takes less than a minute.
            </p>
        </Form>
    </AuthBase>
</template>
