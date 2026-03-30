import { Link } from '@inertiajs/react';
import { Button } from '@/features/shared/ui';
import type { TaskTableRow } from '@/features/tasks/types';
import { taskRouteParam } from '@/utils/routeBindings';

export type TaskTableProps = {
    rows: TaskTableRow[];
    onDelete: (taskRouteKey: string | number) => void;
};

export default function TaskTable({ rows, onDelete }: TaskTableProps) {
    if (rows.length === 0) {
        return (
            <p className="rounded-3xl border border-dashed border-gray-200 bg-gray-50/80 px-6 py-12 text-center text-sm text-gray-500 dark:border-gray-800 dark:bg-gray-900/40 dark:text-gray-400">
                No tasks yet. Create one to get started.
            </p>
        );
    }

    return (
        <div className="overflow-x-auto rounded-3xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <table className="min-w-full text-left text-sm">
                <thead className="border-b border-gray-200 bg-gray-50/80 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:border-gray-800 dark:bg-gray-950/80 dark:text-gray-400">
                    <tr>
                        <th className="px-5 py-3">Task</th>
                        <th className="px-5 py-3">Status</th>
                        <th className="px-5 py-3">Priority</th>
                        <th className="px-5 py-3">Due</th>
                        <th className="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody className="divide-y divide-gray-200 dark:divide-gray-800">
                    {rows.map((row) => (
                        <tr key={row.id} className="hover:bg-gray-50/80 dark:hover:bg-gray-950/50">
                            <td className="px-5 py-3 font-medium text-gray-900 dark:text-gray-100">
                                <Link href={row.previewUrl} className="hover:text-brand-600 dark:hover:text-brand-400">
                                    {row.title}
                                </Link>
                            </td>
                            <td className="px-5 py-3 capitalize text-gray-600 dark:text-gray-300">{row.status.replace('_', ' ')}</td>
                            <td className="px-5 py-3 text-gray-600 dark:text-gray-300">{row.priority ?? '—'}</td>
                            <td className="px-5 py-3 text-gray-600 dark:text-gray-300">{row.due_date ?? '—'}</td>
                            <td className="px-5 py-3">
                                <div className="flex flex-wrap justify-end gap-2">
                                    <Link
                                        href={row.previewUrl}
                                        className="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-900 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-500/30 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-100 dark:hover:bg-gray-800"
                                    >
                                        View
                                    </Link>
                                    <Link
                                        href={row.showUrl}
                                        className="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-900 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-500/30 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-100 dark:hover:bg-gray-800"
                                    >
                                        Full page
                                    </Link>
                                    <Link
                                        href={row.editUrl}
                                        className="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-900 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-500/30 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-100 dark:hover:bg-gray-800"
                                    >
                                        Edit
                                    </Link>
                                    <Button
                                        type="button"
                                        variant="destructive"
                                        size="sm"
                                        onClick={() => {
                                            if (window.confirm('Delete this task?')) {
                                                onDelete(taskRouteParam(row));
                                            }
                                        }}
                                    >
                                        Delete
                                    </Button>
                                </div>
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
}
