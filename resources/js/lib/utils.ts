import { InertiaLinkProps } from '@inertiajs/vue3';
import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function urlIsActive(
    urlToCheck: NonNullable<InertiaLinkProps['href']>,
    currentUrl: string,
) {
    const checkUrl = toUrl(urlToCheck);

    // Remove query parameters for comparison
    const checkUrlPath = checkUrl.split('?')[0];
    const currentUrlPath = currentUrl.split('?')[0];

    return checkUrlPath === currentUrlPath;
}

export function toUrl(href: NonNullable<InertiaLinkProps['href']>) {
    return typeof href === 'string' ? href : href?.url;
}