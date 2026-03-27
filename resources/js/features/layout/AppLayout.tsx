import { useEffect, useMemo, useState } from 'react';
import type { ReactNode } from 'react';
import { AppHeader, AppSidebar, TaskModal } from '@/features/layout/components';
import { LayoutProvider, useLayoutContext, type LayoutContextValue } from '@/features/layout/context/LayoutContext';

type AppLayoutProps = {
    children: ReactNode;
    userName: string;
    userEmail: string;
    navItems: SidebarNavItem[];
    onLogoutRequest: () => Promise<void> | void;
};

export type SidebarNavItem = {
    href: string;
    icon: string;
    label: string;
    isActive?: boolean;
};

function AppLayoutShell({ children, userName, userEmail, navItems }: Omit<AppLayoutProps, 'onLogoutRequest'>) {
    const { openSidebar, closeSidebar, isSidebarOpen, toggleDarkMode, openTaskModal, closeTaskModal, isTaskModalOpen, logout, isLoggingOut } =
        useLayoutContext();

    return (
        <div className="min-h-full">
            <AppHeader
                onOpenSidebar={openSidebar}
                onToggleDarkMode={toggleDarkMode}
                onOpenTaskModal={openTaskModal}
            />
            <AppSidebar
                isOpen={isSidebarOpen}
                onClose={closeSidebar}
                onToggleDarkMode={toggleDarkMode}
                onLogout={logout}
                isLoggingOut={isLoggingOut}
                userName={userName}
                userEmail={userEmail}
                navItems={navItems}
            />

            <div className="lg:pl-72">
                <main className="px-4 py-6 lg:px-6">{children}</main>
            </div>
            <TaskModal
                isOpen={isTaskModalOpen}
                onClose={closeTaskModal}
            />
        </div>
    );
}

export default function AppLayout({ children, userName, userEmail, navItems, onLogoutRequest }: AppLayoutProps) {
    const [isDark, setIsDark] = useState(false);
    const [isSidebarOpen, setIsSidebarOpen] = useState(false);
    const [isTaskModalOpen, setIsTaskModalOpen] = useState(false);
    const [isLoggingOut, setIsLoggingOut] = useState(false);

    useEffect(() => {
        const root = document.documentElement;
        const savedTheme = window.localStorage.getItem('theme');

        if (savedTheme === 'dark') {
            root.classList.add('dark');
            setIsDark(true);
        } else {
            root.classList.remove('dark');
            setIsDark(false);
        }

        const handleKeyDown = (event: KeyboardEvent) => {
            if (event.key === 'Escape') {
                setIsSidebarOpen(false);
                setIsTaskModalOpen(false);
            }
        };

        window.addEventListener('keydown', handleKeyDown);

        return () => {
            window.removeEventListener('keydown', handleKeyDown);
        };
    }, []);

    const value = useMemo<LayoutContextValue>(
        () => ({
            toggleDarkMode: () => {
                const root = document.documentElement;
                const nextIsDark = !isDark;

                setIsDark(nextIsDark);
                root.classList.toggle('dark', nextIsDark);
                window.localStorage.setItem('theme', nextIsDark ? 'dark' : 'light');
            },
            isSidebarOpen,
            openSidebar: () => setIsSidebarOpen(true),
            closeSidebar: () => setIsSidebarOpen(false),
            isTaskModalOpen,
            openTaskModal: () => setIsTaskModalOpen(true),
            closeTaskModal: () => setIsTaskModalOpen(false),
            logout: async () => {
                setIsLoggingOut(true);

                try {
                    await Promise.resolve(onLogoutRequest());
                } finally {
                    setIsLoggingOut(false);
                }
            },
            isLoggingOut,
        }),
        [isDark, isSidebarOpen, isTaskModalOpen, isLoggingOut, onLogoutRequest],
    );

    return (
        <LayoutProvider value={value}>
            <AppLayoutShell
                userName={userName}
                userEmail={userEmail}
                navItems={navItems}
            >
                {children}
            </AppLayoutShell>
        </LayoutProvider>
    );
}

