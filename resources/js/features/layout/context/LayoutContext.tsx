import { useAuth } from '@/features/shared/context/AuthContext';
import { router } from '@inertiajs/react';
import { route } from 'ziggy-js';
import type { ReactElement, ReactNode } from 'react';
import { createContext, useCallback, useContext, useEffect, useMemo, useState } from 'react';

export type AppChromeValue = {
    isSidebarOpen: boolean;
    openSidebar: () => void;
    closeSidebar: () => void;
    /** Navigate to the tasks hub (project picker). */
    goToTasksHub: () => void;
};

export type LayoutContextValue = {
    logout: () => Promise<void>;
    isLoggingOut: boolean;
    /** Present only when `LayoutShellProvider` is used with `mode="app"`. */
    appChrome: AppChromeValue | null;
};

type LayoutShellProviderProps = {
    children: ReactNode;
    mode: 'app' | 'workspace';
};

const LayoutContext = createContext<LayoutContextValue | undefined>(undefined);

export function LayoutShellProvider({
    children,
    mode,
}: LayoutShellProviderProps): ReactElement {
    const { logout: onLogoutRequest, tenant } = useAuth();
    const [isLoggingOut, setIsLoggingOut] = useState(false);
    const [isSidebarOpen, setIsSidebarOpen] = useState(false);

    useEffect(() => {
        if (mode !== 'app') {
            return;
        }

        const handleKeyDown = (event: KeyboardEvent) => {
            if (event.key === 'Escape') {
                setIsSidebarOpen(false);
            }
        };

        window.addEventListener('keydown', handleKeyDown);

        return () => {
            window.removeEventListener('keydown', handleKeyDown);
        };
    }, [mode]);

    const logout = useCallback(async () => {
        setIsLoggingOut(true);

        try {
            await Promise.resolve(onLogoutRequest());
        } finally {
            setIsLoggingOut(false);
        }
    }, [onLogoutRequest]);

    const goToTasksHub = useCallback(() => {
        if (!tenant?.slug) {
            return;
        }

        router.visit(route('tasks.hub', tenant.slug));
    }, [tenant?.slug]);

    const appChrome = useMemo<AppChromeValue | null>(() => {
        if (mode !== 'app') {
            return null;
        }

        return {
            isSidebarOpen,
            openSidebar: () => setIsSidebarOpen(true),
            closeSidebar: () => setIsSidebarOpen(false),
            goToTasksHub,
        };
    }, [mode, goToTasksHub, isSidebarOpen]);

    const value = useMemo<LayoutContextValue>(
        () => ({
            logout,
            isLoggingOut,
            appChrome,
        }),
        [logout, isLoggingOut, appChrome],
    );

    return <LayoutContext.Provider value={value}>{children}</LayoutContext.Provider>;
}

export function useLayoutContext(): LayoutContextValue {
    const context = useContext(LayoutContext);

    if (!context) {
        throw new Error('useLayoutContext must be used within a LayoutShellProvider');
    }

    return context;
}
