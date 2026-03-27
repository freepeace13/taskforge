import type { ReactNode } from 'react';

export interface CardProps {
    title: string;
    description?: string;
    children?: ReactNode;
    actions?: ReactNode;
}

export default function Card({ title, description, children, actions }: CardProps) {
    return (
        <div className="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div className="flex items-start justify-between gap-3">
                <div>
                    <h3 className="text-base font-semibold">{title}</h3>
                    {description && (
                        <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">{description}</p>
                    )}
                </div>
                <span className="text-gray-400">⋯</span>
            </div>

            {children && (
                <div className="mt-4 text-sm text-gray-700 dark:text-gray-200">{children}</div>
            )}

            {actions && (
                <div className="mt-5 flex items-center justify-end gap-2">
                    {actions}
                </div>
            )}
        </div>
    );
}

