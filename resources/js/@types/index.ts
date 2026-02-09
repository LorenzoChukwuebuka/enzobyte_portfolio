// Models
export interface Project {
    id: number;
    title: string;
    slug: string;
    description: string;
    full_description?: string;
    client_name?: string;
    project_url?: string;
    thumbnail?: string;
    thumbnail_url?: string;
    gallery?: string[];
    gallery_urls?: string[];
    technologies?: string[];
    category: 'web_development' | 'mobile_app' | 'ui_ux_design' | 'branding' | 'consulting' | 'other';
    completion_date?: string;
    duration_days?: number;
    featured: boolean;
    is_published: boolean;
    order: number;
    created_at?: string;
}

export interface Service {
    id: number;
    title: string;
    slug: string;
    short_description: string;
    full_description?: string;
    icon?: string;
    features?: string[];
    pricing_model?: 'fixed' | 'hourly' | 'project_based';
    base_price?: number;
    is_active: boolean;
    order: number;
}

export interface TeamMember {
    id: number;
    name: string;
    position: string;
    bio?: string;
    photo?: string;
    photo_url?: string;
    email?: string;
    linkedin?: string;
    twitter?: string;
    github?: string;
    skills?: string[];
    is_active: boolean;
    order: number;
}

export interface BlogPost {
    id: number;
    title: string;
    slug: string;
    excerpt: string;
    content: string;
    featured_image?: string;
    featured_image_url?: string;
    author_id?: number;
    author?: TeamMember;
    tags?: string[];
    category: 'technology' | 'design' | 'business' | 'tutorials' | 'case_studies' | 'news';
    read_time_minutes?: number;
    views: number;
    is_published: boolean;
    published_at?: string;
}

export interface Testimonial {
    id: number;
    client_name: string;
    client_position?: string;
    client_company?: string;
    testimonial: string;
    rating: number;
    client_photo?: string;
    client_photo_url?: string;
    project_id?: number;
    project?: Project;
    is_featured: boolean;
    is_published: boolean;
}

export interface ContactInquiry {
    id: number;
    name: string;
    email: string;
    phone?: string;
    company?: string;
    service_needed?: string;
    message: string;
    budget_range: string;
    status: 'new' | 'in_progress' | 'contacted' | 'qualified' | 'converted' | 'closed';
    internal_notes?: string;
    contacted_at?: string;
    created_at: string;
}

// API Response types
export interface PaginatedResponse<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

export interface ApiResponse<T> {
    data: T;
}

export interface MediaUploadResponse {
    success: boolean;
    path: string;
    url: string;
    file_name: string;
    mime_type: string;
    size: number;
}