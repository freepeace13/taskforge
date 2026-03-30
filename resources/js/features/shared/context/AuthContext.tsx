import type { ReactNode } from 'react';
import { createContext, useMemo, useContext, useCallback } from 'react';
import { router, usePage } from '@inertiajs/react';
import { route } from 'ziggy-js';

export type User = {
    id: number;
    authId: string | null;
    name: string;
    email: string;
};

export type Organization = {
    id: number;
    slug: string;
    name: string;
    role: string;
};

export type AuthContextValue = {
    user: User | null;
    tenant: Organization | null;
    organizations: Organization[];
    switchTenant: (tenant: Organization) => void;
    logout: () => void;
};

const AuthContext = createContext<AuthContextValue | undefined>(undefined);

export type AuthProps = {
    user?: User | null;
    tenant?: Organization | null;
    organizations?: Organization[];
};

type SharedPageProps = {
    auth?: AuthProps;
};

export function AuthProvider({
    children,
}: {
    children: ReactNode;
}) {
    const auth = usePage<SharedPageProps>().props.auth;

    const switchTenant = useCallback((nextTenant: Organization) => {
        router.visit(route('dashboard', { org: nextTenant.slug }));
    }, []);

    const logout = useCallback(() => {
        router.post(route('logout'));
    }, []);

    const value = useMemo<AuthContextValue>(() => {
        const user = auth?.user ?? null;
        const tenant = auth?.tenant ?? null;
        const organizations = auth?.organizations ?? [];

        return {
            user,
            tenant,
            organizations,
            switchTenant,
            logout,
        };
    }, [auth?.organizations, auth?.tenant, auth?.user, logout, switchTenant]);

    return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}

export function useAuth(): AuthContextValue {
    const context = useContext(AuthContext);
    if (!context) {
        throw new Error('useAuth must be used within a AuthProvider');
    }
    return context;
}
