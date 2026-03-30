import { fireEvent, render, screen } from '@testing-library/react';
import type { ReactElement } from 'react';
import { describe, expect, it, vi } from 'vitest';
import { ThemeProvider } from '@/features/shared/context/ThemeContext';
import TaskKanbanBoard from './TaskKanbanBoard';

vi.mock('@inertiajs/react', async () => {
    const actual = await vi.importActual<typeof import('@inertiajs/react')>('@inertiajs/react');

    return {
        ...actual,
        Link: ({ href, children, ...props }: { href: string; children: React.ReactNode }) => (
            <a href={href} {...props}>
                {children}
            </a>
        ),
    };
});

type Row = import('@/features/tasks/types').TaskTableRow;

const baseRow = (overrides: Partial<Row>) => ({
    id: 1,
    key: 'P-01',
    project_id: 1,
    assigned_to_user_id: null,
    title: 'Todo task',
    description: null,
    status: 'todo',
    priority: null,
    due_date: null,
    completed_at: null,
    created_at: '2026-01-01',
    updated_at: '2026-01-01',
    previewUrl: '/tasks?task=P-01',
    showUrl: '/tasks/1',
    editUrl: '/tasks/1/edit',
    ...overrides,
});

function renderWithTheme(ui: ReactElement) {
    return render(<ThemeProvider>{ui}</ThemeProvider>);
}

describe('TaskKanbanBoard', () => {
    it('renders columns and groups tasks by status', () => {
        const onMoveTask = vi.fn();

        renderWithTheme(
            <TaskKanbanBoard
                onMoveTask={onMoveTask}
                rows={[
                    baseRow({
                        id: 1,
                        title: 'Todo task',
                        status: 'todo',
                        previewUrl: '/tasks?task=P-01',
                        showUrl: '/tasks/1',
                    }),
                    baseRow({
                        id: 2,
                        key: 'P-02',
                        title: 'In progress task',
                        status: 'in_progress',
                        previewUrl: '/tasks?task=P-02',
                        showUrl: '/tasks/2',
                    }),
                    baseRow({
                        id: 3,
                        key: 'P-03',
                        title: 'Done task',
                        status: 'done',
                        previewUrl: '/tasks?task=P-03',
                        showUrl: '/tasks/3',
                    }),
                ]}
            />,
        );

        expect(screen.getByText('To do')).toBeInTheDocument();
        expect(screen.getByText('In progress')).toBeInTheDocument();
        expect(screen.getByText('Done')).toBeInTheDocument();

        expect(screen.getByRole('link', { name: 'Todo task' })).toHaveAttribute('href', '/tasks?task=P-01');
        expect(screen.getByRole('link', { name: 'In progress task' })).toHaveAttribute('href', '/tasks?task=P-02');
        expect(screen.getByRole('link', { name: 'Done task' })).toHaveAttribute('href', '/tasks?task=P-03');
    });

    it('calls onMoveTask when a card is dropped on another column', () => {
        const onMoveTask = vi.fn();

        renderWithTheme(
            <TaskKanbanBoard
                onMoveTask={onMoveTask}
                rows={[baseRow({ id: 1, title: 'Todo task', status: 'todo' })]}
            />,
        );

        const card = screen.getByRole('link', { name: 'Todo task' }).closest('[draggable]');
        expect(card).toBeTruthy();

        const dropZone = document.querySelector('[data-droppable-column="done"]');
        expect(dropZone).toBeTruthy();

        const payloadStore: Record<string, string> = {};
        const dataTransfer = {
            effectAllowed: 'move',
            dropEffect: 'move' as const,
            setData: (type: string, value: string) => {
                payloadStore[type] = value;
            },
            getData: (type: string) => payloadStore[type] ?? '',
            setDragImage: vi.fn(),
        };

        fireEvent.dragStart(card!, { dataTransfer, clientX: 10, clientY: 10 });

        fireEvent.dragOver(dropZone!, {
            dataTransfer,
            preventDefault: vi.fn(),
        });

        fireEvent.drop(dropZone!, {
            dataTransfer,
            preventDefault: vi.fn(),
        });

        expect(onMoveTask).toHaveBeenCalledTimes(1);
        expect(onMoveTask).toHaveBeenCalledWith(
            expect.objectContaining({ id: 1, title: 'Todo task', status: 'todo' }),
            'done',
        );
    });
});
