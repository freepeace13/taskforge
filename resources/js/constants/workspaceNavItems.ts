import { SidebarNavItem } from '@/features/layout';
import { route } from 'ziggy-js';

export const workspaceNavItems = (tenantSlug: string, currentRouteName: string): SidebarNavItem[] => [
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
