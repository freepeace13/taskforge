import type { PageProps } from '@inertiajs/core';
import { Head, Link, useForm } from '@inertiajs/react';
import { route } from 'ziggy-js';
import { TaskForm } from '@/features/tasks';
import type { TaskFormFields } from '@/features/tasks/types';
import FlashMessages from '@/components/FlashMessages';

type TasksCreateProps = PageProps & {
    organization: { slug: string; name: string };
    project: { id: number; name: string };
};

export default function TasksCreate({ organization, project }: TasksCreateProps) {
    const form = useForm<TaskFormFields>({
        title: '',
        description: '',
        priority: '',
        due_date: '',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        form.post(
            route('projects.tasks.store', {
                org: organization.slug,
                project: project.id,
            }),
        );
    };

    return (
        <>
            <Head title={`New task — ${project.name}`} />

            <FlashMessages />

            <div className="mb-6">
                <p className="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">{organization.name}</p>
                <h1 className="text-xl font-bold tracking-tight">New task</h1>
                <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">{project.name}</p>
            </div>

            <form onSubmit={submit} className="mx-auto max-w-2xl rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <TaskForm
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
                        {form.processing ? 'Saving…' : 'Create task'}
                    </button>
                    <Link
                        href={route('projects.tasks.index', {
                            org: organization.slug,
                            project: project.id,
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
