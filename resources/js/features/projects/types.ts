export type ProjectAttributes = {
    id: number;
    /** Present after migrations; fallback to `id` in routes when missing. */
    slug?: string | null;
    name: string;
    description: string | null;
    archived_at: string | null;
};

export type PaginatedProjects = {
    data: ProjectAttributes[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    next_page_url: string | null;
    prev_page_url: string | null;
};
