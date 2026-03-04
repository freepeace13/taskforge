import type { ReactNode } from 'react';

export interface TableProps {
    title: string;
    description?: string;
    actionLabel?: string;
    onActionClick?: () => void;
    children: ReactNode;
}

export default function Table({ title, description, actionLabel, onActionClick, children }: TableProps) {
    return (
        <div className="rounded-3xl border border-gray-200 bg-whiteshadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div className="flex items-center justify-between gap-3 p-5">
                <div>
                    <div className="text-base font-semibold">{title}</div>
                    {description && (
                        <div className="text-sm text-gray-500 dark:text-gray-400">{description}</div>
                    )}
                </div>

                {actionLabel && (
                    <button
                        type="button"
                        onClick={onActionClick}
                        className="inline-flex items-center justify-center rounded-2xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold shadow-sm hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-950 dark:hover:bg-gray-800"
                    >
                        {actionLabel}
                    </button>
                )}
            </div>

            <div className="overflow-x-auto">
                <table className="min-w-full text-left text-sm">{children}</table>
            </div>
        </div>
    );
}

