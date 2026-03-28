import type { ReactNode } from 'react';

type ProjectFormFooterProps = {
    submitLabel: string;
    processingLabel: string;
    processing: boolean;
    cancel: ReactNode;
};

export default function ProjectFormFooter({
    submitLabel,
    processingLabel,
    processing,
    cancel,
}: ProjectFormFooterProps) {
    return (
        <div className="mt-8 flex flex-wrap gap-3">
            <button
                type="submit"
                disabled={processing}
                className="inline-flex items-center justify-center rounded-2xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500/40 disabled:opacity-60"
            >
                {processing ? processingLabel : submitLabel}
            </button>
            {cancel}
        </div>
    );
}
