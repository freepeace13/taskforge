import type { PageProps } from '@inertiajs/core';
import { Head, Link, useForm } from '@inertiajs/react';
import { route } from 'ziggy-js';
import { TaskForm } from '@/features/tasks';
import type { TaskAttributes, TaskFormFields } from '@/features/tasks/types';
import FlashSuccess from '@/pages/Tasks/FlashSuccess';

type TasksEditProps = PageProps & {
    organization: { slug: string; name: string };
    project: { id: number; name: string };
    task: TaskAttributes;
};

export default function TasksEdit({ organization, project, task }: TasksEditProps) {
    const form = useForm<TaskFormFields>({
        title: task.title,
        description: task.description ?? '',
        priority: task.priority ?? '',
        due_date: task.due_date ?? '',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        form.patch(
            route('projects.tasks.update', {
                project: project.id,
                task: task.id,
            }),
        );
    };

    return (
        <>
            <Head title={`Edit — ${task.title}`} />

            <FlashSuccess />

            <div className="mb-6">
                <p className="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    {organization.name} · {project.name}
                </p>
                <h1 className="text-xl font-bold tracking-tight">Edit task</h1>
            </div>

            <form onSubmit={submit} className="mx-auto max-w-2xl rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <TaskForm
                    id="edit-task"
                    data={form.data}
                    errors={form.errors}
                    onChange={(field, value) => form.setData(field, value)}
                />

                <div className="mt-8 flex flex-wrap gap-3">
                    <button
                        type="submit"
                        disabled={form.processing}
                        className="inline-flex items-center justify-center rounded-2xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500/40 disabled:opacity-60"
                    >
                        {form.processing ? 'Saving…' : 'Save changes'}
                    </button>
                    <Link
                        href={route('projects.tasks.show', {
                            project: project.id,
                            task: task.id,
                        })}
                        className="inline-flex items-center justify-center rounded-2xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-100 dark:hover:bg-gray-800"
                    >
                        Cancel
                    </Link>
                </div>
            </form>
        </>
    );
}
