import type { PageProps } from '@inertiajs/core';
import type { ReactNode } from 'react';
import { Form, Head, router } from '@inertiajs/react';
import { route } from 'ziggy-js';
import { WorkspaceLayout } from '@/features/layout';
import { Button } from '@/features/shared/ui';

type WorkspaceOrg = {
    id: number;
    name: string;
    slug: string;
    role: string;
};

type WorkspacesIndexProps = PageProps & {
    organizations: WorkspaceOrg[];
};

function formatRole(role: string): string {
    return role.charAt(0).toUpperCase() + role.slice(1);
}

function organizationInitials(name: string): string {
    const parts = name.trim().split(/\s+/).filter(Boolean);
    if (parts.length === 0) {
        return '?';
    }
    if (parts.length === 1) {
        return parts[0].slice(0, 2).toUpperCase();
    }

    return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
}

function OrganizationLogoPlaceholder({ name }: { name: string }) {
    return (
        <div
            className="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl border border-gray-200 bg-gradient-to-br from-gray-50 to-gray-100 text-sm font-semibold tracking-tight text-gray-600 dark:border-gray-700 dark:from-gray-800 dark:to-gray-900 dark:text-gray-300"
            aria-hidden
        >
            {organizationInitials(name)}
        </div>
    );
}

function WorkspacesIndex({ organizations }: WorkspacesIndexProps) {
    const handleOpenWorkspace = (org: WorkspaceOrg) => {
        router.visit(route('dashboard', { org: org.slug }));
    };

    return (
        <>
            <Head title="Your organizations" />

            <div className="mx-auto w-full max-w-lg">
                <div className="mb-8 text-center">
                    <h1 className="text-xl font-bold tracking-tight">Choose a workspace</h1>
                    <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Select an organization to open its projects and tasks. You can switch again anytime from the app.
                    </p>
                </div>

                {organizations.length === 0 ? (
                    <p className="rounded-3xl border border-dashed border-gray-200 bg-gray-50/80 px-6 py-12 text-center text-sm text-gray-500 dark:border-gray-800 dark:bg-gray-900/40 dark:text-gray-400">
                        You are not a member of any organization yet. Ask an admin to invite you, or sign in again after your
                        account is added to a workspace.
                    </p>
                ) : (
                    <ul className="space-y-3">
                        {organizations.map((org) => (
                            <li
                                key={org.id}
                                className="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900"
                            >
                                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                    <div className="flex min-w-0 items-start gap-3 sm:items-center">
                                        <OrganizationLogoPlaceholder name={org.name} />
                                        <div className="min-w-0">
                                            <h2 className="text-base font-semibold text-gray-900 dark:text-gray-100">{org.name}</h2>
                                            <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                Role: {formatRole(org.role)}
                                            </p>
                                        </div>
                                    </div>
                                    <Button
                                        type="button"
                                        size="md"
                                        className="w-full sm:w-auto"
                                        onClick={() => handleOpenWorkspace(org)}
                                    >
                                        Open workspace
                                    </Button>
                                </div>
                            </li>
                        ))}
                    </ul>
                )}
            </div>
        </>
    );
}

WorkspacesIndex.layout = (page: ReactNode) => (
    <WorkspaceLayout
        onLogoutRequest={() =>
            new Promise<void>((resolve) => {
                router.post(route('logout'), undefined, {
                    onFinish: () => {
                        resolve();
                    },
                });
            })
        }
    >
        {page}
    </WorkspaceLayout>
);

export default WorkspacesIndex;
