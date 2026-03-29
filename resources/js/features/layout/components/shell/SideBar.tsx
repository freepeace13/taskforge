import type { ReactNode } from 'react';

type SideBarProps = {
    children: ReactNode;
    isOpen: boolean;
    onOverlayClick?: () => void;
};

export default function SideBar({ children, isOpen, onOverlayClick }: SideBarProps) {
    return (
        <>
            <div
                className={`fixed inset-0 z-40 bg-black/40 lg:hidden ${isOpen ? '' : 'hidden'}`}
                aria-hidden={!isOpen}
                onClick={onOverlayClick}
            />

            <aside
                className={`fixed inset-y-0 left-0 z-50 w-72 border-r border-gray-200 bg-white transition-transform dark:border-gray-800 dark:bg-gray-950 lg:translate-x-0 ${
                    isOpen ? 'translate-x-0' : '-translate-x-full'
                }`}
            >
                {children}
            </aside>
        </>
    );
}
