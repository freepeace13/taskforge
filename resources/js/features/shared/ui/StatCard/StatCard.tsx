import type { ReactNode } from 'react';
import type { SharedUiBaseProps, SharedUiTheme } from '@/features/shared/ui/types';
import { resolveSharedUiTheme } from '@/features/shared/ui/types';

const cardByTheme: Record<SharedUiTheme, string> = {
    light: 'rounded-3xl border border-gray-200 bg-white p-5 shadow-sm',
    dark: 'rounded-3xl border border-gray-800 bg-gray-900 p-5 shadow-sm',
};

const labelByTheme: Record<SharedUiTheme, string> = {
    light: 'text-sm font-medium text-gray-500',
    dark: 'text-sm font-medium text-gray-400',
};

const iconByTheme: Record<SharedUiTheme, string> = {
    light: 'text-gray-400',
    dark: 'text-gray-500',
};

const helperByTheme: Record<SharedUiTheme, string> = {
    light: 'mt-1 text-xs text-gray-500',
    dark: 'mt-1 text-xs text-gray-400',
};

export interface StatCardProps extends SharedUiBaseProps {
    label: string;
    value: number | string;
    icon?: ReactNode;
    helperText?: string;
    valueClassName?: string;
}

export default function StatCard({
    label,
    value,
    icon,
    helperText,
    valueClassName = '',
    theme,
}: StatCardProps) {
    const t = resolveSharedUiTheme(theme);

    return (
        <div className={cardByTheme[t]}>
            <div className="flex items-center justify-between">
                <div className={labelByTheme[t]}>{label}</div>
                {icon && <div className={iconByTheme[t]}>{icon}</div>}
            </div>

            <div className={`mt-3 text-2xl font-bold ${valueClassName}`.trim()}>{value}</div>

            {helperText && (
                <div className={helperByTheme[t]}>
                    {helperText}
                </div>
            )}
        </div>
    );
}
