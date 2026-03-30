import type { PageProps } from '@inertiajs/core';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { useCallback, useMemo, useState } from 'react';
import { route } from 'ziggy-js';
import { TaskDetailsContent, TaskKanbanBoard, TaskTable } from '@/features/tasks';
import type { TaskAttributes, TaskMember, TaskTableRow } from '@/features/tasks/types';
import { Modal } from '@/features/shared/ui';
import FlashMessages from '@/components/FlashMessages';
import { projectRouteParam, taskRouteParam } from '@/utils/routeBindings';

type TasksIndexView = 'list' | 'board';

function tasksIndexViewFromUrl(url: string): TasksIndexView {
    const view = new URL(url, 'http://localhost').searchParams.get('view');
    return view === 'list' ? 'list' : 'board';
}

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
    project: { id: number; slug?: string | null; name: string };
    tasks: PaginatedTasks;
    taskPreview: TaskAttributes | null;
    organizationMembers: TaskMember[];
};

function tasksIndexQueryParams(
    organizationSlug: string,
    project: { id: number; slug?: string | null },
    options: { view: TasksIndexView; taskKey?: string | number | null },
): Record<string, string | number> {
    const params: Record<string, string | number> = {
        org: organizationSlug,
        project: projectRouteParam(project),
    };

    if (options.view === 'list') {
        params.view = 'list';
    }

    if (options.taskKey != null && options.taskKey !== '') {
        params.task = options.taskKey;
    }

    return params;
}

