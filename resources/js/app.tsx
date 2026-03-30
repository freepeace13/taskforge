import './bootstrap';
import '../css/app.css';
import { createRoot } from 'react-dom/client';
import { createInertiaApp, router } from '@inertiajs/react';
import { route } from 'ziggy-js';
import { AppLayout, type SidebarNavItem } from '@/features/layout';
import { ThemeProvider } from './features/shared/context/ThemeContext';
import { AuthProvider } from './features/shared/context/AuthContext';

type WorkspaceLayoutProps = {
    auth?: {
        user?: {
            id: number;
            authId: string | null;
            name: string;
            email: string;
        } | null;
        tenant?: { id: number; slug: string; name: string; role: string } | null;
        organizations?: Array<{ id: number; slug: string; name: string; role: string }>;
    };
};

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob('./pages/**/*.tsx', { eager: true }) as Record<string, any>;
        let page = pages[`./pages/${name}.tsx`];
        const defaultLayout = (currentPage: any) => {
            const props = currentPage.props as WorkspaceLayoutProps;
            const currentRouteName = route().current();
            const tenantSlug = props.auth?.tenant?.slug;

            const navItems: SidebarNavItem[] = [
                {
                    href: route('workspaces'),
                    icon: '🏢',
                    label: 'Workspaces',
                    isActive: route().current('workspaces'),
                },
                {
                    href: tenantSlug ? route('projects.index', { org: tenantSlug }) : route('workspaces'),
                    icon: '📁',
                    label: 'Projects',
                    isActive: currentRouteName?.startsWith('projects.') ?? false,
                },
                {
                    href: tenantSlug ? route('tasks.hub', { org: tenantSlug }) : route('workspaces'),
                    icon: '✅',
                    label: 'My Tasks',
                    isActive: currentRouteName?.startsWith('tasks.') ?? false,
                },
                { href: '#', icon: '👥', label: 'Team' },
                { href: '#', icon: '🕒', label: 'Activity' },
            ];

            return <AppLayout navItems={navItems}>{currentPage}</AppLayout>;
        };

        const existingLayout = page.default.layout || defaultLayout;

        page.default.layout = (currentPage: any) => {
            return <AuthProvider>{existingLayout(currentPage)}</AuthProvider>;
        };

        return page;
    },
    setup({ el, App, props }) {
        const root = createRoot(el);
        root.render(
            <ThemeProvider>
                <App {...props} />
            </ThemeProvider>
        );
    },
});

