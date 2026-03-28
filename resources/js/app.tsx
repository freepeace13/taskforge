import './bootstrap';
import '../css/app.css';
import { createRoot } from 'react-dom/client';
import { createInertiaApp, router } from '@inertiajs/react';
import { route } from 'ziggy-js';
import { AppLayout, type SidebarNavItem } from '@/features/layout';

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob('./pages/**/*.tsx', { eager: true }) as Record<string, any>;
        let page = pages[`./pages/${name}.tsx`];
        page.default.layout =
            page.default.layout ||
            ((currentPage: any) => {
                const tasksRouteName = route().current();
                const tasksNavActive =
                    tasksRouteName === 'tasks.hub' ||
                    (typeof tasksRouteName === 'string' && tasksRouteName.startsWith('projects.tasks.'));

                const navItems: SidebarNavItem[] = [
                    { href: route('dashboard'), icon: '🏠', label: 'Dashboard', isActive: route().current('dashboard') },
                    { href: route('projects.index'), icon: '📁', label: 'Projects', isActive: route().current('projects.index') },
                    { href: route('tasks.hub'), icon: '✅', label: 'My Tasks', isActive: tasksNavActive },
                    { href: '#', icon: '👥', label: 'Team' },
                    { href: '#', icon: '🕒', label: 'Activity' },
                ];

                return (
                    <AppLayout
                        userName={currentPage.props.auth?.user?.name ?? '-'}
                        userEmail={currentPage.props.auth?.user?.email ?? '-'}
                        navItems={navItems}
                        onLogoutRequest={() =>
                            new Promise<void>((resolve) => {
                                router.post(route('logout'), undefined, {
                                    onFinish: () => {
                                        resolve();
                                    },
                                });
                            })
                        }
                    >
                        {currentPage}
                    </AppLayout>
                );
            });
        return page;
    },
    setup({ el, App, props }) {
        const root = createRoot(el);

        root.render(<App {...props} />);
    },
});

