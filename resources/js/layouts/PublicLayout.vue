<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { initializeTheme } from '@/composables/useAppearance';
import { Link } from '@inertiajs/vue3';
import { onMounted, onUnmounted } from 'vue';
import { Toaster } from 'vue-sonner';

defineProps<{
    title?: string;
}>();

onMounted(() => {
    document.documentElement.classList.remove('dark');
});

onUnmounted(() => {
    initializeTheme();
});
</script>

<template>
    <div class="flex min-h-screen flex-col bg-background font-sans antialiased">
        <Toaster richColors position="top-right" />

        <!-- Header -->
        <header class="sticky top-0 z-50 border-b bg-background/95 backdrop-blur supports-backdrop-filter:bg-background/60">
            <div class="container mx-auto flex items-center justify-between px-4 py-3">
                <Link href="/" class="flex items-center gap-2 md:gap-3">
                    <div class="flex items-center gap-1 md:gap-2">
                        <img src="/img/rcf futa logo.png" alt="RCF FUTA" class="h-8 w-8 object-contain md:h-12 md:w-12" />
                        <img src="/img/rcf alumni logo.png" alt="RCF Alumni" class="h-8 w-8 object-contain md:h-12 md:w-12" />
                        <img src="/img/rcf logo.png" alt="RCF Logo" class="h-8 w-8 object-contain md:h-12 md:w-12" />
                    </div>
                    <span class="text-sm font-bold tracking-tight text-foreground md:text-xl">The Bridge</span>
                </Link>
                <nav class="flex items-center gap-4">
                    <Button asChild variant="default" size="sm">
                        <Link href="/portal">Alumni Portal</Link>
                    </Button>
                </nav>
            </div>
        </header>
        <main class="flex-1">
            <div class="container mx-auto px-4 py-8 md:py-12">
                <slot />
            </div>
        </main>

        <!-- Footer -->
        <footer class="border-t bg-card py-6">
            <div class="container mx-auto px-4 text-center text-sm text-muted-foreground">
                <p>&copy; {{ new Date().getFullYear() }} Brought to you by Alumni Relations Unit. Powered by RCF FUTA ICT Team.</p>
            </div>
        </footer>
    </div>
</template>
