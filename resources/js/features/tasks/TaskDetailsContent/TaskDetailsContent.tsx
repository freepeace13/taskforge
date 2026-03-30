import { useMemo } from 'react';
import type { TaskAttributes, TaskMember } from '@/features/tasks/types';
import { memberInitials } from '@/features/tasks/utils/memberInitials';

export type TaskDetailsContentProps = {
    task: TaskAttributes;
    /** When true, omit outer card (e.g. inside a modal that already provides a panel) */
    embedded?: boolean;
    /** Organization members (for add/remove). When omitted with no callback, members are display-only. */
    organizationMembers?: TaskMember[];
    onMembersChange?: (memberIds: number[]) => void;
    membersSaving?: boolean;
};

export default function TaskDetailsContent({
    task,
    embedded = false,
    organizationMembers,
    onMembersChange,
    membersSaving = false,
}: TaskDetailsContentProps) {
    const currentMembers = task.members ?? [];

    const memberIds = useMemo(() => currentMembers.map((m) => m.id), [currentMembers]);

    const availableToAdd = useMemo(() => {
        if (!organizationMembers) {
            return [];
        }

        return organizationMembers.filter((m) => !memberIds.includes(m.id));
    }, [organizationMembers, memberIds]);

    const canEditMembers = Boolean(organizationMembers && onMembersChange);

    const addMember = (userId: number) => {
        if (!onMembersChange || membersSaving) {
            return;
        }

        onMembersChange([...memberIds, userId]);
    };

    const removeMember = (userId: number) => {
        if (!onMembersChange || membersSaving) {
            return;
        }

        onMembersChange(memberIds.filter((id) => id !== userId));
    };

    const membersSection = (
        <div className={embedded ? 'mt-6 border-t border-gray-200 pt-6 dark:border-gray-800' : 'mt-6'}>
            <h3 className="text-sm font-semibold text-gray-900 dark:text-gray-100">Members</h3>
            {currentMembers.length === 0 && !canEditMembers ? (
                <p className="mt-2 text-sm text-gray-500 dark:text-gray-400">No members assigned.</p>
            ) : null}

            {currentMembers.length > 0 ? (
                <ul className="mt-3 flex flex-wrap gap-2">
                    {currentMembers.map((m) => (
                        <li key={m.id}>
                            <span className="inline-flex items-center gap-2 rounded-2xl border border-gray-200 bg-gray-50 py-1 pl-1 pr-2 text-sm text-gray-900 dark:border-gray-700 dark:bg-gray-800/60 dark:text-gray-100">
                                <span
                                    className="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-100 text-xs font-semibold text-brand-800 dark:bg-brand-900/40 dark:text-brand-200"
                                    aria-hidden="true"
                                >
                                    {memberInitials(m.name)}
                                </span>
                                <span className="max-w-[12rem] truncate">{m.name}</span>
                                {canEditMembers ? (
                                    <button
                                        type="button"
                                        className="rounded-lg px-1.5 text-xs font-semibold text-gray-500 hover:bg-gray-200 hover:text-gray-900 disabled:opacity-50 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-100"
                                        disabled={membersSaving}
                                        onClick={() => removeMember(m.id)}
                                        aria-label={`Remove ${m.name} from task`}
                                    >
                                        Remove
                                    </button>
                                ) : null}
                            </span>
                        </li>
                    ))}
                </ul>
            ) : null}

            {canEditMembers && availableToAdd.length > 0 ? (
                <div className="mt-4">
                    <label htmlFor="task-add-member" className="sr-only">
                        Add member
                    </label>
                    <select
                        id="task-add-member"
                        className="w-full max-w-md rounded-2xl border border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30 disabled:opacity-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                        disabled={membersSaving}
                        defaultValue=""
                        onChange={(e) => {
                            const value = e.target.value;
                            e.target.value = '';
                            if (value === '') {
                                return;
                            }

                            addMember(Number.parseInt(value, 10));
                        }}
                    >
                        <option value="">Add a member…</option>
                        {availableToAdd.map((m) => (
                            <option key={m.id} value={m.id}>
                                {m.name}
                            </option>
                        ))}
                    </select>
                </div>
            ) : null}

            {canEditMembers && availableToAdd.length === 0 && currentMembers.length === 0 ? (
                <p className="mt-2 text-sm text-gray-500 dark:text-gray-400">No organization members to add.</p>
            ) : null}
        </div>
    );

    const body = (
        <>
            {task.description ? (
                <p className="whitespace-pre-wrap text-sm text-gray-800 dark:text-gray-200">{task.description}</p>
            ) : (
                <p className="text-sm text-gray-500 dark:text-gray-400">No description.</p>
            )}

            <dl className="mt-6 grid gap-3 text-sm sm:grid-cols-2">
                <div>
                    <dt className="font-semibold text-gray-500 dark:text-gray-400">Due date</dt>
                    <dd className="text-gray-900 dark:text-gray-100">{task.due_date ?? '—'}</dd>
                </div>
                <div>
                    <dt className="font-semibold text-gray-500 dark:text-gray-400">Completed</dt>
                    <dd className="text-gray-900 dark:text-gray-100">{task.completed_at ?? '—'}</dd>
                </div>
            </dl>

            {membersSection}
        </>
    );

    if (embedded) {
        return body;
    }

    return (
        <div className="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            {body}
        </div>
    );
}
