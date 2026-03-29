import type { ReactNode } from 'react';

/** Full-width sticky bar (e.g. workspace header). */
export const appBarWorkspaceClasses =
    'sticky top-0 z-40 border-b border-gray-200 bg-white/80 backdrop-blur dark:border-gray-800 dark:bg-gray-950/70';

/** Mobile-only top bar (hamburger + title strip). */
export const appBarMobileNavClasses = `${appBarWorkspaceClasses} lg:hidden`;

/** Desktop-only toolbar (search + actions). */
export const appBarDesktopToolbarClasses =
    'sticky top-0 z-30 hidden border-b border-gray-200 bg-white/70 backdrop-blur dark:border-gray-800 dark:bg-gray-950/60 lg:block';

type AppBarProps = {
    children: ReactNode;
    /** Defaults to {@link appBarWorkspaceClasses}. */
    className?: string;
};

export default function AppBar({ children, className }: AppBarProps) {
    return <header className={className ?? appBarWorkspaceClasses}>{children}</header>;
}