export default function TasksIndex({
    organization,
    project,
    tasks,
    taskPreview,
    organizationMembers,
}: TasksIndexPageProps) {
    const { url } = usePage();
    const view = useMemo(() => tasksIndexViewFromUrl(url), [url]);
    const [membersSaving, setMembersSaving] = useState(false);

    const rows: TaskTableRow[] = tasks.data.map((t) => ({
        ...t,
        previewUrl: route(
            'projects.tasks.index',
            tasksIndexQueryParams(organization.slug, project, {
                view,
                taskKey: taskRouteParam(t),
            }),
        ),
        showUrl: route('projects.tasks.show', {
            org: organization.slug,
            project: projectRouteParam(project),
            task: taskRouteParam(t),
        }),
        editUrl: route('projects.tasks.edit', {
            org: organization.slug,
            project: projectRouteParam(project),
            task: taskRouteParam(t),
        }),
    }));

    const closeTaskPreview = useCallback(() => {
        const next = new URL(url, window.location.origin);
        next.searchParams.delete('task');
        router.get(next.pathname + next.search, {}, { preserveScroll: true });
    }, [url]);

    const handleDelete = (taskRouteKey: string | number) => {
        router.delete(
            route('projects.tasks.destroy', {
                org: organization.slug,
                project: projectRouteParam(project),
                task: taskRouteKey,
            }),
        );
    };

    const handleKanbanMoveTask = (task: TaskTableRow, status: 'todo' | 'in_progress' | 'done') => {
        router.patch(
            route('projects.tasks.update', {
                org: organization.slug,
                project: projectRouteParam(project),
                task: taskRouteParam(task),
            }),
            {
                status,
                redirect_to_board: true,
            },
            {
                preserveScroll: true,
                only: ['tasks'],
            },
        );
    };

    const handleMembersChange = useCallback(
        (memberIds: number[]) => {
            if (!taskPreview) {
                return;
            }

            setMembersSaving(true);
            router.patch(
                route('projects.tasks.update', {
                    org: organization.slug,
                    project: projectRouteParam(project),
                    task: taskRouteParam(taskPreview),
                }),
                { member_ids: memberIds, redirect_back: true },
                {
                    preserveScroll: true,
                    only: ['taskPreview', 'tasks'],
                    onFinish: () => setMembersSaving(false),
                },
            );
        },
        [organization.slug, project, taskPreview],
    );

    const previewModalTitle = taskPreview
        ? [
              taskPreview.key ? `${taskPreview.key} ` : '',
              taskPreview.title,
          ].join('')
        : '';

    const previewShowUrl = taskPreview
        ? route('projects.tasks.show', {
              org: organization.slug,
              project: projectRouteParam(project),
              task: taskRouteParam(taskPreview),
          })
        : '';

    const previewEditUrl = taskPreview
        ? route('projects.tasks.edit', {
              org: organization.slug,
              project: projectRouteParam(project),
              task: taskRouteParam(taskPreview),
          })
        : '';

    const previewDescription = taskPreview
        ? `${taskPreview.status.replace('_', ' ')}${taskPreview.priority ? ` · ${taskPreview.priority} priority` : ''}`
        : undefined;

    return (
        <>
            <Head title={`Tasks — ${project.name}`} />

            <FlashMessages />

            {taskPreview ? (
                <Modal
                    isOpen
                    onClose={closeTaskPreview}
                    title={previewModalTitle}
                    description={previewDescription}
                    panelClassName="w-full max-w-2xl rounded-3xl border border-gray-200 bg-white shadow-xl dark:border-gray-800 dark:bg-gray-900"
                    footer={
                        <div className="flex w-full flex-wrap items-center justify-between gap-2">
                            <Link
                                href={previewShowUrl}
                                className="text-sm font-semibold text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300"
                            >
                                Open full page
                            </Link>
                            <div className="flex flex-wrap gap-2">
                                <Link
                                    href={previewEditUrl}
                                    className="inline-flex items-center justify-center rounded-2xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700"
                                >
                                    Edit
                                </Link>
                            </div>
                        </div>
                    }
                >
                    <TaskDetailsContent
                        task={taskPreview}
                        embedded
                        organizationMembers={organizationMembers}
                        onMembersChange={handleMembersChange}
                        membersSaving={membersSaving}
                    />
                </Modal>
            ) : null}

            <div className="mb-6 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p className="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">{organization.name}</p>
                    <h1 className="text-xl font-bold tracking-tight">{project.name}</h1>
                    <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">Tasks for this project.</p>
                </div>

                <div className="flex flex-wrap items-center gap-2">
                    <div className="flex rounded-2xl border border-gray-200 bg-white p-1 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                        <Link
                            href={route('projects.tasks.index', {
                                org: organization.slug,
                                project: projectRouteParam(project),
                                view: 'list',
                            })}
                            className={[
                                'rounded-2xl px-3 py-1.5 text-sm font-semibold',
                                view === 'list'
                                    ? 'bg-gray-100 text-gray-900 dark:bg-gray-800 dark:text-gray-100'
                                    : 'text-gray-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800/60',
                            ].join(' ')}
                        >
                            List
                        </Link>
                        <Link
                            href={route('projects.tasks.index', { org: organization.slug, project: projectRouteParam(project) })}
                            className={[
                                'rounded-2xl px-3 py-1.5 text-sm font-semibold',
                                view === 'board'
                                    ? 'bg-gray-100 text-gray-900 dark:bg-gray-800 dark:text-gray-100'
                                    : 'text-gray-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800/60',
                            ].join(' ')}
                        >
                            Board
                        </Link>
                    </div>
                    <Link
                        href={route('tasks.hub', { org: organization.slug })}
                        className="inline-flex items-center justify-center rounded-2xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-500/30 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-100 dark:hover:bg-gray-800"
                    >
                        All projects
                    </Link>
                    <Link
                        href={route('projects.tasks.create', {
                            org: organization.slug,
                            project: projectRouteParam(project),
                        })}
                        className="inline-flex items-center justify-center rounded-2xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500/40"
                    >
                        + New task
                    </Link>
                </div>
            </div>

            {view === 'board' ? (
                <TaskKanbanBoard rows={rows} onMoveTask={handleKanbanMoveTask} />
            ) : (
                <TaskTable rows={rows} onDelete={handleDelete} />
            )}

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
