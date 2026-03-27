import type { ReactNode } from 'react';
import { createContext, useContext, useEffect, useMemo, useState } from 'react';
import { router, usePage } from '@inertiajs/react';
import { route } from 'ziggy-js';
import { AppHeader, AppSidebar, TaskModal } from '@/features/layout/components';

type AppLayoutProps = {
    children: ReactNode;
};

type LayoutContextValue = {
    toggleDarkMode: () => void;
    isSidebarOpen: boolean;
    openSidebar: () => void;
    closeSidebar: () => void;
    isTaskModalOpen: boolean;
    openTaskModal: () => void;
    closeTaskModal: () => void;
    logout: () => void;
    isLoggingOut: boolean;
};

const LayoutContext = createContext<LayoutContextValue | undefined>(undefined);

export function useLayoutContext(): LayoutContextValue {
    const context = useContext(LayoutContext);

    if (!context) {
        throw new Error('useLayoutContext must be used within an AppLayout');
    }

    return context;
}

function LayoutProvider({ children }: { children: ReactNode }) {
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

    const toggleDarkMode = () => {
        const root = document.documentElement;
        const nextIsDark = !isDark;

        setIsDark(nextIsDark);

        root.classList.toggle('dark', nextIsDark);
        window.localStorage.setItem('theme', nextIsDark ? 'dark' : 'light');
    };

    const logout = () => {
        setIsLoggingOut(true);

        router.post(route('logout'), undefined, {
            onFinish: () => {
                setIsLoggingOut(false);
            },
        });
    };

    const openSidebar = () => setIsSidebarOpen(true);
    const closeSidebar = () => setIsSidebarOpen(false);

    const openTaskModal = () => setIsTaskModalOpen(true);
    const closeTaskModal = () => setIsTaskModalOpen(false);

    const value = useMemo<LayoutContextValue>(
        () => ({
            toggleDarkMode,
            isSidebarOpen,
            openSidebar,
            closeSidebar,
            isTaskModalOpen,
            openTaskModal,
            closeTaskModal,
            logout,
            isLoggingOut,
        }),
        [isDark, isSidebarOpen, isTaskModalOpen, isLoggingOut],
    );

    return <LayoutContext.Provider value={value}>{children}</LayoutContext.Provider>;
}

function AppLayoutShell({ children }: AppLayoutProps) {
    const { openSidebar, closeSidebar, isSidebarOpen, toggleDarkMode, openTaskModal, closeTaskModal, isTaskModalOpen, logout, isLoggingOut } =
        useLayoutContext();
    const auth = usePage().props.auth as { user?: { name?: string; email?: string } };

    const navItems = useMemo(
        () => [
            { href: route('dashboard'), icon: '🏠', label: 'Dashboard', isActive: route().current('dashboard') },
            { href: route('projects.index'), icon: '📁', label: 'Projects', isActive: route().current('projects.index') },
            { href: route('tasks.index'), icon: '✅', label: 'My Tasks', isActive: route().current('tasks.index') },
            { href: '#', icon: '👥', label: 'Team' },
            { href: '#', icon: '🕒', label: 'Activity' },
        ],
        [],
    );

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
                userName={auth?.user?.name ?? '-'}
                userEmail={auth?.user?.email ?? '-'}
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

export default function AppLayout({ children }: AppLayoutProps) {
    return (
        <LayoutProvider>
            <AppLayoutShell>{children}</AppLayoutShell>
        </LayoutProvider>
    );
}

