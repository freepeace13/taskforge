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

function WorkspacesIndex({ organizations }: WorkspacesIndexProps) {
    return (
        <>
            <Head title="Your organizations" />

            <div className="mb-8">
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
                                <div>
                                    <h2 className="text-base font-semibold text-gray-900 dark:text-gray-100">{org.name}</h2>
                                    <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        Role: {formatRole(org.role)}
                                    </p>
                                </div>
                                <Form
                                    action={route('workspaces.store')}
                                    method="post"
                                    className="shrink-0"
                                >
                                    <input
                                        type="hidden"
                                        name="organization_id"
                                        value={org.id}
                                    />
                                    <Button
                                        type="submit"
                                        size="md"
                                    >
                                        Open workspace
                                    </Button>
                                </Form>
                            </div>
                        </li>
                    ))}
                </ul>
            )}
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
