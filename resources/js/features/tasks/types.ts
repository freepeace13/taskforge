export type TaskFormFields = {
    title: string;
    description: string;
    priority: string;
    due_date: string;
};

/** Organization member on a task (collaborators). */
export type TaskMember = {
    id: number;
    name: string;
    email: string;
};

export type TaskAttributes = {
    id: number;
    key: string | null;
    project_id: number;
    assigned_to_user_id: number | null;
    title: string;
    description: string | null;
    status: string;
    priority: string | null;
    due_date: string | null;
    completed_at: string | null;
    created_at: string;
    updated_at: string;
    /** Present when the task is loaded with the `members` relationship. */
    members?: TaskMember[];
};

export type TaskTableRow = TaskAttributes & {
    /** Tasks index URL with `task` query — opens route-based preview modal */
    previewUrl: string;
    showUrl: string;
    editUrl: string;
};
