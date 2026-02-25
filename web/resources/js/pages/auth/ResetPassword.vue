<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Spinner } from '@/components/ui/spinner';
import AuthBase from '@/layouts/AuthLayout.vue';
import { update } from '@/routes/password';
import { Form, Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    token: string;
    email: string;
}>();

const inputEmail = ref(props.email);
</script>

<template>
    <AuthBase
        header="Reset Password"
        description="Please enter your new password below"
    >
        <Head title="Reset password" />

        <Form
            v-bind="update.form()"
            :transform="(data) => ({ ...data, token, email })"
            :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-5"
        >
            <!-- Email Input (Read-only) -->
            <div class="relative">
                <input
                    id="email"
                    type="email"
                    name="email"
                    autocomplete="email"
                    v-model="inputEmail"
                    readonly
                    class="w-full rounded-full border-[1.5px] border-[var(--abg-primary)] bg-[var(--abg-primary)]/5 px-6 py-3.5 font-body text-[var(--abg-primary)] opacity-70 outline-none"
                    placeholder="Email Address"
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
                    autofocus
                    tabindex="1"
                    autocomplete="new-password"
                    placeholder="New Password"
                    class="w-full rounded-full border-[1.5px] border-[var(--abg-primary)] bg-transparent px-6 py-4 font-body text-[var(--abg-primary)] placeholder-[var(--abg-primary)]/60 shadow-sm outline-none focus:ring-2 focus:ring-[var(--abg-primary)]/20"
                />
                <InputError :message="errors.password" class="mt-1 ml-4" />
            </div>

            <!-- Password Confirmation -->
            <div class="relative">
                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    required
                    tabindex="2"
                    autocomplete="new-password"
                    placeholder="Confirm New Password"
                    class="w-full rounded-full border-[1.5px] border-[var(--abg-primary)] bg-transparent px-6 py-4 font-body text-[var(--abg-primary)] placeholder-[var(--abg-primary)]/60 shadow-sm outline-none focus:ring-2 focus:ring-[var(--abg-primary)]/20"
                />
                <InputError
                    :message="errors.password_confirmation"
                    class="mt-1 ml-4"
                />
            </div>

            <!-- Submit Button -->
            <button
                type="submit"
                class="mt-4 flex w-full items-center justify-center rounded-full bg-[var(--abg-primary)] py-4 font-display text-xl font-bold text-[var(--abg-secondary)] shadow-lg transition-all hover:brightness-110 active:scale-[0.98] disabled:opacity-50 disabled:hover:brightness-100"
                :disabled="processing"
                tabindex="3"
            >
                <Spinner v-if="processing" class="mr-2" />
                Reset Password
            </button>

            <!-- Footer Text -->
            <p
                class="mt-4 text-center font-body text-sm leading-relaxed font-medium text-[var(--abg-primary)]/80"
            >
                Secure your account with a strong password.<br />
                Process takes less than a minute.
            </p>
        </Form>
    </AuthBase>
</template>
