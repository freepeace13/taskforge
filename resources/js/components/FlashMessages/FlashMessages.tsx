import { usePage } from '@inertiajs/react';
import FlashMessage from '@/features/shared/ui/FlashMessage';

type FlashPageProps = {
    flash?: {
        success?: string | null;
        error?: string | null;
    };
};

/**
 * Renders session flash messages from Inertia shared props. Use on page containers;
 * keeps `usePage` out of feature modules.
 */
export default function FlashMessages() {
    const { flash } = usePage<FlashPageProps>().props;

    const error = flash?.error ?? null;
    const success = flash?.success ?? null;

    if (!error && !success) {
        return null;
    }

    return (
        <div className="mb-4 space-y-3">
            {error ? <FlashMessage variant="error" message={error} /> : null}
            {success ? <FlashMessage variant="success" message={success} /> : null}
        </div>
    );
}
