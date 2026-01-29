import { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href?: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon;
    isActive?: boolean;
    items?: NavItem[];
}

export type AppPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
    messages: any;
};

export interface UserProfile {
    profile_picture?: string;
    profile_picture_url?: string;
    address?: string;
    contact_number?: string;
    gender?: string;
    birth_date?: string;
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    role: 'admin' | 'customer';
    profile?: UserProfile;
}

export type BreadcrumbItemType = BreadcrumbItem;

export interface DataTableColumn<T = any> {
    label: string;
    key: keyof T | string;
    align?: 'left' | 'center' | 'right';
    render?: (value: any, row: T) => string | number;
    class?: string;
    sortable?: boolean;
}

export interface DataTableAction<T = any> {
    label: string;
    icon: any;
    onClick: (row: T) => void;
    variant?: 'default' | 'ghost' | 'destructive' | 'outline';
    show?: (row: T) => boolean;
    class?: string;
}

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface PaginationData {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
    links: PaginationLink[];
}

export interface FilterOption {
    label: string;
    value: string;
}

export interface FilterConfig {
    label: string;
    key: string;
    options: FilterOption[];
    placeholder?: string;
    value?: string;
}

export interface Message {
    id: number;
    conversation_id: number;
    sender_id: number;
    sender_type: 'admin' | 'customer';
    message: string;
    item_id: number | null;
    read_at: string | null;
    created_at: string;
    updated_at: string;
    user?: User;
    item?: any;
}

export interface Conversation {
    id: number;
    customer_id: number;
    admin_id: number | null;
    last_message_at: string;
    created_at: string;
    updated_at: string;
    user_customer?: User;
    user_admin?: User;
    messages?: Message[];
    unread_count?: number;
}
