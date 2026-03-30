import type { ReactNode } from 'react';
import type { SharedUiBaseProps, SharedUiTheme } from '@/features/shared/ui/types';
import { resolveSharedUiTheme } from '@/features/shared/ui/types';

const asideByTheme: Record<SharedUiTheme, string> = {
    light:
        'fixed inset-y-0 left-0 z-50 w-72 border-r border-gray-200 bg-white transition-transform lg:translate-x-0',
    dark: 'fixed inset-y-0 left-0 z-50 w-72 border-r border-gray-800 bg-gray-950 transition-transform lg:translate-x-0',
};

export interface SideBarProps extends SharedUiBaseProps {
    children: ReactNode;
    isOpen: boolean;
    onOverlayClick?: () => void;
}

export default function SideBar({ children, isOpen, onOverlayClick, theme }: SideBarProps) {
    const t = resolveSharedUiTheme(theme);

    return (
        <>
            <div
                className={`fixed inset-0 z-40 bg-black/40 lg:hidden ${isOpen ? '' : 'hidden'}`}
                aria-hidden={!isOpen}
                onClick={onOverlayClick}
            />

            <aside
                className={`${asideByTheme[t]} ${isOpen ? 'translate-x-0' : '-translate-x-full'}`}
            >
                {children}
            </aside>
        </>
    );
}
