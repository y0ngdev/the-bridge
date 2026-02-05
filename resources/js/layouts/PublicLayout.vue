<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Toaster } from 'vue-sonner';
import AppLogo from '@/components/AppLogo.vue';

defineProps<{
    title?: string;
}>();
</script>

<template>
    <div class="min-h-screen bg-background flex flex-col font-sans antialiased">
        <Toaster richColors position="top-right" />
        
        <!-- Main Content -->
        <main class="flex-1">
            <div class="container mx-auto px-4 py-8 md:py-12">
                <slot />
            </div>
        </main>

        <!-- Footer -->
        <footer class="border-t bg-card py-6">
            <div class="container mx-auto px-4 text-center text-sm text-muted-foreground">
                <p>&copy; {{ new Date().getFullYear() }} Alumni Relations Unit. All rights reserved.</p>
            </div>
        </footer>
    </div>
</template>
