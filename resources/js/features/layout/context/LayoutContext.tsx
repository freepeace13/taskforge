import type { ReactNode } from 'react';
import { createContext, useContext } from 'react';

export type LayoutContextValue = {
    toggleDarkMode: () => void;
    isSidebarOpen: boolean;
    openSidebar: () => void;
    closeSidebar: () => void;
    /** Navigate to the tasks hub (project picker). */
    goToTasksHub: () => void;
    logout: () => void;
    isLoggingOut: boolean;
};

type LayoutProviderProps = {
    value: LayoutContextValue;
    children: ReactNode;
};

const LayoutContext = createContext<LayoutContextValue | undefined>(undefined);

export function LayoutProvider({ value, children }: LayoutProviderProps) {
    return <LayoutContext.Provider value={value}>{children}</LayoutContext.Provider>;
}

export function useLayoutContext(): LayoutContextValue {
    const context = useContext(LayoutContext);

    if (!context) {
        throw new Error('useLayoutContext must be used within a LayoutProvider');
    }

    return context;
}
