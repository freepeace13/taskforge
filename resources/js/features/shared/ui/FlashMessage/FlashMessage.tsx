export type FlashMessageVariant = 'success' | 'error';

export type FlashMessageProps = {
    variant: FlashMessageVariant;
    message: string;
};

const variantClasses: Record<FlashMessageVariant, string> = {
    success:
        'border-green-200 bg-green-50 text-green-900 dark:border-green-900/60 dark:bg-green-950/40 dark:text-green-100',
    error: 'border-red-200 bg-red-50 text-red-900 dark:border-red-900/60 dark:bg-red-950/40 dark:text-red-100',
};

export default function FlashMessage({ variant, message }: FlashMessageProps) {
    return (
        <div className={`rounded-2xl border px-4 py-3 text-sm ${variantClasses[variant]}`} role="alert">
            {message}
        </div>
    );
}
