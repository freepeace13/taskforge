import { useCallback, useState } from 'react';
import { useTheme } from '@/features/shared/context/ThemeContext';
import { KanbanColumn } from '@/features/shared/ui';
import type { SharedUiTheme } from '@/features/shared/ui/types';
import { sharedUiThemeClass } from '@/features/shared/ui/types';
import type { TaskTableRow } from '@/features/tasks/types';
import TaskCard from '@/features/tasks/TaskCard';
import { alignDragPreviewToCursor } from '@/features/tasks/utils/alignDragPreviewToCursor';

export type TaskStatusColumnKey = 'todo' | 'in_progress' | 'done';

const columnLabelByKey: Record<TaskStatusColumnKey, string> = {
    todo: 'To do',
    in_progress: 'In progress',
    done: 'Done',
};

const indicatorColorByKey: Record<TaskStatusColumnKey, string> = {
    todo: 'bg-sky-500',
    in_progress: 'bg-amber-500',
    done: 'bg-emerald-500',
};

const emptyColumnPlaceholderByTheme: Record<SharedUiTheme, string> = {
    light:
        'rounded-3xl border border-dashed border-gray-200 bg-gray-50/80 px-4 py-8 text-center text-xs text-gray-500',
    dark:
        'rounded-3xl border border-dashed border-gray-800 bg-gray-900/40 px-4 py-8 text-center text-xs text-gray-400',
};

export const TASK_CARD_DRAG_TYPE = 'application/x-taskforge-task';

export type TaskKanbanBoardProps = {
    rows: TaskTableRow[];
    onMoveTask: (task: TaskTableRow, status: TaskStatusColumnKey) => void;
};

function groupRowsByStatus(rows: TaskTableRow[]): Record<TaskStatusColumnKey, TaskTableRow[]> {
    const columns: Record<TaskStatusColumnKey, TaskTableRow[]> = {
        todo: [],
        in_progress: [],
        done: [],
    };

    for (const row of rows) {
        if (row.status === 'todo' || row.status === 'in_progress' || row.status === 'done') {
            columns[row.status].push(row);
        } else {
            columns.todo.push(row);
        }
    }

    return columns;
}

export default function TaskKanbanBoard({ rows, onMoveTask }: TaskKanbanBoardProps) {
    const { isDark } = useTheme();
    const uiTheme: SharedUiTheme = isDark ? 'dark' : 'light';

    const columns = groupRowsByStatus(rows);
    const [dragOverColumn, setDragOverColumn] = useState<TaskStatusColumnKey | null>(null);
    const [draggingTaskId, setDraggingTaskId] = useState<number | null>(null);

    const handleDragStart = useCallback((task: TaskTableRow) => (event: React.DragEvent<HTMLDivElement>) => {
        alignDragPreviewToCursor(event);
        setDraggingTaskId(task.id);
        event.dataTransfer.setData(
            TASK_CARD_DRAG_TYPE,
            JSON.stringify({ id: task.id }),
        );
        event.dataTransfer.effectAllowed = 'move';
    }, []);

    const handleDragEnd = useCallback(() => {
        setDraggingTaskId(null);
        setDragOverColumn(null);
    }, []);

    const handleColumnDragOver = useCallback(
        (columnKey: TaskStatusColumnKey) => (event: React.DragEvent<HTMLDivElement>) => {
            event.preventDefault();
            event.dataTransfer.dropEffect = 'move';
            setDragOverColumn(columnKey);
        },
        [],
    );

    const handleColumnDragLeave = useCallback((event: React.DragEvent<HTMLDivElement>) => {
        const next = event.relatedTarget as Node | null;
        if (event.currentTarget.contains(next)) {
            return;
        }
        setDragOverColumn(null);
    }, []);

    const handleColumnDrop = useCallback(
        (columnKey: TaskStatusColumnKey) => (event: React.DragEvent<HTMLDivElement>) => {
            event.preventDefault();
            setDragOverColumn(null);
            setDraggingTaskId(null);

            const raw = event.dataTransfer.getData(TASK_CARD_DRAG_TYPE);
            if (!raw) {
                return;
            }

            let parsed: { id?: number };
            try {
                parsed = JSON.parse(raw) as { id?: number };
            } catch {
                return;
            }

            if (typeof parsed.id !== 'number') {
                return;
            }

            const task = rows.find((r) => r.id === parsed.id);
            if (!task) {
                return;
            }

            if (task.status === columnKey) {
                return;
            }

            onMoveTask(task, columnKey);
        },
        [onMoveTask, rows],
    );

    return (
        <div className="overflow-x-auto">
            <div className="flex min-w-max gap-4">
                {(Object.keys(columns) as TaskStatusColumnKey[]).map((key) => {
                    const isDropTarget = dragOverColumn === key;
                    const dropRingClass = isDropTarget
                        ? sharedUiThemeClass(uiTheme, {
                              light: 'ring-2 ring-brand-500/50 ring-offset-2 ring-offset-white',
                              dark: 'ring-2 ring-brand-500/50 ring-offset-2 ring-offset-gray-900',
                          })
                        : '';

                    return (
                        <KanbanColumn
                            key={key}
                            title={columnLabelByKey[key]}
                            count={columns[key].length}
                            indicatorColorClass={indicatorColorByKey[key]}
                            theme={uiTheme}
                        >
                            <div
                                data-droppable-column={key}
                                className={`min-h-[4.5rem] rounded-2xl transition ${dropRingClass}`}
                                onDragOver={handleColumnDragOver(key)}
                                onDragLeave={handleColumnDragLeave}
                                onDrop={handleColumnDrop(key)}
                            >
                                {columns[key].length > 0 ? (
                                    <div className="space-y-3">
                                        {columns[key].map((task) => (
                                            <TaskCard
                                                key={task.id}
                                                task={task}
                                                draggable
                                                dimmed={draggingTaskId === task.id}
                                                onDragStart={handleDragStart(task)}
                                                onDragEnd={handleDragEnd}
                                                theme={uiTheme}
                                            />
                                        ))}
                                    </div>
                                ) : (
                                    <p className={emptyColumnPlaceholderByTheme[uiTheme]}>
                                        No tasks.
                                    </p>
                                )}
                            </div>
                        </KanbanColumn>
                    );
                })}
            </div>
        </div>
    );
}
