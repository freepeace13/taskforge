import type { ReactNode } from 'react';
import type { SharedUiBaseProps, SharedUiTheme } from '@/features/shared/ui/types';
import { resolveSharedUiTheme } from '@/features/shared/ui/types';

const shellByTheme: Record<SharedUiTheme, string> = {
    light: 'rounded-3xl border border-gray-200 bg-white shadow-sm',
    dark: 'rounded-3xl border border-gray-800 bg-gray-900 shadow-sm',
};

const descriptionByTheme: Record<SharedUiTheme, string> = {
    light: 'text-sm text-gray-500',
    dark: 'text-sm text-gray-400',
};

const actionButtonByTheme: Record<SharedUiTheme, string> = {
    light:
        'inline-flex items-center justify-center rounded-2xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold shadow-sm hover:bg-gray-50',
    dark: 'inline-flex items-center justify-center rounded-2xl border border-gray-800 bg-gray-950 px-4 py-2 text-sm font-semibold shadow-sm hover:bg-gray-800',
};

export interface TableProps extends SharedUiBaseProps {
    title: string;
    description?: string;
    actionLabel?: string;
    onActionClick?: () => void;
    children: ReactNode;
}

export default function Table({
    title,
    description,
    actionLabel,
    onActionClick,
    children,
    theme,
}: TableProps) {
    const t = resolveSharedUiTheme(theme);

    return (
        <div className={shellByTheme[t]}>
            <div className="flex items-center justify-between gap-3 p-5">
                <div>
                    <div className="text-base font-semibold">{title}</div>
                    {description && (
                        <div className={descriptionByTheme[t]}>{description}</div>
                    )}
                </div>

                {actionLabel && (
                    <button
                        type="button"
                        onClick={onActionClick}
                        className={actionButtonByTheme[t]}
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
