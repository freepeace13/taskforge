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
            <div className="min-h-full">
                <WorkspaceHeader
                    userName={userName}
                    userEmail={userEmail}
                />
                <main className="px-4 py-6 lg:px-6">{children}</main>
            </div>
        </LayoutShellProvider>
    );
}
