export type TaskFormFields = {
    title: string;
    description: string;
    priority: string;
    due_date: string;
};

export type TaskAttributes = {
    id: number;
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
};

export type TaskTableRow = TaskAttributes & {
    showUrl: string;
    editUrl: string;
};
