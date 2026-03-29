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
    toggleDarkMode: () => void;
    logout: () => Promise<void>;
    isLoggingOut: boolean;
    /** Present only when `LayoutShellProvider` is used with `mode="app"`. */
    appChrome: AppChromeValue | null;
};

type LayoutShellProviderProps = {
    children: ReactNode;
    mode: 'app' | 'workspace';
    onLogoutRequest: () => Promise<void> | void;
    /** When `mode` is `app`, optional handler for the tasks hub action; defaults to a no-op. */
    goToTasksHub?: () => void;
};

const LayoutContext = createContext<LayoutContextValue | undefined>(undefined);

export function LayoutShellProvider({
    children,
    mode,
    onLogoutRequest,
    goToTasksHub,
}: LayoutShellProviderProps): ReactElement {
    const [isDark, setIsDark] = useState(false);
    const [isLoggingOut, setIsLoggingOut] = useState(false);
    const [isSidebarOpen, setIsSidebarOpen] = useState(false);

    useEffect(() => {
        const root = document.documentElement;
        const savedTheme = window.localStorage.getItem('theme');

        if (savedTheme === 'dark') {
            root.classList.add('dark');
            setIsDark(true);
        } else {
            root.classList.remove('dark');
            setIsDark(false);
        }
    }, []);

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

    const toggleDarkMode = useCallback(() => {
        const root = document.documentElement;
        const nextIsDark = !isDark;

        setIsDark(nextIsDark);
        root.classList.toggle('dark', nextIsDark);
        window.localStorage.setItem('theme', nextIsDark ? 'dark' : 'light');
    }, [isDark]);

    const logout = useCallback(async () => {
        setIsLoggingOut(true);

        try {
            await Promise.resolve(onLogoutRequest());
        } finally {
            setIsLoggingOut(false);
        }
    }, [onLogoutRequest]);

    const appChrome = useMemo<AppChromeValue | null>(() => {
        if (mode !== 'app') {
            return null;
        }

        return {
            isSidebarOpen,
            openSidebar: () => setIsSidebarOpen(true),
            closeSidebar: () => setIsSidebarOpen(false),
            goToTasksHub: goToTasksHub ?? (() => {}),
        };
    }, [mode, goToTasksHub, isSidebarOpen]);

    const value = useMemo<LayoutContextValue>(
        () => ({
            toggleDarkMode,
            logout,
            isLoggingOut,
            appChrome,
        }),
        [toggleDarkMode, logout, isLoggingOut, appChrome],
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
