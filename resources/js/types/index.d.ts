import { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';

// ============================================
// Auth & User Types
// ============================================

export interface Auth {
    user: User;
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    avatar_url?: string;
    email_verified_at: string | null;
    is_admin?: boolean;
    created_at: string;
    updated_at: string;
}

// ============================================
// Navigation Types
// ============================================

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon;
    isActive?: boolean;
    adminOnly?: boolean;
    items?: NavItem[];
}

export interface NavGroup {
    title: string;
    icon?: LucideIcon;
    items: NavItem[];
    defaultOpen?: boolean;
}

// ============================================
// App Page Props
// ============================================

export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
};

// ============================================
// Tenure Types
// ============================================

export interface Tenure {
    id: number;
    name?: string;
    year: string;
    family_name?: string;
    is_active?: boolean;
    start_date?: string | null;
    end_date?: string | null;
}

export interface Department {
    id: number;
    name: string;
    code: string;
}

// ============================================
// Enum Option Type
// ============================================

export interface EnumOption {
    value: string;
    label: string;
}

// ============================================
// Alumnus Types
// ============================================

/** Full Alumnus - all fields */
export interface Alumnus {
    id: number;
    name: string;
    email: string | null;
    phones: string[] | null;
    department_id: number | null;
    department?: { id: number; name: string; code: string } | null;
    gender: string | null;
    birth_date: string | null;
    tenure_id: number | null;
    unit: string | null;
    state: string | null;
    address: string | null;
    past_exco_office: string | null;
    current_exco_office: string | null;
    is_futa_staff: boolean;
    marital_status: string | null;
    occupation: string | null;
    current_employer: string | null;
    photo: string | null;
    photo_url: string | null;
    initials: string;
    tenure?: Tenure | null;
    communication_logs?: CommunicationLog[];
}

/** Minimal Alumnus for list views - subset of fields */
export interface AlumnusSummary {
    id: number;
    name: string;
    email: string | null;
    phones: string[] | null;
    state: string | null;
    unit: string | null;
    photo_url: string | null;
    initials: string;
    tenure: Tenure | null;
}

/** Birthday Alumnus - different structure for birthday page */
export interface BirthdayAlumnus {
    id: number;
    name: string;
    birth_date: string;
    email: string | null;
    phones: string[] | null;
    dept: string | null;
    location: string | null;
    photo_url: string | null;
    initials: string;
}

// ============================================
// Communication Log Types
// ============================================

export interface CommunicationLog {
    id: number;
    alumnus_id: number;
    user_id: number;
    type: string; // Enum
    outcome: string; // Enum
    notes: string | null;
    occurred_at: string;
    created_at: string;
    updated_at: string;
    user?: User;
}

// ============================================
// Pagination Types
// ============================================

/** Single pagination link */
export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

/** Pagination links (Laravel API Resource style) */
export interface PaginationLinks {
    first: string | null;
    last: string | null;
    prev: string | null;
    next: string | null;
}

/** Pagination meta (Laravel API Resource style) */
export interface PaginationMeta {
    current_page: number;
    from: number;
    last_page: number;
    links: PaginationLink[];
    path: string;
    per_page: number;
    to: number;
    total: number;
}

/** Paginated response with links & meta (Laravel API Resource style) */
export interface PaginatedResponse<T> {
    data: T[];
    links: PaginationLinks;
    meta: PaginationMeta;
}

/** Simple paginated response (Laravel paginate() style) */
export interface SimplePaginatedResponse<T> {
    data: T[];
    links: PaginationLink[];
    current_page: number;
    last_page: number;
    first_page_url?: string;
    last_page_url?: string;
    next_page_url?: string | null;
    prev_page_url?: string | null;
    from: number | null;
    to: number | null;
    total: number;
    per_page: number;
    path?: string;
}

// ============================================
// Redemption Week Types
// ============================================

export interface EventDay {
    value: string;
    label: string;
}

export interface RedemptionWeekSession {
    id: number;
    name: string;
    year: number;
    start_date: string | null;
    end_date: string | null;
    event_dates: Record<string, string> | null;
    description: string | null;
    is_active: boolean;
    created_at: string;
    updated_at: string;
    attendances_count?: number;
    attendance_stats?: Record<string, { label: string; count: number }>;
    unique_attendees?: number;
    perfect_count?: number;
}

export interface RedemptionWeekAttendance {
    id: number;
    session_id: number;
    alumnus_id: number;
    event_day: string;
    marked_by: number | null;
    created_at: string;
    updated_at: string;
    alumnus?: { id: number; name: string; email: string | null };
    marked_by_user?: User;
}

export interface AttendanceByDay {
    label: string;
    attendances: RedemptionWeekAttendance[];
    count: number;
}

// Legacy type alias
export type BreadcrumbItemType = BreadcrumbItem;
