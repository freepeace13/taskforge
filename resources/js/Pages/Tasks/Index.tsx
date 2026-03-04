import { Head } from '@inertiajs/react';
import Button from '@/Components/Button';
import KanbanColumn from '@/Components/KanbanColumn';

type KanbanColumnConfig = {
    id: number;
    title: string;
    count: number;
    indicatorColorClass: string;
};

const columns: KanbanColumnConfig[] = [
    { id: 1, title: 'Backlog', count: 6, indicatorColorClass: 'bg-gray-400' },
    { id: 2, title: 'Ready', count: 4, indicatorColorClass: 'bg-blue-500' },
    { id: 3, title: 'In Progress', count: 5, indicatorColorClass: 'bg-yellow-500' },
    { id: 4, title: 'Review', count: 3, indicatorColorClass: 'bg-purple-500' },
    { id: 5, title: 'Blocked', count: 1, indicatorColorClass: 'bg-red-500' },
    { id: 6, title: 'Done', count: 8, indicatorColorClass: 'bg-green-500' },
];

export default function TasksIndex() {
    return (
        <>
            <Head title="Kanban" />

            <section className="flex flex-col gap-4">
                <div className="mb-6 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h1 className="text-xl font-bold tracking-tight">Projects</h1>
                        <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Kanban view by project status.
                        </p>
                    </div>

                    <div className="flex items-center gap-2">
                        <Button
                            variant="secondary"
                            size="md"
                            aria-label="Switch to table"
                        >
                            Table view
                        </Button>

                        <Button size="md">+ New Project</Button>
                    </div>
                </div>

                <div className="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div className="flex flex-1 flex-col gap-3 sm:flex-row">
                        <input
                            className="w-full rounded-2xl border border-gray-200 bg-white px-4 py-2 text-sm shadow-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500/30 dark:border-gray-800 dark:bg-gray-900 dark:placeholder:text-gray-500 sm:max-w-sm"
                            placeholder="Search projects..."
                        />

                        <select className="w-full rounded-2xl border border-gray-200 bg-white px-4 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 dark:border-gray-800 dark:bg-gray-900 sm:w-48">
                            <option>All Owners</option>
                            <option>Kin Basco</option>
                            <option>Mia</option>
                            <option>Alex</option>
                        </select>

                        <select className="w-full rounded-2xl border border-gray-200 bg-white px-4 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 dark:border-gray-800 dark:bg-gray-900 sm:w-48">
                            <option>Sort: Updated</option>
                            <option>Sort: Name</option>
                            <option>Sort: Progress</option>
                        </select>
                    </div>

                    <div className="flex items-center gap-2">
                        <Button
                            variant="ghost"
                            size="md"
                        >
                            Clear
                        </Button>
                    </div>
                </div>

                <div className="-mx-4 overflow-x-auto overflow-y-visible px-4 pb-4">
                    <div className="flex min-w-max gap-4">
                        {columns.map((column) => (
                            <KanbanColumn
                                key={column.id}
                                title={column.title}
                                count={column.count}
                                indicatorColorClass={column.indicatorColorClass}
                            />
                        ))}
                    </div>
                </div>
            </section>
        </>
    );
}

