import { InertiaLinkProps } from '@inertiajs/vue3';
import type { ClassValue } from 'clsx';
import { clsx } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function urlIsActive(urlToCheck: NonNullable<InertiaLinkProps['href']>, currentUrl: string, exact: boolean = true) {
    const urlPath = toUrl(urlToCheck).split('?')[0];
    const currentPath = currentUrl.split('?')[0];

    if (exact) {
        // Exact match only - for direct links
        return currentPath === urlPath;
    }

    // Prefix match for parent/group detection (e.g., /alumni matches /alumni/birthdays)
    return currentPath === urlPath || currentPath.startsWith(urlPath + '/');
}

export function toUrl(href: NonNullable<InertiaLinkProps['href']>) {
    return typeof href === 'string' ? href : href?.url;
}

/**
 * Format a phone number for display
 * Handles various formats and returns a readable format
 */
export function formatPhoneNumber(phone: string): string {
    if (!phone) return '';

    // Remove all non-digit characters except + at the start
    const cleaned = phone.replace(/[^\d+]/g, '');

    // If it starts with +234 (Nigeria), format as +234 XXX XXX XXXX
    if (cleaned.startsWith('+234')) {
        const digits = cleaned.substring(4);
        if (digits.length === 10) {
            return `+234 ${digits.substring(0, 3)} ${digits.substring(3, 6)} ${digits.substring(6)}`;
        }
    }

    // If it starts with 0 and has 11 digits (Nigerian local format)
    if (cleaned.startsWith('0') && cleaned.length === 11) {
        return `${cleaned.substring(0, 4)} ${cleaned.substring(4, 7)} ${cleaned.substring(7)}`;
    }

    // For other international numbers starting with +
    if (cleaned.startsWith('+') && cleaned.length > 10) {
        const countryCode = cleaned.substring(0, cleaned.length - 10);
        const number = cleaned.substring(cleaned.length - 10);
        return `${countryCode} ${number.substring(0, 3)} ${number.substring(3, 6)} ${number.substring(6)}`;
    }

    // Return as-is if no pattern matches
    return phone;
}
