import type { ReactNode } from 'react';
import { useEffect } from 'react';
import type { SharedUiBaseProps, SharedUiTheme } from '@/features/shared/ui/types';
import { resolveSharedUiTheme } from '@/features/shared/ui/types';

const panelByTheme: Record<SharedUiTheme, string> = {
    light: 'w-full max-w-lg rounded-3xl border border-gray-200 bg-white shadow-xl',
    dark: 'w-full max-w-lg rounded-3xl border border-gray-800 bg-gray-900 shadow-xl',
};

const headerBorderByTheme: Record<SharedUiTheme, string> = {
    light: 'flex items-start justify-between gap-4 border-b border-gray-200 p-5',
    dark: 'flex items-start justify-between gap-4 border-b border-gray-800 p-5',
};

const descriptionByTheme: Record<SharedUiTheme, string> = {
    light: 'mt-1 text-sm text-gray-500',
    dark: 'mt-1 text-sm text-gray-400',
};

const closeButtonByTheme: Record<SharedUiTheme, string> = {
    light:
        'rounded-2xl border border-gray-200 bg-white px-3 py-2 text-sm font-semibold hover:bg-gray-50',
    dark: 'rounded-2xl border border-gray-800 bg-gray-950 px-3 py-2 text-sm font-semibold hover:bg-gray-800',
};

const footerBorderByTheme: Record<SharedUiTheme, string> = {
    light: 'flex items-center justify-end gap-2 border-t border-gray-200 p-5',
    dark: 'flex items-center justify-end gap-2 border-t border-gray-800 p-5',
};

export interface ModalProps extends SharedUiBaseProps {
    isOpen: boolean;
    onClose: () => void;
    title: string;
    description?: string;
    children: ReactNode;
    footer?: ReactNode;
    /** Override panel width / layout (defaults to max-w-lg panel for this theme) */
    panelClassName?: string;
}

export default function Modal({
    isOpen,
    onClose,
    title,
    description,
    children,
    footer,
    theme,
    panelClassName,
}: ModalProps) {
    const t = resolveSharedUiTheme(theme);

    useEffect(() => {
        if (!isOpen) {
            return;
        }

        const onKeyDown = (event: KeyboardEvent): void => {
            if (event.key === 'Escape') {
                onClose();
            }
        };

        document.addEventListener('keydown', onKeyDown);

        return () => {
            document.removeEventListener('keydown', onKeyDown);
        };
    }, [isOpen, onClose]);

    if (!isOpen) {
        return null;
    }

    const handleBackdropClick = () => {
        onClose();
    };

    const panelClass = panelClassName ?? panelByTheme[t];

    return (
        <div className="fixed inset-0 z-[60]">
            <div
                className="absolute inset-0 bg-black/50"
                onClick={handleBackdropClick}
                aria-hidden="true"
            />

            <div className="absolute inset-0 grid place-items-center p-4">
                <div className={panelClass}>
                    <div className={headerBorderByTheme[t]}>
                        <div>
                            <div className="text-base font-semibold">{title}</div>
                            {description && (
                                <div className={descriptionByTheme[t]}>
                                    {description}
                                </div>
                            )}
                        </div>
                        <button
                            type="button"
                            onClick={onClose}
                            className={closeButtonByTheme[t]}
                            aria-label="Close modal"
                        >
                            ✕
                        </button>
                    </div>

                    <div className="space-y-4 p-5">{children}</div>

                    {footer && (
                        <div className={footerBorderByTheme[t]}>
                            {footer}
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
}
