import type { ReactNode } from 'react';
import { AppHeader, AppSidebar } from '@/features/layout/components';
import { LayoutShellProvider, useLayoutContext } from '@/features/layout/context/LayoutContext';

type AppLayoutProps = {
    children: ReactNode;
    userName: string;
    userEmail: string;
    navItems: SidebarNavItem[];
    onLogoutRequest: () => Promise<void> | void;
    onNewTaskNavigate?: () => void;
};

export type SidebarNavItem = {
    href: string;
    icon: string;
    label: string;
    isActive?: boolean;
};

function AppLayoutShell({ children, userName, userEmail, navItems }: Omit<AppLayoutProps, 'onLogoutRequest' | 'onNewTaskNavigate'>) {
    const { appChrome, toggleDarkMode, logout, isLoggingOut } = useLayoutContext();

    if (!appChrome) {
        throw new Error('AppLayoutShell must be used with LayoutShellProvider mode="app"');
    }

    const { openSidebar, closeSidebar, isSidebarOpen, goToTasksHub } = appChrome;

    return (
        <div className="min-h-full">
            <AppHeader
                onOpenSidebar={openSidebar}
                onToggleDarkMode={toggleDarkMode}
                onOpenTaskModal={goToTasksHub}
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
        </div>
    );
}

export default function AppLayout({ children, userName, userEmail, navItems, onLogoutRequest, onNewTaskNavigate }: AppLayoutProps) {
    return (
        <LayoutShellProvider
            mode="app"
            onLogoutRequest={onLogoutRequest}
            goToTasksHub={onNewTaskNavigate}
        >
            <AppLayoutShell
                userName={userName}
                userEmail={userEmail}
                navItems={navItems}
            >
                {children}
            </AppLayoutShell>
        </LayoutShellProvider>
    );
}
