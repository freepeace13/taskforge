import type { ReactNode } from 'react';

export interface ModalProps {
    isOpen: boolean;
    onClose: () => void;
    title: string;
    description?: string;
    children: ReactNode;
    footer?: ReactNode;
}

export default function Modal({ isOpen, onClose, title, description, children, footer }: ModalProps) {
    if (!isOpen) {
        return null;
    }

    const handleBackdropClick = () => {
        onClose();
    };

    return (
        <div className="fixed inset-0 z-[60]">
            <div
                className="absolute inset-0 bg-black/50"
                onClick={handleBackdropClick}
                aria-hidden="true"
            />

            <div className="absolute inset-0 grid place-items-center p-4">
                <div className="w-full max-w-lg rounded-3xl border border-gray-200 bg-white shadow-xl dark:border-gray-800 dark:bg-gray-900">
                    <div className="flex items-start justify-between gap-4 border-b border-gray-200 p-5 dark:border-gray-800">
                        <div>
                            <div className="text-base font-semibold">{title}</div>
                            {description && (
                                <div className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    {description}
                                </div>
                            )}
                        </div>
                        <button
                            type="button"
                            onClick={onClose}
                            className="rounded-2xl border border-gray-200 bg-white px-3 py-2 text-sm font-semibold hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-950 dark:hover:bg-gray-800"
                            aria-label="Close modal"
                        >
                            ✕
                        </button>
                    </div>

                    <div className="space-y-4 p-5">{children}</div>

                    {footer && (
                        <div className="flex items-center justify-end gap-2 border-t border-gray-200 p-5 dark:border-gray-800">
                            {footer}
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
}

