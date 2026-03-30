import type { ReactNode } from 'react';
import type { SharedUiBaseProps, SharedUiTheme } from '@/features/shared/ui/types';
import { resolveSharedUiTheme } from '@/features/shared/ui/types';

const workspaceByTheme: Record<SharedUiTheme, string> = {
    light: 'sticky top-0 z-40 border-b border-gray-200 bg-white/80 backdrop-blur',
    dark: 'sticky top-0 z-40 border-b border-gray-800 bg-gray-950/70 backdrop-blur',
};

/**
 * Full-width sticky bar (e.g. workspace header).
 * Combined light + `dark:` string for Tailwind scan and legacy use without a `theme` prop.
 */
export const appBarWorkspaceClasses =
    'sticky top-0 z-40 border-b border-gray-200 bg-white/80 backdrop-blur dark:border-gray-800 dark:bg-gray-950/70';

/** Mobile-only top bar (hamburger + title strip). */
export const appBarMobileNavClasses = `${appBarWorkspaceClasses} lg:hidden`;

/** Desktop-only toolbar (search + actions). */
export const appBarDesktopToolbarClasses =
    'sticky top-0 z-30 hidden border-b border-gray-200 bg-white/70 backdrop-blur dark:border-gray-800 dark:bg-gray-950/60 lg:block';

export interface AppBarProps extends SharedUiBaseProps {
    children: ReactNode;
    /** Defaults to theme-specific workspace bar classes. */
    className?: string;
}

export default function AppBar({ children, className, theme }: AppBarProps) {
    const t = resolveSharedUiTheme(theme);
    return (
        <header className={className ?? workspaceByTheme[t]}>{children}</header>
    );
}
