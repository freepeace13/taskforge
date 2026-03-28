import type { ReactNode } from 'react';
import { Button } from '@/features/shared/ui';
import type { ProjectAttributes } from '@/features/projects/types';

export type ProjectListRow = {
    project: ProjectAttributes;
    actions: ReactNode;
};

type ProjectsListTableProps = {
    rows: ProjectListRow[];
    emptyMessage: string;
    onDeleteRequest: (projectId: number) => void;
};

export default function ProjectsListTable({ rows, emptyMessage, onDeleteRequest }: ProjectsListTableProps) {
    return (
        <div className="rounded-3xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div className="overflow-x-auto">
                <table className="min-w-full text-left text-sm">
                    <thead className="border-b border-gray-200 bg-gray-50 text-xs uppercase text-gray-500 dark:border-gray-800 dark:bg-gray-950 dark:text-gray-400">
                        <tr>
                            <th className="px-6 py-4 font-semibold">Project</th>
                            <th className="px-6 py-4 text-right font-semibold">Actions</th>
                        </tr>
                    </thead>

                    <tbody className="divide-y divide-gray-200 dark:divide-gray-800">
                        {rows.length === 0 ? (
                            <tr>
                                <td colSpan={2} className="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                                    {emptyMessage}
                                </td>
                            </tr>
                        ) : (
                            rows.map(({ project, actions }) => (
                                <tr key={project.id} className="hover:bg-gray-50 dark:hover:bg-gray-950/60">
                                    <td className="px-6 py-5">
                                        <div className="font-semibold text-gray-900 dark:text-gray-100">{project.name}</div>
                                        {project.description ? (
                                            <div className="mt-1 text-xs text-gray-500 dark:text-gray-400">{project.description}</div>
                                        ) : null}
                                    </td>

                                    <td className="px-6 py-5 text-right">
                                        <div className="flex flex-wrap items-center justify-end gap-2">
                                            {actions}
                                            <Button type="button" variant="ghost" size="sm" onClick={() => onDeleteRequest(project.id)}>
                                                Delete
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                            ))
                        )}
                    </tbody>
                </table>
            </div>
        </div>
    );
}
