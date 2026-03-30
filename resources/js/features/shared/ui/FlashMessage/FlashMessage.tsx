import type { SharedUiBaseProps, SharedUiTheme } from '@/features/shared/ui/types';
import { resolveSharedUiTheme } from '@/features/shared/ui/types';

export type FlashMessageVariant = 'success' | 'error';

export interface FlashMessageProps extends SharedUiBaseProps {
    variant: FlashMessageVariant;
    message: string;
}

const variantByTheme: Record<FlashMessageVariant, Record<SharedUiTheme, string>> = {
    success: {
        light: 'border-green-200 bg-green-50 text-green-900',
        dark: 'border-green-900/60 bg-green-950/40 text-green-100',
    },
    error: {
        light: 'border-red-200 bg-red-50 text-red-900',
        dark: 'border-red-900/60 bg-red-950/40 text-red-100',
    },
};

export default function FlashMessage({ variant, message, theme }: FlashMessageProps) {
    const t = resolveSharedUiTheme(theme);
    const variantCss = variantByTheme[variant][t];

    return (
        <div className={`rounded-2xl border px-4 py-3 text-sm ${variantCss}`} role="alert">
            {message}
        </div>
    );
}
