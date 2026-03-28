import type { PageProps } from '@inertiajs/core';
import { Head, Link, router } from '@inertiajs/react';
import { route } from 'ziggy-js';
import type { TaskAttributes } from '@/features/tasks/types';
import FlashSuccess from '@/pages/Tasks/FlashSuccess';

type TasksShowProps = PageProps & {
    organization: { slug: string; name: string };
    project: { id: number; name: string };
    task: TaskAttributes;
};

export default function TasksShow({ organization, project, task }: TasksShowProps) {
    const destroy = () => {
        if (!window.confirm('Delete this task?')) {
            return;
        }

        router.delete(
            route('projects.tasks.destroy', {
                project: project.id,
                task: task.id,
            }),
        );
    };

    return (
        <>
            <Head title={task.title} />

            <FlashSuccess />

            <div className="mb-6 flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p className="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        {organization.name} · {project.name}
                    </p>
                    <h1 className="text-xl font-bold tracking-tight">{task.title}</h1>
                    <p className="mt-2 text-sm capitalize text-gray-600 dark:text-gray-300">
                        {task.status.replace('_', ' ')}
                        {task.priority ? ` · ${task.priority} priority` : ''}
                    </p>
                </div>

                <div className="flex flex-wrap gap-2">
                    <Link
                        href={route('projects.tasks.index', {
                            project: project.id,
                        })}
                        className="inline-flex items-center justify-center rounded-2xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-100 dark:hover:bg-gray-800"
                    >
                        Back to list
                    </Link>
                    <Link
                        href={route('projects.tasks.edit', {
                            project: project.id,
                            task: task.id,
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

            <div className="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                {task.description ? (
                    <p className="whitespace-pre-wrap text-sm text-gray-800 dark:text-gray-200">{task.description}</p>
                ) : (
                    <p className="text-sm text-gray-500 dark:text-gray-400">No description.</p>
                )}

                <dl className="mt-6 grid gap-3 text-sm sm:grid-cols-2">
                    <div>
                        <dt className="font-semibold text-gray-500 dark:text-gray-400">Due date</dt>
                        <dd className="text-gray-900 dark:text-gray-100">{task.due_date ?? '—'}</dd>
                    </div>
                    <div>
                        <dt className="font-semibold text-gray-500 dark:text-gray-400">Completed</dt>
                        <dd className="text-gray-900 dark:text-gray-100">{task.completed_at ?? '—'}</dd>
                    </div>
                </dl>
            </div>
        </>
    );
}
