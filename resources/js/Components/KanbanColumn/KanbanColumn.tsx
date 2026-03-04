import type { ReactNode } from 'react';

export interface KanbanColumnProps {
    title: string;
    count: number;
    indicatorColorClass?: string;
    children?: ReactNode;
}

export default function KanbanColumn({
    title,
    count,
    indicatorColorClass = 'bg-green-500',
    children,
}: KanbanColumnProps) {
    return (
        <section className="w-80 shrink-0 rounded-3xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div className="flex items-center justify-between border-b border-gray-200 p-4 dark:border-gray-800">
                <div className="flex items-center gap-2">
                    <span className={`h-2.5 w-2.5 rounded-full ${indicatorColorClass}`} />
                    <h2 className="text-sm font-semibold">{title}</h2>
                    <span className="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-200">
                        {count}
                    </span>
                </div>
                <button
                    type="button"
                    className="rounded-xl px-2 py-1 text-sm font-semibold hover:bg-gray-100 dark:hover:bg-gray-950/60"
                >
                    +
                </button>
            </div>

            <div className="space-y-3 p-4">
                {children ?? (
                    <article className="rounded-3xl border border-gray-200 bg-white p-4 shadow-sm transition hover:shadow-md dark:border-gray-800 dark:bg-gray-950/40">
                        <div className="text-sm font-semibold">Core Platform</div>
                        <p className="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Multi-tenant foundation layer.
                        </p>

                        <div className="mt-4 flex items-center justify-between">
                            <div className="flex -space-x-2">
                                <div className="h-7 w-7 rounded-xl bg-gray-300 dark:bg-gray-700" />
                                <div className="h-7 w-7 rounded-xl bg-gray-400 dark:bg-gray-600" />
                                <div className="h-7 w-7 rounded-xl bg-gray-500 dark:bg-gray-500" />
                            </div>
                            <span className="text-xs font-semibold text-gray-600 dark:text-gray-300">
                                70%
                            </span>
                        </div>

                        <div className="mt-2 h-2 w-full rounded-full bg-gray-200 dark:bg-gray-800">
                            <div className="h-2 rounded-full bg-brand-600" style={{ width: '70%' }} />
                        </div>
                    </article>
                )}
            </div>
        </section>
    );
}

