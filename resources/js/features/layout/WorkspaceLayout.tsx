import type { ReactNode } from 'react';
import { usePage } from '@inertiajs/react';
import WorkspaceHeader from '@/features/layout/components/WorkspaceHeader/WorkspaceHeader';
import { LayoutShellProvider } from '@/features/layout/context/LayoutContext';

type WorkspaceLayoutProps = {
    children: ReactNode;
    onLogoutRequest: () => Promise<void> | void;
};

type SharedPageProps = {
    auth?: {
        user?: {
            name?: string;
            email?: string;
        } | null;
    };
};

export default function WorkspaceLayout({ children, onLogoutRequest }: WorkspaceLayoutProps) {
    const { props } = usePage<SharedPageProps>();

    const userName = props.auth?.user?.name ?? '-';
    const userEmail = props.auth?.user?.email ?? '-';

    return (
        <LayoutShellProvider
            mode="workspace"
            onLogoutRequest={onLogoutRequest}
        >
            <div className="flex min-h-screen flex-col">
                <WorkspaceHeader
                    userName={userName}
                    userEmail={userEmail}
                />
                <main className="container mx-auto flex-1 px-4 py-8">{children}</main>
            </div>
        </LayoutShellProvider>
    );
}
