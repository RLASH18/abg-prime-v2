<script setup lang="ts">
import { send } from '@/routes/verification';
import { Form, Head, Link, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

import DeleteUser from '@/components/DeleteUser.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import Select from '@/components/Select.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { useInitials } from '@/composables/useInitials';
import { useSettingsRoutes } from '@/composables/useSettingsRoutes';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem, type UserProfile } from '@/types';
import { Upload } from 'lucide-vue-next';

interface Props {
    mustVerifyEmail: boolean;
    status?: string;
    profile?: UserProfile;
}

const props = defineProps<Props>();

const page = usePage();
const user = page.props.auth.user;

const settingsRoutes = useSettingsRoutes();
const { getInitials } = useInitials();

const genderOptions = [
    { value: 'male', label: 'Male' },
    { value: 'female', label: 'Female' },
];

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Profile settings',
        href: settingsRoutes.value.profile.edit().url,
    },
];

const imagePreview = ref<string | null>(
    props.profile?.profile_picture_url || null,
);
const fileInputRef = ref<HTMLInputElement | null>(null);

const onFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    const file = target.files?.[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (event) => {
            imagePreview.value = event.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

const triggerFileInput = () => {
    fileInputRef.value?.click();
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Profile settings" />

        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <HeadingSmall
                    title="Profile information"
                    description="Update your name and email address"
                />

                <Form
                    v-bind="settingsRoutes.profile.update.form()"
                    class="space-y-6"
                    v-slot="{ errors, processing, recentlySuccessful }"
                >
                    <!-- Profile Picture Section -->
                    <div
                        class="flex items-start gap-6 border-b border-border pb-6"
                    >
                        <div class="group relative">
                            <Avatar class="h-24 w-24 border-2 border-border">
                                <AvatarImage
                                    v-if="imagePreview"
                                    :src="imagePreview"
                                    :alt="user.name"
                                />
                                <AvatarFallback class="text-2xl font-semibold">
                                    {{ getInitials(user.name) }}
                                </AvatarFallback>
                            </Avatar>
                            <div
                                class="absolute inset-0 flex cursor-pointer items-center justify-center rounded-full bg-black/60 opacity-0 transition-opacity group-hover:opacity-100"
                                @click="triggerFileInput"
                            >
                                <Upload class="h-5 w-5 text-white" />
                            </div>
                        </div>

                        <div class="flex-1 space-y-3">
                            <div>
                                <Label class="text-base">Profile Picture</Label>
                                <p class="mt-1 text-sm text-muted-foreground">
                                    Click on the avatar to upload a new picture
                                </p>
                            </div>

                            <div class="flex items-center gap-3">
                                <input
                                    ref="fileInputRef"
                                    id="profile_picture"
                                    type="file"
                                    name="profile_picture"
                                    accept="image/*"
                                    @change="onFileChange"
                                    class="hidden"
                                />
                                <span class="text-xs text-muted-foreground"
                                    >JPG, PNG or GIF (max 2MB)</span
                                >
                            </div>
                            <InputError :message="errors.profile_picture" />
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label for="name">Name</Label>
                        <Input
                            id="name"
                            class="mt-1 block w-full"
                            name="name"
                            :default-value="user.name"
                            required
                            autocomplete="name"
                            placeholder="Full name"
                        />
                        <InputError class="mt-2" :message="errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Email address</Label>
                        <Input
                            id="email"
                            type="email"
                            class="mt-1 block w-full"
                            name="email"
                            :default-value="user.email"
                            required
                            autocomplete="username"
                            placeholder="Email address"
                        />
                        <InputError class="mt-2" :message="errors.email" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="address">Address</Label>
                        <Textarea
                            id="address"
                            class="mt-1 block w-full"
                            name="address"
                            :default-value="profile?.address"
                            placeholder="Your address"
                            rows="3"
                        />
                        <InputError class="mt-2" :message="errors.address" />
                    </div>

                    <!-- Two column grid for profile fields -->
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="contact_number">Contact Number</Label>
                            <Input
                                id="contact_number"
                                type="tel"
                                class="mt-1 block w-full"
                                name="contact_number"
                                :default-value="profile?.contact_number"
                                placeholder="Your contact number"
                            />
                            <InputError
                                class="mt-2"
                                :message="errors.contact_number"
                            />
                        </div>

                        <div class="grid gap-2">
                            <Label for="gender">Gender</Label>
                            <Select
                                id="gender"
                                class="mt-1"
                                name="gender"
                                :model-value="profile?.gender"
                                :options="genderOptions"
                                placeholder="Select gender"
                            />
                            <InputError class="mt-2" :message="errors.gender" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="birth_date">Birth Date</Label>
                            <Input
                                id="birth_date"
                                type="date"
                                class="mt-1 block w-full"
                                name="birth_date"
                                :default-value="profile?.birth_date"
                                placeholder="Birth Date"
                            />
                            <InputError
                                class="mt-2"
                                :message="errors.birth_date"
                            />
                        </div>
                    </div>

                    <div v-if="mustVerifyEmail && !user.email_verified_at">
                        <p class="-mt-4 text-sm text-muted-foreground">
                            Your email address is unverified.
                            <Link
                                :href="send()"
                                as="button"
                                class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                            >
                                Click here to resend the verification email.
                            </Link>
                        </p>

                        <div
                            v-if="status === 'verification-link-sent'"
                            class="mt-2 text-sm font-medium text-green-600"
                        >
                            A new verification link has been sent to your email
                            address.
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <Button
                            :disabled="processing"
                            data-test="update-profile-button"
                            >Save</Button
                        >

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p
                                v-show="recentlySuccessful"
                                class="text-sm text-neutral-600"
                            >
                                Saved.
                            </p>
                        </Transition>
                    </div>
                </Form>
            </div>

            <DeleteUser />
        </SettingsLayout>
    </AppLayout>
</template>
