import type { StatusVariant } from '@/features/shared/types';
import type { SharedUiBaseProps, SharedUiTheme } from '@/features/shared/ui/types';
import { resolveSharedUiTheme } from '@/features/shared/ui/types';

const baseClasses =
    'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold';

const variantByTheme: Record<StatusVariant, Record<SharedUiTheme, string>> = {
    'in-progress': {
        light: 'bg-yellow-100 text-yellow-800',
        dark: 'bg-yellow-500/15 text-yellow-200',
    },
    done: {
        light: 'bg-green-100 text-green-800',
        dark: 'bg-green-500/15 text-green-200',
    },
    backlog: {
        light: 'bg-gray-100 text-gray-700',
        dark: 'bg-gray-800 text-gray-200',
    },
};

export interface StatusBadgeProps extends SharedUiBaseProps {
    label: string;
    variant: StatusVariant;
}

export default function StatusBadge({ label, variant, theme }: StatusBadgeProps) {
    const t = resolveSharedUiTheme(theme);
    const variantCss = variantByTheme[variant][t];

    return (
        <span className={`${baseClasses} ${variantCss}`}>
            {label}
        </span>
    );
}
