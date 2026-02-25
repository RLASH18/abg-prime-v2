<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Spinner } from '@/components/ui/spinner';
import AuthBase from '@/layouts/AuthLayout.vue';
import { login } from '@/routes';
import { email } from '@/routes/password';
import { Form, Head, Link } from '@inertiajs/vue3';

defineProps<{
    status?: string;
}>();
</script>

<template>
    <AuthBase
        header="Forgot Password"
        description="Enter your email to receive a password reset link"
    >
        <Head title="Forgot password" />

        <div
            v-if="status"
            class="mb-4 text-center font-body text-sm font-medium text-green-600"
        >
            {{ status }}
        </div>

        <Form
            v-bind="email.form()"
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
                    placeholder="Email Address"
                    class="w-full rounded-full border-[1.5px] border-[var(--abg-primary)] bg-transparent px-6 py-4 font-body text-[var(--abg-primary)] placeholder-[var(--abg-primary)]/60 shadow-sm outline-none focus:ring-2 focus:ring-[var(--abg-primary)]/20"
                />
                <InputError :message="errors.email" class="mt-1 ml-4" />
            </div>

            <!-- Submit Button -->
            <button
                type="submit"
                class="mt-2 flex w-full items-center justify-center rounded-full bg-[var(--abg-primary)] py-4 font-display text-xl font-bold text-[var(--abg-secondary)] shadow-lg transition-all hover:brightness-110 active:scale-[0.98] disabled:opacity-50 disabled:hover:brightness-100"
                :disabled="processing"
                tabindex="2"
            >
                <Spinner v-if="processing" class="mr-2" />
                Email reset link
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

            <!-- Back to Login Link -->
            <Link
                :href="login()"
                class="flex w-full items-center justify-center rounded-full border-[2px] border-[var(--abg-primary)] bg-transparent py-3.5 font-display text-xl font-medium text-[var(--abg-primary)] transition-colors"
                tabindex="3"
            >
                Back to Login
            </Link>

            <!-- Footer Text -->
            <p
                class="mt-4 text-center font-body text-sm leading-relaxed font-medium text-[var(--abg-primary)]/80"
            >
                Remember your password? Go back to login,<br />
                it only takes a second.
            </p>
        </Form>
    </AuthBase>
</template>
