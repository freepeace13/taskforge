import type { ReactNode } from 'react';
import { LayoutShellProvider } from '@/features/layout/context/LayoutContext';
import { useLayoutContext } from '@/features/layout/context/LayoutContext';
import {
    AppBar,
    Button,
    Dropdown,
    DropdownContent,
    DropdownTrigger,
    useDropdown,
} from '@/features/shared/ui';
import { BrandMark } from '@/features/layout/components/BrandMark';
import { useAuth } from '@/features/shared/context/AuthContext';
import { useTheme } from '../shared/context/ThemeContext';
import { initialsFromName } from '@/features/shared/utils';

type WorkspaceLayoutProps = {
    children: ReactNode;
};

type AccountMenuPanelProps = {
    userName: string;
    userEmail: string;
};

function AccountMenuPanel({ userName, userEmail }: AccountMenuPanelProps) {
    const { setOpen } = useDropdown();
    const { logout, isLoggingOut } = useLayoutContext();
    const { toggleDarkMode } = useTheme();

    return (
        <>
            <div className="border-b border-gray-100 px-4 py-3 dark:border-gray-800">
                <div className="truncate text-sm font-semibold text-gray-900 dark:text-gray-100">{userName}</div>
                <div className="truncate text-xs text-gray-500 dark:text-gray-400">{userEmail}</div>
            </div>

            <div className="py-1">
                <button
                    type="button"
                    role="menuitem"
                    className="flex w-full items-center gap-2 px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-800"
                    onClick={toggleDarkMode}
                >
                    <span aria-hidden>🌓</span>
                    Toggle theme
                </button>
            </div>

            <div className="border-t border-gray-100 px-3 py-2 dark:border-gray-800">
                <Button
                    type="button"
                    variant="secondary"
                    size="sm"
                    className="w-full"
                    disabled={isLoggingOut}
                    onClick={() => {
                        setOpen(false);
                        void logout();
                    }}
                >
                    {isLoggingOut ? 'Logging out…' : 'Log out'}
                </Button>
            </div>
        </>
    );
}

type WorkspaceHeaderProps = {
    userName: string;
    userEmail: string;
};

export function WorkspaceHeader({ userName, userEmail }: WorkspaceHeaderProps) {
    return (
        <AppBar>
            <div className="flex items-center justify-between px-4 py-3 lg:px-6">
                <div className="flex items-center gap-3">
                    <BrandMark />
                    <div>
                        <div className="text-sm font-bold tracking-tight">TaskForge</div>
                        <div className="text-xs text-gray-500 dark:text-gray-400">Multi-tenant workspace</div>
                    </div>
                </div>

                <Dropdown>
                    <DropdownTrigger
                        className="flex h-10 w-10 items-center justify-center rounded-2xl bg-gray-200 text-xs font-semibold text-gray-800 ring-brand-500/40 transition hover:bg-gray-300 focus:outline-none focus-visible:ring-2 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700"
                    >
                        <span className="sr-only">Open account menu</span>
                        <span aria-hidden>{initialsFromName(userName)}</span>
                    </DropdownTrigger>

                    <DropdownContent
                        menuLabel="Account"
                        className="w-64 py-2"
                    >
                        <AccountMenuPanel
                            userName={userName}
                            userEmail={userEmail}
                        />
                    </DropdownContent>
                </Dropdown>
            </div>
        </AppBar>
    );
}

export default function WorkspaceLayout({ children }: WorkspaceLayoutProps) {
    const { user } = useAuth();

    const userName = user?.name ?? '-';
    const userEmail = user?.email ?? '-';

    return (
        <LayoutShellProvider mode="workspace">
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
