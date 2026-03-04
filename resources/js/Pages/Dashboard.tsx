import type { PageProps } from '@inertiajs/core';
import { Head } from '@inertiajs/react';
import Button from '@/Components/Button';
import StatCard from '@/Components/StatCard';
import StatusBadge from '@/Components/StatusBadge';
import Table from '@/Components/Table';
import type { DashboardStats, StatusVariant } from '@/Components/types';

type DashboardProps = PageProps & {
    user: {
        name: string;
    };
    stats: DashboardStats;
};

type RecentTask = {
    id: number;
    title: string;
    project: string;
    statusLabel: string;
    statusVariant: StatusVariant;
    due: string;
};

const recentTasks: RecentTask[] = [
    {
        id: 1,
        title: 'Implement org invites',
        project: 'Core',
        statusLabel: 'In Progress',
        statusVariant: 'in-progress',
        due: 'Mar 10',
    },
    {
        id: 2,
        title: 'Scoped bindings for tasks',
        project: 'API',
        statusLabel: 'Done',
        statusVariant: 'done',
        due: 'Mar 04',
    },
    {
        id: 3,
        title: 'Add activity log UI',
        project: 'Web',
        statusLabel: 'Backlog',
        statusVariant: 'backlog',
        due: 'Mar 18',
    },
];

export default function Dashboard({ stats }: DashboardProps) {
    return (
        <>
            <Head title="Dashboard" />
            <div className="mb-6 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 className="text-xl font-bold tracking-tight">Dashboard (Inertia)</h1>
                    <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Overview of your projects and tasks, powered by Inertia + React.
                    </p>
                </div>

                <div className="flex items-center gap-2">
                    <Button
                        variant="secondary"
                        size="md"
                    >
                        Export
                    </Button>

                    <Button size="md">
                        Create Project
                    </Button>
                </div>
            </div>

            <section className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <StatCard
                    label="Projects"
                    value={stats.projects}
                    icon="📁"
                    helperText="+2 this week"
                />
                <StatCard
                    label="Open Tasks"
                    value={stats.openTasks}
                    icon="✅"
                    helperText="7 due today"
                />
                <StatCard
                    label="Completed"
                    value={stats.completed}
                    icon="🏁"
                    helperText="Last 30 days"
                />
                <StatCard
                    label="Overdue"
                    value={stats.overdue}
                    icon="⏰"
                    helperText="Needs attention"
                    valueClassName="text-red-600 dark:text-red-400"
                />
            </section>

            <section className="mt-6">
                <Table title="Recent Tasks" description="Latest updates across your org">
                    <thead className="border-t border-gray-200 bg-gray-50 text-xs uppercase text-gray-500 dark:border-gray-800 dark:bg-gray-950 dark:text-gray-400">
                        <tr>
                            <th className="px-5 py-3 font-semibold">Task</th>
                            <th className="px-5 py-3 font-semibold">Project</th>
                            <th className="px-5 py-3 font-semibold">Status</th>
                            <th className="px-5 py-3 font-semibold">Due</th>
                            <th className="px-5 py-3 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>

                    <tbody className="divide-y divide-gray-200 dark:divide-gray-800">
                        {recentTasks.map((task) => (
                            <tr
                                key={task.id}
                                className="hover:bg-gray-50 dark:hover:bg-gray-950/60"
                            >
                                <td className="px-5 py-4 font-medium">{task.title}</td>
                                <td className="px-5 py-4 text-gray-600 dark:text-gray-300">
                                    {task.project}
                                </td>
                                <td className="px-5 py-4">
                                    <StatusBadge
                                        label={task.statusLabel}
                                        variant={task.statusVariant}
                                    />
                                </td>
                                <td className="px-5 py-4 text-gray-600 dark:text-gray-300">
                                    {task.due}
                                </td>
                                <td className="px-5 py-4 text-right">
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                    >
                                        View
                                    </Button>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </Table>
            </section>
        </>
    );
}

