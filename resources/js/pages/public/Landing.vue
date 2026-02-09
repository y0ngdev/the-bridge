<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowRight, Calendar, Globe, Heart, Shield, Sparkles, Users } from 'lucide-vue-next';

interface Props {
    stats: {
        totalAlumni: number;
        totalTenures: number;
        activeYear: string;
    };
}

defineProps<Props>();
</script>

<template>
    <PublicLayout>
        <Head title="Welcome Home" />

        <div class="flex flex-col">
            <!-- Hero Section -->
            <section class="relative overflow-hidden py-24 md:py-36">
                <!-- Warm background gradient -->
                <div class="absolute inset-0 -z-10 bg-linear-to-br from-amber-500/5 via-transparent to-primary/5"></div>
                <div
                    class="absolute top-1/4 left-1/4 -z-10 h-[500px] w-[500px] rounded-full bg-amber-500/5 blur-[100px]"
                ></div>
                <div
                    class="absolute right-1/4 bottom-1/4 -z-10 h-[400px] w-[400px] rounded-full bg-primary/5 blur-[100px]"
                ></div>

                <div class="container mx-auto px-4 text-center">
                    <div class="mx-auto max-w-4xl space-y-8">
                        <!-- Welcome badge -->
                        <div
                            class="inline-flex items-center gap-2 rounded-full border border-amber-500/20 bg-amber-500/10 px-5 py-2 text-sm text-amber-600 dark:text-amber-400"
                        >
                            <Heart class="h-4 w-4 fill-current" />
                            <span>Welcome Home, Family</span>
                        </div>

                        <!-- Headline -->
                        <h1 class="text-4xl font-extrabold leading-tight tracking-tight md:text-6xl lg:text-7xl">
                            <span class="block text-foreground">Remember Those</span>
                            <span
                                class="bg-linear-to-r from-amber-500 via-primary to-blue-500 bg-clip-text text-transparent"
                            >
                                Beautiful Days?
                            </span>
                        </h1>

                        <!-- Emotional tagline -->
                        <p class="mx-auto max-w-2xl text-lg leading-relaxed text-muted-foreground md:text-xl">
                            The late-night fellowship meetings. The songs we sang together. The friends who became family.
                            <span class="font-medium text-foreground">The Bridge</span>
                            keeps those connections alive — no matter how far life has taken you.
                        </p>

                        <!-- CTA Buttons -->
                        <div class="flex flex-col items-center justify-center gap-4 pt-6 sm:flex-row">
                            <Button asChild size="lg" class="gap-2 px-8 text-base">
                                <Link href="/portal">
                                    Find My Record
                                    <ArrowRight class="h-4 w-4" />
                                </Link>
                            </Button>
                            <Button asChild variant="ghost" size="lg" class="gap-2 text-base text-muted-foreground">
                                <a href="#memories">
                                    <Sparkles class="h-4 w-4" />
                                    Explore
                                </a>
                            </Button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Stats Section - warmer design -->
            <section class="border-y border-border/50 bg-linear-to-r from-amber-500/5 via-muted/30 to-primary/5 py-16">
                <div class="container mx-auto px-4">
                    <div class="grid grid-cols-1 gap-8 sm:grid-cols-3">
                        <div class="text-center">
                            <div class="text-4xl font-bold text-amber-600 dark:text-amber-400 md:text-5xl">
                                {{ stats.totalAlumni.toLocaleString() }}+
                            </div>
                            <p class="mt-2 text-muted-foreground">Alumni Connected</p>
                        </div>
                        <div class="text-center">
                            <div class="text-4xl font-bold text-primary md:text-5xl">{{ stats.totalTenures }}</div>
                            <p class="mt-2 text-muted-foreground">Generations Strong</p>
                        </div>
                        <div class="text-center">
                            <div class="text-4xl font-bold text-blue-500 md:text-5xl">{{ stats.activeYear }}+</div>
                            <p class="mt-2 text-muted-foreground">Years of Fellowship</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Memories/About Section -->
            <section id="memories" class="py-24">
                <div class="container mx-auto px-4">
                    <div class="mx-auto max-w-3xl text-center">
                        <div class="mb-6 inline-flex items-center gap-2 text-amber-600 dark:text-amber-400">
                            <Sparkles class="h-5 w-5" />
                            <span class="text-sm font-medium uppercase tracking-wider">Our Story</span>
                        </div>
                        <h2 class="text-3xl font-bold md:text-4xl">More Than an Alumni Network</h2>
                        <p class="mt-6 text-lg leading-relaxed text-muted-foreground">
                            From prayer meetings in small rooms to graduation ceremonies on the big stage — RCFFUTA has always
                            been about family. Now, no matter where you are in the world,
                            <span class="text-foreground">The Bridge</span> brings us back together. Update your details.
                            Find old friends. Relive the memories.
                        </p>
                    </div>
                </div>
            </section>

            <!-- Features Section - warmer cards -->
            <section class="bg-muted/20 py-24">
                <div class="container mx-auto px-4">
                    <h2 class="mb-4 text-center text-3xl font-bold md:text-4xl">Stay Connected</h2>
                    <p class="mx-auto mb-12 max-w-xl text-center text-muted-foreground">
                        Whether you graduated last year or decades ago, there's a place for you here.
                    </p>

                    <div class="mx-auto grid max-w-5xl grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                        <Card class="group border-none bg-card/50 shadow-lg transition-all hover:-translate-y-1 hover:shadow-xl">
                            <CardContent class="flex flex-col items-center p-6 text-center">
                                <div class="mb-4 rounded-full bg-amber-500/10 p-4 transition-colors group-hover:bg-amber-500/20">
                                    <Users class="h-6 w-6 text-amber-600 dark:text-amber-400" />
                                </div>
                                <h3 class="mb-2 font-semibold">Update Profile</h3>
                                <p class="text-sm text-muted-foreground">Keep your info current so friends can find you.</p>
                            </CardContent>
                        </Card>

                        <Card class="group border-none bg-card/50 shadow-lg transition-all hover:-translate-y-1 hover:shadow-xl">
                            <CardContent class="flex flex-col items-center p-6 text-center">
                                <div class="mb-4 rounded-full bg-primary/10 p-4 transition-colors group-hover:bg-primary/20">
                                    <Globe class="h-6 w-6 text-primary" />
                                </div>
                                <h3 class="mb-2 font-semibold">Find Friends</h3>
                                <p class="text-sm text-muted-foreground">Reconnect with batchmates across the globe.</p>
                            </CardContent>
                        </Card>

                        <Card class="group border-none bg-card/50 shadow-lg transition-all hover:-translate-y-1 hover:shadow-xl">
                            <CardContent class="flex flex-col items-center p-6 text-center">
                                <div class="mb-4 rounded-full bg-blue-500/10 p-4 transition-colors group-hover:bg-blue-500/20">
                                    <Calendar class="h-6 w-6 text-blue-500" />
                                </div>
                                <h3 class="mb-2 font-semibold">Join Events</h3>
                                <p class="text-sm text-muted-foreground">Reunions, Redemption Week, and more await.</p>
                            </CardContent>
                        </Card>

                        <Card class="group border-none bg-card/50 shadow-lg transition-all hover:-translate-y-1 hover:shadow-xl">
                            <CardContent class="flex flex-col items-center p-6 text-center">
                                <div class="mb-4 rounded-full bg-green-500/10 p-4 transition-colors group-hover:bg-green-500/20">
                                    <Shield class="h-6 w-6 text-green-500" />
                                </div>
                                <h3 class="mb-2 font-semibold">Safe & Secure</h3>
                                <p class="text-sm text-muted-foreground">Your data stays protected, always.</p>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </section>

            <!-- Final CTA Section - emotional appeal -->
            <section class="py-24">
                <div class="container mx-auto px-4">
                    <div
                        class="mx-auto max-w-3xl overflow-hidden rounded-3xl bg-linear-to-br from-amber-500/10 via-primary/5 to-blue-500/10 p-10 text-center md:p-14"
                    >
                        <div class="mb-4 inline-flex items-center gap-2 text-amber-600 dark:text-amber-400">
                            <Heart class="h-5 w-5 fill-current" />
                        </div>
                        <h2 class="text-2xl font-bold md:text-3xl">Once RCFFUTA, Always Family</h2>
                        <p class="mx-auto mt-4 max-w-xl text-muted-foreground">
                            Take a moment to update your records. It's how we stay connected. It's how we remember. It's how
                            we keep the bridge strong.
                        </p>
                        <Button asChild size="lg" class="mt-8 gap-2 px-8">
                            <Link href="/portal">
                                Update My Profile
                                <ArrowRight class="h-4 w-4" />
                            </Link>
                        </Button>
                    </div>
                </div>
            </section>
        </div>
    </PublicLayout>
</template>
