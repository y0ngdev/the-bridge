<script setup lang="ts">
import { update, updateAvatar, destroyAvatar } from '@/actions/App/Http/Controllers/Settings/ProfileController';
import { edit } from '@/routes/profile';
import { Form, Head, Link, router, usePage } from '@inertiajs/vue3';

import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/SettingsLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Camera, Trash2 } from 'lucide-vue-next';
import { ref, computed } from 'vue';
import { toast } from 'vue-sonner';

interface Props {
    mustVerifyEmail: boolean;
    status?: string;
}

defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Profile settings',
        href: edit().url,
    },
];

const page = usePage();
const user = computed(() => page.props.auth.user);

// Avatar upload
const avatarInput = ref<HTMLInputElement | null>(null);
const uploading = ref(false);
const avatarError = ref<string | null>(null);

function getInitials(name: string): string {
    return name
        .split(' ')
        .map(n => n[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);
}

function selectAvatar() {
    avatarInput.value?.click();
}

function handleAvatarChange(event: Event) {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    
    if (!file) return;
    
    // Validate file type
    if (!['image/jpeg', 'image/png', 'image/gif', 'image/webp'].includes(file.type)) {
        avatarError.value = 'Please select an image file (JPEG, PNG, GIF, or WebP)';
        return;
    }
    
    // Validate file size (2MB)
    if (file.size > 2 * 1024 * 1024) {
        avatarError.value = 'Image must be less than 2MB';
        return;
    }
    
    avatarError.value = null;
    uploading.value = true;
    
    const formData = new FormData();
    formData.append('avatar', file);
    
    router.post(updateAvatar().url, formData, {
        forceFormData: true,
        onSuccess: () => {
            toast.success('Profile photo updated!');
            uploading.value = false;
            if (avatarInput.value) {
                avatarInput.value.value = '';
            }
        },
        onError: (errors) => {
            avatarError.value = errors.avatar || 'Failed to upload photo';
            uploading.value = false;
        },
    });
}

function removeAvatar() {
    router.delete(destroyAvatar().url, {
        onSuccess: () => {
            toast.success('Profile photo removed!');
        },
    });
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Profile settings" />

        <SettingsLayout>
            <!-- Avatar Section -->
            <div class="flex flex-col space-y-6 mb-8">
                <HeadingSmall title="Profile photo" description="Upload a photo for your profile" />
                
                <div class="flex items-center gap-6">
                    <div class="relative group">
                        <Avatar class="h-24 w-24 text-lg">
                            <AvatarImage :src="user.avatar_url ?? ''" :alt="user.name" />
                            <AvatarFallback>{{ getInitials(user.name) }}</AvatarFallback>
                        </Avatar>
                        <button 
                            @click="selectAvatar"
                            class="absolute inset-0 flex items-center justify-center bg-black/50 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity"
                            :disabled="uploading"
                        >
                            <Camera class="h-6 w-6" />
                        </button>
                    </div>
                    
                    <div class="flex flex-col gap-2">
                        <div class="flex gap-2">
                            <Button 
                                variant="outline" 
                                size="sm"
                                @click="selectAvatar"
                                :disabled="uploading"
                            >
                                <Camera class="mr-2 h-4 w-4" />
                                {{ uploading ? 'Uploading...' : 'Upload photo' }}
                            </Button>
                            <Button 
                                v-if="user.avatar_url"
                                variant="outline" 
                                size="sm"
                                class="text-destructive hover:text-destructive"
                                @click="removeAvatar"
                            >
                                <Trash2 class="mr-2 h-4 w-4" />
                                Remove
                            </Button>
                        </div>
                        <p class="text-xs text-muted-foreground">
                            JPG, PNG, GIF or WebP. Max 2MB.
                        </p>
                        <InputError v-if="avatarError" :message="avatarError" />
                    </div>
                    
                    <input
                        ref="avatarInput"
                        type="file"
                        accept="image/jpeg,image/png,image/gif,image/webp"
                        class="hidden"
                        @change="handleAvatarChange"
                    />
                </div>
            </div>
            
            <hr class="my-6" />
            
            <!-- Profile Info Section -->
            <div class="flex flex-col space-y-6">
                <HeadingSmall title="Profile information" description="Update your name and email address" />

                <Form v-bind="update.form()" class="space-y-6" v-slot="{ errors, processing, recentlySuccessful }">
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

                    <!-- Email verification section - uncomment when verification is enabled in Fortify
                    <div v-if="mustVerifyEmail && !user.email_verified_at">
                        <p class="-mt-4 text-sm text-muted-foreground">
                            Your email address is unverified.
                            <Link
                                :href="verificationNotificationRoute"
                                method="post"
                                as="button"
                                class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                            >
                                Click here to resend the verification email.
                            </Link>
                        </p>

                        <div v-if="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-green-600">
                            A new verification link has been sent to your email address.
                        </div>
                    </div>
                    -->

                    <div class="flex items-center gap-4">
                        <Button :disabled="processing" data-test="update-profile-button">Save</Button>

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p v-show="recentlySuccessful" class="text-sm text-neutral-600">Saved.</p>
                        </Transition>
                    </div>
                </Form>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
