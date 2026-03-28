import { usePage } from '@inertiajs/react';

type FlashProps = {
    flash?: { success?: string | null };
};

export default function FlashSuccess() {
    const { flash } = usePage<FlashProps>().props;
    const message = flash?.success;

    if (!message) {
        return null;
    }

    return (
        <div className="mb-4 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-900 dark:border-green-900/60 dark:bg-green-950/40 dark:text-green-100">
            {message}
        </div>
    );
}
