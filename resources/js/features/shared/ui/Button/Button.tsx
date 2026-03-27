import type { ButtonHTMLAttributes, ReactNode } from 'react';

type ButtonVariant = 'primary' | 'secondary' | 'destructive' | 'ghost';
type ButtonSize = 'sm' | 'md' | 'lg';

export interface ButtonProps extends ButtonHTMLAttributes<HTMLButtonElement> {
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

const variantClasses: Record<ButtonVariant, string> = {
    primary: 'bg-brand-600 text-white shadow-sm hover:bg-brand-700 focus:ring-brand-500/40',
    secondary:
        'border border-gray-200 bg-white text-gray-900 shadow-sm hover:bg-gray-50 focus:ring-brand-500/30 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-100 dark:hover:bg-gray-800',
    destructive: 'bg-red-600 text-white shadow-sm hover:bg-red-700 focus:ring-red-500/40',
    ghost: 'text-gray-700 hover:bg-gray-100 focus:ring-brand-500/30 dark:text-gray-200 dark:hover:bg-gray-900',
};

export default function Button({
    variant = 'primary',
    size = 'sm',
    className = '',
    children,
    ...props
}: ButtonProps) {
    const sizeCss = sizeClasses[size] ?? sizeClasses.md;
    const variantCss = variantClasses[variant] ?? variantClasses.primary;

    const mergedClassName = [baseClasses, sizeCss, variantCss, className].filter(Boolean).join(' ');

    return (
        <button type="button" {...props} className={mergedClassName}>
            {children}
        </button>
    );
}

