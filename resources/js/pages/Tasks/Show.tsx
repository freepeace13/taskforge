import type { PageProps } from '@inertiajs/core';
import { Head, Link, router } from '@inertiajs/react';
import { useCallback, useState } from 'react';
import { route } from 'ziggy-js';
import type { TaskAttributes, TaskMember } from '@/features/tasks/types';
import { TaskDetailsContent } from '@/features/tasks';
import FlashMessages from '@/components/FlashMessages';
import { projectRouteParam, taskRouteParam } from '@/utils/routeBindings';

type TasksShowProps = PageProps & {
    organization: { slug: string; name: string };
    project: { id: number; slug?: string | null; name: string };
    task: TaskAttributes;
    organizationMembers: TaskMember[];
};

export default function TasksShow({ organization, project, task, organizationMembers }: TasksShowProps) {
    const [membersSaving, setMembersSaving] = useState(false);

    const handleMembersChange = useCallback(
        (memberIds: number[]) => {
            setMembersSaving(true);
            router.patch(
                route('projects.tasks.update', {
                    org: organization.slug,
                    project: projectRouteParam(project),
                    task: taskRouteParam(task),
                }),
                { member_ids: memberIds, redirect_back: true },
                {
                    preserveScroll: true,
                    only: ['task', 'organizationMembers'],
                    onFinish: () => setMembersSaving(false),
                },
            );
        },
        [organization.slug, project, task],
    );
    const destroy = () => {
        if (!window.confirm('Delete this task?')) {
            return;
        }

        router.delete(
            route('projects.tasks.destroy', {
                org: organization.slug,
                project: projectRouteParam(project),
                task: taskRouteParam(task),
            }),
        );
    };

    return (
        <>
            <Head title={task.title} />

            <FlashMessages />

            <div className="mb-6 flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p className="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        {organization.name} · {project.name}
                    </p>
                    <h1 className="text-xl font-bold tracking-tight">
                        {task.key ? (
                            <span className="mr-2 font-mono text-base text-gray-500 dark:text-gray-400">{task.key}</span>
                        ) : null}
                        {task.title}
                    </h1>
                    <p className="mt-2 text-sm capitalize text-gray-600 dark:text-gray-300">
                        {task.status.replace('_', ' ')}
                        {task.priority ? ` · ${task.priority} priority` : ''}
                    </p>
                </div>

                <div className="flex flex-wrap gap-2">
                    <Link
                        href={route('projects.tasks.index', {
                            org: organization.slug,
                            project: projectRouteParam(project),
                        })}
                        className="inline-flex items-center justify-center rounded-2xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-100 dark:hover:bg-gray-800"
                    >
                        Back to list
                    </Link>
                    <Link
                        href={route('projects.tasks.edit', {
                            org: organization.slug,
                            project: projectRouteParam(project),
                            task: taskRouteParam(task),
                        })}
                        className="inline-flex items-center justify-center rounded-2xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700"
                    >
                        Edit
                    </Link>
                    <button
                        type="button"
                        onClick={destroy}
                        className="inline-flex items-center justify-center rounded-2xl bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-700"
                    >
                        Delete
                    </button>
                </div>
            </div>

            <TaskDetailsContent
                task={task}
                organizationMembers={organizationMembers}
                onMembersChange={handleMembersChange}
                membersSaving={membersSaving}
            />
        </>
    );
}
