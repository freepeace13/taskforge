import type { ReactNode } from 'react';

type SideBarItemProps = {
    children: ReactNode;
    isActive?: boolean;
    /** When set, renders an anchor with these styles. */
    href?: string;
    className?: string;
};

const itemBaseClasses =
    'flex items-center gap-3 rounded-2xl px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900';

const itemActiveClasses =
    'bg-gray-100 font-semibold text-gray-900 dark:bg-gray-900 dark:text-gray-100';

export default function SideBarItem({ children, isActive = false, href, className = '' }: SideBarItemProps) {
    const stateClasses = isActive ? itemActiveClasses : itemBaseClasses;
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
