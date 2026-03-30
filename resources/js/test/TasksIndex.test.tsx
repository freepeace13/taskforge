import { render, screen } from '@testing-library/react';
import { describe, expect, it, vi } from 'vitest';
import TasksIndex from '@/pages/Tasks/Index';

const inertiaMocks = vi.hoisted(() => {
    return {
        url: '/orgs/acme/projects/proj/tasks',
    };
});

vi.mock('@inertiajs/react', async () => {
    const actual = await vi.importActual<typeof import('@inertiajs/react')>('@inertiajs/react');

    return {
        ...actual,
        Head: () => null,
        Link: ({ href, children, ...props }: { href: string; children: React.ReactNode }) => (
            <a href={href} {...props}>
                {children}
            </a>
        ),
        router: {
            delete: vi.fn(),
            get: vi.fn(),
            patch: vi.fn(),
        },
        usePage: () => ({
            url: inertiaMocks.url,
        }),
    };
});

vi.mock('ziggy-js', () => ({
    route: () => '/mock',
}));

vi.mock('@/components/FlashMessages', () => ({
    default: () => null,
}));

vi.mock('@/features/tasks', () => ({
    TaskKanbanBoard: () => <div>Kanban board</div>,
    TaskTable: () => <div>Task table</div>,
}));

describe('TasksIndex', () => {
    it('shows list view when url contains ?view=list', () => {
        inertiaMocks.url = '/orgs/acme/projects/proj/tasks?view=list';

        render(
            <TasksIndex
                organization={{ slug: 'acme', name: 'Acme' }}
                project={{ id: 1, slug: 'proj', name: 'Project' }}
                tasks={{
                    data: [],
                    current_page: 1,
                    last_page: 1,
                    per_page: 15,
                    total: 0,
                    next_page_url: null,
                    prev_page_url: null,
                }}
                taskPreview={null}
                organizationMembers={[]}
            />,
        );

        expect(screen.getByText('Task table')).toBeInTheDocument();
        expect(screen.queryByText('Kanban board')).not.toBeInTheDocument();
    });

    it('shows board view when url has no view=list query (default)', () => {
        inertiaMocks.url = '/orgs/acme/projects/proj/tasks';

        render(
            <TasksIndex
                organization={{ slug: 'acme', name: 'Acme' }}
                project={{ id: 1, slug: 'proj', name: 'Project' }}
                tasks={{
                    data: [],
                    current_page: 1,
                    last_page: 1,
                    per_page: 15,
                    total: 0,
                    next_page_url: null,
                    prev_page_url: null,
                }}
                taskPreview={null}
                organizationMembers={[]}
            />,
        );

        expect(screen.getByText('Kanban board')).toBeInTheDocument();
        expect(screen.queryByText('Task table')).not.toBeInTheDocument();
    });
});
