import { Button, Dropdown, DropdownContent, DropdownTrigger, useDropdown } from '@/features/shared/ui';
import { BrandMark } from '@/features/layout/components/BrandMark';
import AppBar from '@/features/layout/components/shell/AppBar';
import { useLayoutContext } from '@/features/layout/context/LayoutContext';

function initialsFromName(name: string): string {
    const trimmed = name.trim();

    if (!trimmed) {
        return '?';
    }

    const parts = trimmed.split(/\s+/);

    if (parts.length >= 2) {
        return `${parts[0]![0]!}${parts[parts.length - 1]![0]!}`.toUpperCase();
    }

    return trimmed.slice(0, 2).toUpperCase();
}

type AccountMenuPanelProps = {
    userName: string;
    userEmail: string;
};

function AccountMenuPanel({ userName, userEmail }: AccountMenuPanelProps) {
    const { setOpen } = useDropdown();
    const { toggleDarkMode, logout, isLoggingOut } = useLayoutContext();

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
                    onClick={() => {
                        toggleDarkMode();
                    }}
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

export default function WorkspaceHeader({ userName, userEmail }: WorkspaceHeaderProps) {
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
