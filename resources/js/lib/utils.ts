import { InertiaLinkProps } from '@inertiajs/vue3';
import type { ClassValue } from 'clsx';
import { clsx } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function urlIsActive(urlToCheck: NonNullable<InertiaLinkProps['href']>, currentUrl: string) {
    const urlPath = toUrl(urlToCheck).split('?')[0];
    const currentPath = currentUrl.split('?')[0];
    // Exact match or prefix match for parent routes (e.g., /redemption-week matches /redemption-week/1)
    return currentPath === urlPath || currentPath.startsWith(urlPath + '/');
}

export function toUrl(href: NonNullable<InertiaLinkProps['href']>) {
    return typeof href === 'string' ? href : href?.url;
}
