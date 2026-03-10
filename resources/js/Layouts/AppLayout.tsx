import type { ReactNode } from 'react';
import { createContext, useContext, useEffect, useMemo, useState } from 'react';
import { router, usePage } from '@inertiajs/react';
import Button from '@/Components/Button';
import Modal from '@/Components/Modal';

type AppLayoutProps = {
    children: ReactNode;
};

type LayoutContextValue = {
    isDark: boolean;
    toggleDarkMode: () => void;
    isSidebarOpen: boolean;
    openSidebar: () => void;
    closeSidebar: () => void;
    isTaskModalOpen: boolean;
    openTaskModal: () => void;
    closeTaskModal: () => void;
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

    const openSidebar = () => setIsSidebarOpen(true);
    const closeSidebar = () => setIsSidebarOpen(false);

    const openTaskModal = () => setIsTaskModalOpen(true);
    const closeTaskModal = () => setIsTaskModalOpen(false);

    const value = useMemo<LayoutContextValue>(
        () => ({
            isDark,
            toggleDarkMode,
            isSidebarOpen,
            openSidebar,
            closeSidebar,
            isTaskModalOpen,
            openTaskModal,
            closeTaskModal,
        }),
        [isDark, isSidebarOpen, isTaskModalOpen],
    );

    return <LayoutContext.Provider value={value}>{children}</LayoutContext.Provider>;
}

function AppHeader() {
    const { openSidebar, toggleDarkMode, openTaskModal } = useLayoutContext();

    return (
        <>
            {/* Mobile Topbar */}
            <header className="sticky top-0 z-40 border-b border-gray-200 bg-white/80 backdrop-blur dark:border-gray-800 dark:bg-gray-950/70 lg:hidden">
                <div className="flex items-center gap-3 px-4 py-3">
                    <Button
                        variant="secondary"
                        size="sm"
                        onClick={openSidebar}
                        aria-label="Open menu"
                    >
                        ☰
                    </Button>

                    <div className="flex-1">
                        <div className="text-sm font-bold tracking-tight">TaskForge</div>
                        <div className="text-xs text-gray-500 dark:text-gray-400">Dashboard</div>
                    </div>

                    <Button
                        variant="secondary"
                        size="sm"
                        onClick={toggleDarkMode}
                        aria-label="Toggle dark mode"
                    >
                        🌓
                    </Button>
                </div>
            </header>
            {/* Desktop Topbar */}
            <div className="sticky top-0 z-30 hidden border-b border-gray-200 bg-white/70 backdrop-blur dark:border-gray-800 dark:bg-gray-950/60 lg:block">
                <div className="flex items-center gap-3 px-6 py-4">
                    <div className="flex-1">
                        <div className="relative">
                            <span className="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-400">
                                ⌘
                            </span>
                            <input
                                className="w-full rounded-2xl border border-gray-200 bg-white px-9 py-2 text-sm shadow-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500/40 dark:border-gray-800 dark:bg-gray-900 dark:placeholder:text-gray-500"
                                placeholder="Search (Cmd+K)"
                            />
                        </div>
                    </div>

                    <Button
                        size="md"
                        onClick={openTaskModal}
                    >
                        + New
                    </Button>

                    <Button
                        variant="secondary"
                        size="sm"
                        aria-label="Notifications"
                    >
                        🔔
                    </Button>

                    <div className="h-9 w-9 rounded-2xl bg-gray-200 dark:bg-gray-800" />
                </div>
            </div>
        </>
    );
}

