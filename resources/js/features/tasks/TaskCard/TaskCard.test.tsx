import { render } from '@testing-library/react';
import { describe, expect, it, vi } from 'vitest';
import TaskCard from './TaskCard';

vi.mock('@inertiajs/react', () => ({
    Link: ({ href, children, ...props }: { href: string; children: React.ReactNode }) => (
        <a href={href} {...props}>
            {children}
        </a>
    ),
}));

type Row = import('@/features/tasks/types').TaskTableRow;

const baseTask: Row = {
    id: 1,
    key: 'P-01',
    project_id: 1,
    assigned_to_user_id: null,
    title: 'Example',
    description: null,
    status: 'todo',
    priority: 'high',
    due_date: '2026-03-30',
    completed_at: null,
    created_at: '2026-01-01',
    updated_at: '2026-01-01',
    previewUrl: '/tasks?task=P-01',
    showUrl: '/tasks/1',
    editUrl: '/tasks/1/edit',
};

describe('TaskCard', () => {
    it('uses light theme surface classes when theme is light', () => {
        const { container } = render(<TaskCard task={baseTask} theme="light" />);
        const link = container.querySelector('a');
        expect(link?.className).toContain('border-gray-200');
        expect(link?.className).toContain('bg-white');
    });

    it('uses dark theme surface classes when theme is dark', () => {
        const { container } = render(<TaskCard task={baseTask} theme="dark" />);
        const link = container.querySelector('a');
        expect(link?.className).toContain('border-gray-800');
        expect(link?.className).toContain('bg-gray-950/40');
    });
});
