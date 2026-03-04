export interface DashboardStats {
    projects: number;
    openTasks: number;
    completed: number;
    overdue: number;
}

export type StatusVariant = 'in-progress' | 'done' | 'backlog';

