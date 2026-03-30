import type { ButtonHTMLAttributes, ReactNode } from 'react';
import type { SharedUiBaseProps, SharedUiTheme } from '@/features/shared/ui/types';
import { resolveSharedUiTheme } from '@/features/shared/ui/types';

type ButtonVariant = 'primary' | 'secondary' | 'destructive' | 'ghost';
type ButtonSize = 'sm' | 'md' | 'lg';

export interface ButtonProps extends ButtonHTMLAttributes<HTMLButtonElement>, SharedUiBaseProps {
    variant?: ButtonVariant;
    size?: ButtonSize;
    children: ReactNode;
}

const baseClasses =
    'inline-flex items-center justify-center gap-2 font-semibold focus:outline-none focus:ring-2 disabled:opacity-60 disabled:cursor-not-allowed';

const sizeClasses: Record<ButtonSize, string> = {
    sm: 'rounded-xl px-3 py-1.5 text-xs',
    md: 'rounded-2xl px-4 py-2 text-sm',
    lg: 'rounded-2xl px-5 py-3 text-base',
};

const primaryClasses =
    'bg-brand-600 text-white shadow-sm hover:bg-brand-700 focus:ring-brand-500/40';

const destructiveClasses =
    'bg-red-600 text-white shadow-sm hover:bg-red-700 focus:ring-red-500/40';

const secondaryByTheme: Record<SharedUiTheme, string> = {
    light:
        'border border-gray-200 bg-white text-gray-900 shadow-sm hover:bg-gray-50 focus:ring-brand-500/30',
    dark: 'border border-gray-800 bg-gray-900 text-gray-100 shadow-sm hover:bg-gray-800 focus:ring-brand-500/30',
};

const ghostByTheme: Record<SharedUiTheme, string> = {
    light: 'text-gray-700 hover:bg-gray-100 focus:ring-brand-500/30',
    dark: 'text-gray-200 hover:bg-gray-900 focus:ring-brand-500/30',
};

export default function Button({
    variant = 'primary',
    size = 'sm',
    className = '',
    children,
    theme,
    ...props
}: ButtonProps) {
    const t = resolveSharedUiTheme(theme);
    const sizeCss = sizeClasses[size] ?? sizeClasses.md;

    let variantCss: string;
    if (variant === 'primary') {
        variantCss = primaryClasses;
    } else if (variant === 'destructive') {
        variantCss = destructiveClasses;
    } else if (variant === 'secondary') {
        variantCss = secondaryByTheme[t];
    } else {
        variantCss = ghostByTheme[t];
    }

    const mergedClassName = [baseClasses, sizeCss, variantCss, className].filter(Boolean).join(' ');

    return (
        <button type="button" {...props} className={mergedClassName}>
            {children}
        </button>
    );
}
