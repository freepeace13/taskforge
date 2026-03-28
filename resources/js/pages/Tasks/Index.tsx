import type { PageProps } from '@inertiajs/core';
import { Head, Link, router } from '@inertiajs/react';
import { route } from 'ziggy-js';
import { TaskTable } from '@/features/tasks';
import type { TaskAttributes, TaskTableRow } from '@/features/tasks/types';
import FlashMessages from '@/components/FlashMessages';

type PaginatedTasks = {
    data: TaskAttributes[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    next_page_url: string | null;
    prev_page_url: string | null;
};

type TasksIndexPageProps = PageProps & {
    organization: { slug: string; name: string };
    project: { id: number; name: string };
    tasks: PaginatedTasks;
};

export default function TasksIndex({ organization, project, tasks }: TasksIndexPageProps) {
    const rows: TaskTableRow[] = tasks.data.map((t) => ({
        ...t,
        showUrl: route('projects.tasks.show', {
            project: project.id,
            task: t.id,
        }),
        editUrl: route('projects.tasks.edit', {
            project: project.id,
            task: t.id,
        }),
    }));

    const handleDelete = (taskId: number) => {
        router.delete(
            route('projects.tasks.destroy', {
                project: project.id,
                task: taskId,
            }),
        );
    };

    return (
        <>
            <Head title={`Tasks — ${project.name}`} />

            <FlashMessages />

            <div className="mb-6 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p className="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">{organization.name}</p>
                    <h1 className="text-xl font-bold tracking-tight">{project.name}</h1>
                    <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">Tasks for this project.</p>
                </div>

                <div className="flex flex-wrap items-center gap-2">
                    <Link
                        href={route('tasks.hub')}
                        className="inline-flex items-center justify-center rounded-2xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-500/30 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-100 dark:hover:bg-gray-800"
                    >
                        All projects
                    </Link>
                    <Link
                        href={route('projects.tasks.create', {
                            project: project.id,
                        })}
                        className="inline-flex items-center justify-center rounded-2xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500/40"
                    >
                        + New task
                    </Link>
                </div>
            </div>

            <TaskTable rows={rows} onDelete={handleDelete} />

            {tasks.last_page > 1 ? (
                <div className="mt-6 flex items-center justify-between gap-3 text-sm text-gray-600 dark:text-gray-400">
                    <span>
                        Page {tasks.current_page} of {tasks.last_page} ({tasks.total} tasks)
                    </span>
                    <div className="flex gap-2">
                        {tasks.prev_page_url ? (
                            <Link
                                href={tasks.prev_page_url}
                                className="rounded-xl border border-gray-200 px-3 py-1.5 font-semibold hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-900"
                            >
                                Previous
                            </Link>
                        ) : null}
                        {tasks.next_page_url ? (
                            <Link
                                href={tasks.next_page_url}
                                className="rounded-xl border border-gray-200 px-3 py-1.5 font-semibold hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-900"
                            >
                                Next
                            </Link>
                        ) : null}
                    </div>
                </div>
            ) : null}
        </>
    );
}
