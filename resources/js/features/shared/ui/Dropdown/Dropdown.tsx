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

type DropdownAlign = 'start' | 'end' | 'full';

type DropdownContextValue = {
    open: boolean;
    setOpen: (next: boolean) => void;
    menuId: string;
    containerRef: RefObject<HTMLDivElement | null>;
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

export interface DropdownProps {
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
        }),
        [open, setOpen, menuId],
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

export interface DropdownContentProps {
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

export function DropdownContent({
    children,
    className = '',
    align = 'end',
    menuLabel,
}: DropdownContentProps) {
    const { open, menuId } = useDropdownContext('DropdownContent');

    if (!open) {
        return null;
    }

    const positionClass = alignClasses[align];

    return (
        <div
            id={menuId}
            role="menu"
            aria-label={menuLabel}
            className={[
                'absolute z-50 mt-2 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900',
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
