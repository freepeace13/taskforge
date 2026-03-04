import type { ReactNode } from 'react';

export interface StatCardProps {
    label: string;
    value: number | string;
    icon?: ReactNode;
    helperText?: string;
    valueClassName?: string;
}

export default function StatCard({ label, value, icon, helperText, valueClassName = '' }: StatCardProps) {
    return (
        <div className="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div className="flex items-center justify-between">
                <div className="text-sm font-medium text-gray-500 dark:text-gray-400">{label}</div>
                {icon && <div className="text-gray-400">{icon}</div>}
            </div>

            <div className={`mt-3 text-2xl font-bold ${valueClassName}`.trim()}>{value}</div>

            {helperText && (
                <div className="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    {helperText}
                </div>
            )}
        </div>
    );
}

