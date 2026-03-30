import type { ReactNode } from 'react';
import type { SharedUiBaseProps, SharedUiTheme } from '@/features/shared/ui/types';
import { resolveSharedUiTheme } from '@/features/shared/ui/types';

const sectionByTheme: Record<SharedUiTheme, string> = {
    light: 'w-80 shrink-0 rounded-3xl border border-gray-200 bg-white',
    dark: 'w-80 shrink-0 rounded-3xl border border-gray-800 bg-gray-900',
};

const headerRowByTheme: Record<SharedUiTheme, string> = {
    light: 'flex items-center justify-between border-b border-gray-200 p-4',
    dark: 'flex items-center justify-between border-b border-gray-800 p-4',
};

const countBadgeByTheme: Record<SharedUiTheme, string> = {
    light: 'rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-700',
    dark: 'rounded-full bg-gray-800 px-2 py-0.5 text-xs font-semibold text-gray-200',
};

const titleByTheme: Record<SharedUiTheme, string> = {
    light: 'text-sm font-semibold text-gray-900',
    dark: 'text-sm font-semibold text-gray-100',
};

const addButtonByTheme: Record<SharedUiTheme, string> = {
    light: 'rounded-xl px-2 py-1 text-sm font-semibold text-gray-600 hover:bg-gray-100',
    dark: 'rounded-xl px-2 py-1 text-sm font-semibold text-gray-300 hover:bg-gray-950/60',
};

export interface KanbanColumnProps extends SharedUiBaseProps {
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
    theme,
}: KanbanColumnProps) {
    const t = resolveSharedUiTheme(theme);

    return (
        <section className={sectionByTheme[t]}>
            <div className={headerRowByTheme[t]}>
                <div className="flex items-center gap-2">
                    <span className={`h-2.5 w-2.5 rounded-full ${indicatorColorClass}`} />
                    <h2 className={titleByTheme[t]}>{title}</h2>
                    <span className={countBadgeByTheme[t]}>
                        {count}
                    </span>
                </div>
                <button
                    type="button"
                    className={addButtonByTheme[t]}
                >
                    +
                </button>
            </div>

            <div className="space-y-3 p-4">
                {children}
            </div>
        </section>
    );
}
