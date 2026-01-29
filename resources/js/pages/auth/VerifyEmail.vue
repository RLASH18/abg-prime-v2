<script setup lang="ts">
import { Spinner } from '@/components/ui/spinner';
import AuthBase from '@/layouts/AuthLayout.vue';
import { logout } from '@/routes';
import { send } from '@/routes/verification';
import { Form, Head, Link } from '@inertiajs/vue3';

defineProps<{
    status?: string;
}>();
</script>

<template>
    <AuthBase
        header="Verify Email"
        description="Please verify your email address by clicking on the link we just emailed to you."
    >
        <Head title="Email verification" />

        <div
            v-if="status === 'verification-link-sent'"
            class="mb-6 text-center font-body text-sm font-medium text-green-600"
        >
            A new verification link has been sent to the email address you
            provided during registration.
        </div>

        <Form
            v-bind="send.form()"
            v-slot="{ processing }"
            class="flex flex-col gap-5"
        >
            <!-- Resend Button -->
            <button
                type="submit"
                class="mt-2 flex w-full items-center justify-center rounded-full bg-[var(--abg-primary)] py-4 font-display text-xl font-bold text-[var(--abg-secondary)] shadow-lg transition-all hover:brightness-110 active:scale-[0.98] disabled:opacity-50 disabled:hover:brightness-100"
                :disabled="processing"
                tabindex="1"
            >
                <Spinner v-if="processing" class="mr-2" />
                Resend verification email
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

            <!-- Logout Link -->
            <Link
                :href="logout()"
                method="post"
                as="button"
                type="button"
                class="flex w-full items-center justify-center rounded-full border-[2px] border-[var(--abg-primary)] bg-transparent py-3.5 font-display text-xl font-medium text-[var(--abg-primary)] transition-colors hover:bg-[var(--abg-primary)]/5"
                tabindex="2"
            >
                Log out
            </Link>

            <!-- Footer Text -->
            <p
                class="mt-4 text-center font-body text-sm leading-relaxed font-medium text-[var(--abg-primary)]/80"
            >
                Need to change your email? Log out and <br />
                re-register with a different one.
            </p>
        </Form>
    </AuthBase>
</template>
