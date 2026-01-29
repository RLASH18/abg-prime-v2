<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import {
    PinInput,
    PinInputGroup,
    PinInputSlot,
} from '@/components/ui/pin-input';
import { Spinner } from '@/components/ui/spinner';
import AuthBase from '@/layouts/AuthLayout.vue';
import { store } from '@/routes/two-factor/login';
import { Form, Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface AuthConfigContent {
    title: string;
    description: string;
    toggleText: string;
}

const authConfigContent = computed<AuthConfigContent>(() => {
    if (showRecoveryInput.value) {
        return {
            title: 'Recovery Code',
            description:
                'Please confirm access to your account by entering one of your emergency recovery codes.',
            toggleText: 'Use an authentication code',
        };
    }

    return {
        title: '2FA Code',
        description:
            'Enter the authentication code provided by your authenticator application.',
        toggleText: 'Use a recovery code',
    };
});

const showRecoveryInput = ref<boolean>(false);

const toggleRecoveryMode = (clearErrors: () => void): void => {
    showRecoveryInput.value = !showRecoveryInput.value;
    clearErrors();
    code.value = [];
};

const code = ref<number[]>([]);
const codeValue = computed<string>(() => code.value.join(''));
</script>

<template>
    <AuthBase
        :header="authConfigContent.title"
        :description="authConfigContent.description"
    >
        <Head title="Two-Factor Authentication" />

        <div class="space-y-6">
            <template v-if="!showRecoveryInput">
                <Form
                    v-bind="store.form()"
                    class="flex flex-col gap-5"
                    reset-on-error
                    @error="code = []"
                    #default="{ errors, processing, clearErrors }"
                >
                    <input type="hidden" name="code" :value="codeValue" />

                    <div
                        class="flex flex-col items-center justify-center space-y-3 text-center"
                    >
                        <div class="flex w-full items-center justify-center">
                            <PinInput
                                id="otp"
                                placeholder="○"
                                v-model="code"
                                type="number"
                                otp
                            >
                                <PinInputGroup class="gap-2">
                                    <PinInputSlot
                                        v-for="(id, index) in 6"
                                        :key="id"
                                        :index="index"
                                        :disabled="processing"
                                        class="size-12 rounded-xl border-2 border-[var(--abg-primary)] bg-transparent text-xl font-bold text-[var(--abg-primary)] shadow-sm focus:ring-2 focus:ring-[var(--abg-primary)]/20"
                                        autofocus
                                    />
                                </PinInputGroup>
                            </PinInput>
                        </div>
                        <InputError :message="errors.code" />
                    </div>

                    <button
                        type="submit"
                        class="mt-2 flex w-full items-center justify-center rounded-full bg-[var(--abg-primary)] py-4 font-display text-xl font-bold text-[var(--abg-secondary)] shadow-lg transition-all hover:brightness-110 active:scale-[0.98] disabled:opacity-50 disabled:hover:brightness-100"
                        :disabled="processing"
                    >
                        <Spinner v-if="processing" class="mr-2" />
                        Continue
                    </button>

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

                    <button
                        type="button"
                        class="flex w-full items-center justify-center rounded-full border-[2px] border-[var(--abg-primary)] bg-transparent py-3.5 font-display text-xl font-medium text-[var(--abg-primary)] transition-colors hover:bg-[var(--abg-primary)]/5"
                        @click="() => toggleRecoveryMode(clearErrors)"
                    >
                        {{ authConfigContent.toggleText }}
                    </button>
                </Form>
            </template>

            <template v-else>
                <Form
                    v-bind="store.form()"
                    class="flex flex-col gap-5"
                    reset-on-error
                    #default="{ errors, processing, clearErrors }"
                >
                    <div class="relative">
                        <input
                            name="recovery_code"
                            type="text"
                            placeholder="Recovery Code"
                            :autofocus="showRecoveryInput"
                            required
                            class="w-full rounded-full border-[1.5px] border-[var(--abg-primary)] bg-transparent px-6 py-4 font-body text-[var(--abg-primary)] placeholder-[var(--abg-primary)]/60 shadow-sm outline-none focus:ring-2 focus:ring-[var(--abg-primary)]/20"
                        />
                        <InputError
                            :message="errors.recovery_code"
                            class="mt-1 ml-4"
                        />
                    </div>

                    <button
                        type="submit"
                        class="mt-2 flex w-full items-center justify-center rounded-full bg-[var(--abg-primary)] py-4 font-display text-xl font-bold text-[var(--abg-secondary)] shadow-lg transition-all hover:brightness-110 active:scale-[0.98] disabled:opacity-50 disabled:hover:brightness-100"
                        :disabled="processing"
                    >
                        <Spinner v-if="processing" class="mr-2" />
                        Continue
                    </button>

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

                    <button
                        type="button"
                        class="flex w-full items-center justify-center rounded-full border-[2px] border-[var(--abg-primary)] bg-transparent py-3.5 font-display text-xl font-medium text-[var(--abg-primary)] transition-colors hover:bg-[var(--abg-primary)]/5"
                        @click="() => toggleRecoveryMode(clearErrors)"
                    >
                        {{ authConfigContent.toggleText }}
                    </button>
                </Form>
            </template>
        </div>
    </AuthBase>
</template>
