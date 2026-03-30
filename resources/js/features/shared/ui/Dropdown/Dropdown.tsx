import {
    createContext,
    useCallback,
    useContext,
    useEffect,
    useId,
    useMemo,
    useRef,
    useState,
} from 'react';
import type { ButtonHTMLAttributes, ReactNode, RefObject } from 'react';
import type { SharedUiBaseProps, SharedUiTheme } from '@/features/shared/ui/types';
import { resolveSharedUiTheme } from '@/features/shared/ui/types';

type DropdownAlign = 'start' | 'end' | 'full';

type DropdownContextValue = {
    open: boolean;
    setOpen: (next: boolean) => void;
    menuId: string;
    containerRef: RefObject<HTMLDivElement | null>;
    theme: SharedUiTheme;
};

const DropdownContext = createContext<DropdownContextValue | null>(null);

function useDropdownContext(component: string): DropdownContextValue {
    const context = useContext(DropdownContext);

    if (!context) {
        throw new Error(`${component} must be used within <Dropdown>`);
    }

    return context;
}

/**
 * Access dropdown open state from content rendered inside {@link Dropdown}.
 * Use to programmatically close the panel (for example before navigation or logout).
 */
export function useDropdown(): DropdownContextValue {
    return useDropdownContext('useDropdown');
}

export interface DropdownProps extends SharedUiBaseProps {
    children: ReactNode;
    className?: string;
    defaultOpen?: boolean;
    open?: boolean;
    onOpenChange?: (open: boolean) => void;
}

export default function Dropdown({
    children,
    className = '',
    defaultOpen = false,
    open: openControlled,
    onOpenChange,
    theme,
}: DropdownProps) {
    const [uncontrolledOpen, setUncontrolledOpen] = useState(defaultOpen);
    const isControlled = openControlled !== undefined;
    const open = isControlled ? openControlled : uncontrolledOpen;

    const setOpen = useCallback(
        (next: boolean) => {
            if (!isControlled) {
                setUncontrolledOpen(next);
            }

            onOpenChange?.(next);
        },
        [isControlled, onOpenChange],
    );

    const containerRef = useRef<HTMLDivElement>(null);
    const menuId = useId();
    const resolvedTheme = resolveSharedUiTheme(theme);

    useEffect(() => {
        if (!open) {
            return;
        }

        const handlePointerDown = (event: MouseEvent) => {
            if (containerRef.current?.contains(event.target as Node)) {
                return;
            }

            setOpen(false);
        };

        const handleEscape = (event: KeyboardEvent) => {
            if (event.key === 'Escape') {
                setOpen(false);
            }
        };

        document.addEventListener('mousedown', handlePointerDown);
        document.addEventListener('keydown', handleEscape);

        return () => {
            document.removeEventListener('mousedown', handlePointerDown);
            document.removeEventListener('keydown', handleEscape);
        };
    }, [open, setOpen]);

    const value = useMemo(
        () => ({
            open,
            setOpen,
            menuId,
            containerRef,
            theme: resolvedTheme,
        }),
        [open, setOpen, menuId, resolvedTheme],
    );

    return (
        <DropdownContext.Provider value={value}>
            <div
                ref={containerRef}
                className={['relative', className].filter(Boolean).join(' ')}
            >
                {children}
            </div>
        </DropdownContext.Provider>
    );
}

export type DropdownTriggerProps = ButtonHTMLAttributes<HTMLButtonElement>;

export function DropdownTrigger({ type = 'button', onClick, ...props }: DropdownTriggerProps) {
    const { open, setOpen, menuId } = useDropdownContext('DropdownTrigger');

    return (
        <button
            type={type}
            aria-expanded={open}
            aria-controls={menuId}
            aria-haspopup="menu"
            onClick={(event) => {
                onClick?.(event);

                if (event.defaultPrevented) {
                    return;
                }

                setOpen(!open);
            }}
            {...props}
        />
    );
}

export interface DropdownContentProps extends SharedUiBaseProps {
    children: ReactNode;
    className?: string;
    align?: DropdownAlign;
    menuLabel?: string;
}

const alignClasses: Record<DropdownAlign, string> = {
    start: 'left-0',
    end: 'right-0',
    full: 'left-0 right-0',
};

const panelByTheme: Record<SharedUiTheme, string> = {
    light: 'absolute z-50 mt-2 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-lg',
    dark: 'absolute z-50 mt-2 overflow-hidden rounded-2xl border border-gray-800 bg-gray-900 shadow-lg',
};

export function DropdownContent({
    children,
    className = '',
    align = 'end',
    menuLabel,
    theme,
}: DropdownContentProps) {
    const { open, menuId, theme: parentTheme } = useDropdownContext('DropdownContent');

    if (!open) {
        return null;
    }

    const positionClass = alignClasses[align];
    const t = resolveSharedUiTheme(theme ?? parentTheme);

    return (
        <div
            id={menuId}
            role="menu"
            aria-label={menuLabel}
            className={[
                panelByTheme[t],
                positionClass,
                className,
            ]
                .filter(Boolean)
                .join(' ')}
        >
            {children}
        </div>
    );
}
