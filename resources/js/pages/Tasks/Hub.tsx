import type { PageProps } from '@inertiajs/core';
import { Head, Link } from '@inertiajs/react';
import { route } from 'ziggy-js';
import { projectRouteParam } from '@/utils/routeBindings';

type HubProject = {
    id: number;
    slug?: string | null;
    name: string;
};

type HubOrganization = {
    slug: string;
    name: string;
    projects: HubProject[];
};

type TasksHubProps = PageProps & {
    organizations: HubOrganization[];
};

export default function TasksHub({ organizations }: TasksHubProps) {
    return (
        <>
            <Head title="Tasks" />

            <div className="mb-6">
                <h1 className="text-xl font-bold tracking-tight">Tasks</h1>
                <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">Choose a project to view and manage its tasks.</p>
            </div>

            {organizations.length === 0 ? (
                <p className="rounded-3xl border border-dashed border-gray-200 bg-gray-50/80 px-6 py-12 text-center text-sm text-gray-500 dark:border-gray-800 dark:bg-gray-900/40 dark:text-gray-400">
                    You are not a member of any organization yet.
                </p>
            ) : (
                <ul className="space-y-6">
                    {organizations.map((org) => (
                        <li
                            key={org.slug}
                            className="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900"
                        >
                            <h2 className="text-base font-semibold text-gray-900 dark:text-gray-100">{org.name}</h2>
                            {org.projects.length === 0 ? (
                                <p className="mt-2 text-sm text-gray-500 dark:text-gray-400">No active projects.</p>
                            ) : (
                                <ul className="mt-3 divide-y divide-gray-100 dark:divide-gray-800">
                                    {org.projects.map((project) => (
                                        <li key={project.id} className="flex items-center justify-between gap-3 py-3 first:pt-0 last:pb-0">
                                            <span className="text-sm font-medium text-gray-800 dark:text-gray-200">{project.name}</span>
                                            <Link
                                                href={route('projects.tasks.index', {
                                                    org: org.slug,
                                                    project: projectRouteParam(project),
                                                })}
                                                className="text-sm font-semibold text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300"
                                            >
                                                Open tasks →
                                            </Link>
                                        </li>
                                    ))}
                                </ul>
                            )}
                        </li>
                    ))}
                </ul>
            )}
        </>
    );
}
