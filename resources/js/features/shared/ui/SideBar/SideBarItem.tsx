import type { ReactNode } from 'react';
import type { SharedUiBaseProps, SharedUiTheme } from '@/features/shared/ui/types';
import { resolveSharedUiTheme } from '@/features/shared/ui/types';

const itemBaseByTheme: Record<SharedUiTheme, string> = {
    light:
        'flex items-center gap-3 rounded-2xl px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100',
    dark: 'flex items-center gap-3 rounded-2xl px-3 py-2 text-sm font-medium text-gray-200 hover:bg-gray-900',
};

const itemActiveByTheme: Record<SharedUiTheme, string> = {
    light: 'bg-gray-100 font-semibold text-gray-900',
    dark: 'bg-gray-900 font-semibold text-gray-100',
};

export interface SideBarItemProps extends SharedUiBaseProps {
    children: ReactNode;
    /** Static active state, or a function evaluated on render (e.g. route-based). */
    isActive?: boolean | (() => boolean);
    /** When set, renders an anchor with these styles. */
    href?: string;
    className?: string;
}

function resolveIsActive(isActive: SideBarItemProps['isActive']): boolean {
    if (isActive === undefined) {
        return false;
    }

    return typeof isActive === 'function' ? isActive() : isActive;
}

export default function SideBarItem({
    children,
    isActive,
    href,
    className = '',
    theme,
}: SideBarItemProps) {
    const t = resolveSharedUiTheme(theme);
    const active = resolveIsActive(isActive);
    const stateClasses = active ? itemActiveByTheme[t] : itemBaseByTheme[t];
    const combined = `${stateClasses} ${className}`.trim();

    if (href !== undefined) {
        return (
            <a
                href={href}
                className={combined}
            >
                {children}
            </a>
        );
    }

    return <div className={combined}>{children}</div>;
}
