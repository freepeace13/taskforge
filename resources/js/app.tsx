import './bootstrap';
import '../css/app.css';
import { createRoot } from 'react-dom/client';
import { createInertiaApp, router } from '@inertiajs/react';
import { route } from 'ziggy-js';
import { AppLayout, type SidebarNavItem } from '@/features/layout';

type WorkspaceLayoutProps = {
    auth?: { user?: { name?: string; email?: string } };
    tenantOrganization?: { slug: string; name: string } | null;
};

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob('./pages/**/*.tsx', { eager: true }) as Record<string, any>;
        let page = pages[`./pages/${name}.tsx`];
        page.default.layout =
            page.default.layout ||
            ((currentPage: any) => {
                const props = currentPage.props as WorkspaceLayoutProps;
                const currentRouteName = route().current();
                const tenantSlug = props.tenantOrganization?.slug;

                const navItems: SidebarNavItem[] = [
                    {
                        href: route('workspaces'),
                        icon: '🏢',
                        label: 'Workspaces',
                        isActive: route().current('workspaces'),
                    },
                    {
                        href: route('projects.index', tenantSlug),
                        icon: '📁',
                        label: 'Projects',
                        isActive: currentRouteName?.startsWith('projects.') ?? false,
                    },
                    {
                        href: route('tasks.hub', tenantSlug),
                        icon: '✅',
                        label: 'My Tasks',
                        isActive: currentRouteName?.startsWith('tasks.') ?? false,
                    },
                    { href: '#', icon: '👥', label: 'Team' },
                    { href: '#', icon: '🕒', label: 'Activity' },
                ];

                return (
                    <AppLayout
                        userName={props.auth?.user?.name ?? '-'}
                        userEmail={props.auth?.user?.email ?? '-'}
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
                        onNewTaskNavigate={
                            tenantSlug !== undefined
                                ? () => {
                                      router.visit(route('tasks.hub', tenantSlug));
                                  }
                                : undefined
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

