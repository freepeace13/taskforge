import type { StatusVariant } from '../types';

export interface StatusBadgeProps {
    label: string;
    variant: StatusVariant;
}

const baseClasses =
    'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold';

const variantClasses: Record<StatusVariant, string> = {
    'in-progress':
        'bg-yellow-100 text-yellow-800 dark:bg-yellow-500/15 dark:text-yellow-200',
    done: 'bg-green-100 text-green-800 dark:bg-green-500/15 dark:text-green-200',
    backlog:
        'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-200',
};

export default function StatusBadge({ label, variant }: StatusBadgeProps) {
    const variantCss = variantClasses[variant];

    return (
        <span className={`${baseClasses} ${variantCss}`}>
            {label}
        </span>
    );
}

