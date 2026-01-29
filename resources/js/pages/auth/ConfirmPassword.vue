<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Spinner } from '@/components/ui/spinner';
import AuthBase from '@/layouts/AuthLayout.vue';
import { store } from '@/routes/password/confirm';
import { Form, Head } from '@inertiajs/vue3';
</script>

<template>
    <AuthBase
        header="Confirm Password"
        description="This is a secure area of the application. Please confirm your password before continuing."
    >
        <Head title="Confirm password" />

        <Form
            v-bind="store.form()"
            reset-on-success
            v-slot="{ errors, processing }"
            class="flex flex-col gap-5"
        >
            <!-- Password Input -->
            <div class="relative">
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autofocus
                    tabindex="1"
                    autocomplete="current-password"
                    placeholder="Enter Password"
                    class="w-full rounded-full border-[1.5px] border-[var(--abg-primary)] bg-transparent px-6 py-4 font-body text-[var(--abg-primary)] placeholder-[var(--abg-primary)]/60 shadow-sm outline-none focus:ring-2 focus:ring-[var(--abg-primary)]/20"
                />
                <InputError :message="errors.password" class="mt-1 ml-4" />
            </div>

            <!-- Confirm Button -->
            <button
                type="submit"
                class="mt-2 flex w-full items-center justify-center rounded-full bg-[var(--abg-primary)] py-4 font-display text-xl font-bold text-[var(--abg-secondary)] shadow-lg transition-all hover:brightness-110 active:scale-[0.98] disabled:opacity-50 disabled:hover:brightness-100"
                :disabled="processing"
                tabindex="2"
            >
                <Spinner v-if="processing" class="mr-2" />
                Confirm Password
            </button>

            <!-- Footer Text -->
            <p
                class="mt-4 text-center font-body text-sm leading-relaxed font-medium text-[var(--abg-primary)]/80"
            >
                For your security, please verify your identity.<br />
                It only takes a second.
            </p>
        </Form>
    </AuthBase>
</template>