function AppSidebar() {
    const { isSidebarOpen, closeSidebar, toggleDarkMode } = useLayoutContext();

    return (
        <>
            {/* Mobile overlay */}

            {/* Mobile overlay */}
            <div
                className={`fixed inset-0 z-40 bg-black/40 lg:hidden ${isSidebarOpen ? '' : 'hidden'}`}
                aria-hidden={!isSidebarOpen}
                onClick={closeSidebar}
            />

            {/* Sidebar */}
            <aside
                className={`fixed inset-y-0 left-0 z-50 w-72 border-r border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-950 transition-transform lg:translate-x-0 ${
                    isSidebarOpen ? 'translate-x-0' : '-translate-x-full'
                }`}
            >
                <div className="flex h-full flex-col">
                    <div className="flex items-center justify-between px-5 py-4">
                        <div>
                            <div className="text-base font-bold tracking-tight">TaskForge</div>
                            <div className="text-xs text-gray-500 dark:text-gray-400">Multi-tenant workspace</div>
                        </div>

                        <Button
                            variant="secondary"
                            size="sm"
                            onClick={closeSidebar}
                            className="lg:hidden"
                            aria-label="Close menu"
                        >
                            ✕
                        </Button>
                    </div>

                    <div className="px-5 pb-4">
                        <label className="block text-xs font-medium text-gray-500 dark:text-gray-400">Organization</label>
                        <button
                            type="button"
                            className="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-3 py-2 text-left text-sm font-semibold shadow-sm hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-900 dark:hover:bg-gray-800"
                        >
                            <div className="flex items-center justify-between">
                                <span className="truncate">My workspace</span>
                                <span className="text-gray-400">⌄</span>
                            </div>
                            <div className="mt-1 truncate text-xs text-gray-500 dark:text-gray-400">
                                {(usePage().props.auth as { user?: { email: string } })?.user?.email ?? '-'}
                            </div>
                        </button>
                    </div>

                    <nav className="flex-1 space-y-1 px-3">
                        <a
                            href={route('dashboard')}

                            className={`flex items-center gap-3 rounded-2xl px-3 py-2 text-sm font-semibold ${route().current('dashboard') ? 'bg-gray-100 text-gray-900 dark:bg-gray-900 dark:text-gray-100' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900'}`}
                        >
                            <span>🏠</span> Dashboard
                        </a>
                        <a
                            href={route('projects.index')}
                            className={`flex items-center gap-3 rounded-2xl px-3 py-2 text-sm font-medium ${route().current('projects.index') ? 'bg-gray-100 text-gray-900 dark:bg-gray-900 dark:text-gray-100' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900'}`}
                        >
                            <span>📁</span> Projects
                        </a>
                        <a
                            href={route('tasks.index')}
                            className={`flex items-center gap-3 rounded-2xl px-3 py-2 text-sm font-medium ${route().current('tasks.index') ? 'bg-gray-100 text-gray-900 dark:bg-gray-900 dark:text-gray-100' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900'}`}
                        >
                            <span>✅</span> My Tasks
                        </a>
                        <a
                            href="#"
                            className="flex items-center gap-3 rounded-2xl px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900"
                        >
                            <span>👥</span> Team
                        </a>
                        <a
                            href="#"
                            className="flex items-center gap-3 rounded-2xl px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900"
                        >
                            <span>🕒</span> Activity
                        </a>

                        <div className="my-4 border-t border-gray-200 dark:border-gray-800" />

                        <a
                            href="#"
                            className="flex items-center gap-3 rounded-2xl px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900"
                        >
                            <span>⚙️</span> Settings
                        </a>
                    </nav>

                    <div className="border-t border-gray-200 p-4 dark:border-gray-800">
                        <div className="flex items-center gap-3">
                            <div className="h-10 w-10 rounded-2xl bg-gray-200 dark:bg-gray-800" />
                            <div className="min-w-0 flex-1">
                                <div className="truncate text-sm font-semibold">
                                    {(usePage().props.auth as { user?: { name: string } })?.user?.name ?? '-'}
                                </div>
                                <div className="truncate text-xs text-gray-500 dark:text-gray-400">Owner</div>
                            </div>
                            <Button
                                variant="secondary"
                                size="sm"
                                onClick={() => router.post(route('logout'))}
                                aria-label="Log out"
                                className="hidden lg:inline-flex"
                            >
                                Log out
                            </Button>
                            <Button
                                variant="secondary"
                                size="sm"
                                onClick={toggleDarkMode}
                                className="hidden lg:inline-flex"
                                aria-label="Toggle dark mode"
                            >
                                🌓
                            </Button>
                        </div>
                    </div>
                </div>
            </aside>
        </>
    );
}

function TaskModal() {
    const { isTaskModalOpen, closeTaskModal } = useLayoutContext();

    return (
        <Modal
            isOpen={isTaskModalOpen}
            onClose={closeTaskModal}
            title="Create task"
            description="Add a task to a project."
            footer={
                <>
                    <Button
                        variant="ghost"
                        size="sm"
                        onClick={closeTaskModal}
                    >
                        Cancel
                    </Button>
                    <Button
                        size="sm"
                        onClick={closeTaskModal}
                    >
                        Create
                    </Button>
                </>
            }
        >
            <div>
                <label className="block text-sm font-semibold">Task title</label>
                <input
                    className="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-3 py-2 text-sm shadow-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500/30 dark:border-gray-800 dark:bg-gray-950 dark:placeholder:text-gray-500"
                    placeholder="e.g. Implement invites"
                />
            </div>

            <div>
                <label className="block text-sm font-semibold">Project</label>
                <select className="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 dark:border-gray-800 dark:bg-gray-950">
                    <option>Core</option>
                    <option>API</option>
                    <option>Web</option>
                </select>
            </div>

            <div className="grid gap-4 sm:grid-cols-2">
                <div>
                    <label className="block text-sm font-semibold">Due date</label>
                    <input
                        type="date"
                        className="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 dark:border-gray-800 dark:bg-gray-950"
                    />
                </div>
                <div>
                    <label className="block text-sm font-semibold">Status</label>
                    <select className="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 dark:border-gray-800 dark:bg-gray-950">
                        <option>Backlog</option>
                        <option>In Progress</option>
                        <option>Done</option>
                    </select>
                </div>
            </div>
        </Modal>
    );
}

export default function AppLayout({ children }: AppLayoutProps) {
    return (
        <LayoutProvider>
            <div className="min-h-full">
                <AppHeader />
                <AppSidebar />

            <div className="lg:pl-72">
                {/* Content */}
                <main className="px-4 py-6 lg:px-6">{children}</main>
            </div>
                <TaskModal />
            </div>
        </LayoutProvider>
    );
}

