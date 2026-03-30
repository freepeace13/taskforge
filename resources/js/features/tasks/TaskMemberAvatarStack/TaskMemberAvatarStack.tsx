import type { TaskMember } from '@/features/tasks/types';
import type { SharedUiBaseProps, SharedUiTheme } from '@/features/shared/ui/types';
import { resolveSharedUiTheme } from '@/features/shared/ui/types';
import { memberInitials } from '@/features/tasks/utils/memberInitials';

const stackByTheme: Record<SharedUiTheme, string> = {
    light: 'flex items-center',
    dark: 'flex items-center',
};

const avatarByTheme: Record<SharedUiTheme, string> = {
    light:
        'inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full border-2 border-white bg-gray-200 text-[10px] font-semibold text-gray-700 ring-1 ring-gray-100',
    dark: 'inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full border-2 border-gray-950 bg-gray-700 text-[10px] font-semibold text-gray-100 ring-1 ring-gray-800',
};

const overflowByTheme: Record<SharedUiTheme, string> = {
    light:
        'inline-flex h-7 min-w-7 shrink-0 items-center justify-center rounded-full border-2 border-white bg-gray-100 px-1 text-[10px] font-semibold text-gray-600 ring-1 ring-gray-100',
    dark: 'inline-flex h-7 min-w-7 shrink-0 items-center justify-center rounded-full border-2 border-gray-950 bg-gray-800 px-1 text-[10px] font-semibold text-gray-300 ring-1 ring-gray-800',
};

export type TaskMemberAvatarStackProps = SharedUiBaseProps & {
    members: TaskMember[];
    /** Max avatars before +N overflow (default 3). */
    maxVisible?: number;
};

export default function TaskMemberAvatarStack({ members, maxVisible = 3, theme }: TaskMemberAvatarStackProps) {
    const t = resolveSharedUiTheme(theme);
    const visible = members.slice(0, maxVisible);
    const overflow = members.length - visible.length;

    if (members.length === 0) {
        return null;
    }

    return (
        <div className={stackByTheme[t]} aria-label="Task members">
            {visible.map((m, index) => (
                <span
                    key={m.id}
                    className={[avatarByTheme[t], index > 0 ? '-ml-2' : ''].filter(Boolean).join(' ')}
                    title={`${m.name}`}
                    aria-hidden="true"
                >
                    {memberInitials(m.name)}
                </span>
            ))}
            {overflow > 0 ? (
                <span className={[overflowByTheme[t], visible.length > 0 ? '-ml-2' : ''].filter(Boolean).join(' ')} title={`${overflow} more`}>
                    +{overflow}
                </span>
            ) : null}
        </div>
    );
}
