import type { DragEvent } from 'react';
import { Link } from '@inertiajs/react';
import type { SharedUiBaseProps, SharedUiTheme } from '@/features/shared/ui/types';
import { resolveSharedUiTheme } from '@/features/shared/ui/types';
import TaskMemberAvatarStack from '@/features/tasks/TaskMemberAvatarStack/TaskMemberAvatarStack';
import type { TaskTableRow } from '@/features/tasks/types';

const cardByTheme: Record<SharedUiTheme, string> = {
    light: 'rounded-3xl border border-gray-200 bg-white p-4 shadow-sm transition hover:shadow-md',
    dark: 'rounded-3xl border border-gray-800 bg-gray-950/40 p-4 shadow-sm transition hover:shadow-md',
};

const titleByTheme: Record<SharedUiTheme, string> = {
    light: 'text-sm font-semibold text-gray-900',
    dark: 'text-sm font-semibold text-gray-100',
};

const metaByTheme: Record<SharedUiTheme, string> = {
    light: 'mt-1 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-gray-500',
    dark: 'mt-1 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-gray-400',
};

const membersRowByTheme: Record<SharedUiTheme, string> = {
    light: 'mt-3 flex items-center justify-end',
    dark: 'mt-3 flex items-center justify-end',
};

export interface TaskCardProps extends SharedUiBaseProps {
    task: TaskTableRow;
    draggable?: boolean;
    dimmed?: boolean;
    onDragStart?: (event: DragEvent<HTMLDivElement>) => void;
    onDragEnd?: (event: DragEvent<HTMLDivElement>) => void;
}

export default function TaskCard({
    task,
    draggable = false,
    dimmed = false,
    onDragStart,
    onDragEnd,
    theme,
}: TaskCardProps) {
    const t = resolveSharedUiTheme(theme);

    const cardClassName = [cardByTheme[t], dimmed ? 'opacity-50' : ''].filter(Boolean).join(' ');

    const members = task.members ?? [];

    const body = (
        <>
            <div className={titleByTheme[t]}>{task.title}</div>
            <div className={metaByTheme[t]}>
                {task.priority ? <span className="capitalize">{task.priority}</span> : null}
                {task.priority && task.due_date ? <span aria-hidden="true">·</span> : null}
                {task.due_date ? <span>{task.due_date}</span> : null}
            </div>
            {members.length > 0 ? (
                <div className={membersRowByTheme[t]}>
                    <TaskMemberAvatarStack members={members} theme={theme} maxVisible={3} />
                </div>
            ) : null}
        </>
    );

    if (draggable) {
        return (
            <div
                draggable
                onDragStart={onDragStart}
                onDragEnd={onDragEnd}
                className={`cursor-grab active:cursor-grabbing ${cardClassName}`}
            >
                <Link href={task.previewUrl} className="block outline-none">
                    {body}
                </Link>
            </div>
        );
    }

    return (
        <Link href={task.previewUrl} className={`block ${cardClassName}`}>
            {body}
        </Link>
    );
}
