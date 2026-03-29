import { Button, Dropdown, DropdownContent, DropdownTrigger } from '@/features/shared/ui';
import { BrandMark } from '@/features/layout/components/BrandMark';
import SideBar from '@/features/layout/components/shell/SideBar';
import SideBarItem from '@/features/layout/components/shell/SideBarItem';

type SidebarNavItem = {
    href: string;
    label: string;
    icon: string;
    isActive?: boolean;
};

type AppSidebarProps = {
    isOpen: boolean;
    onClose: () => void;
    onToggleDarkMode: () => void;
    onLogout: () => void | Promise<void>;
    isLoggingOut: boolean;
    userName: string;
    userEmail: string;
    navItems: SidebarNavItem[];
};

type WorkspaceDropdownProps = {
    userEmail: string;
};

function WorkspaceDropdown({ userEmail }: WorkspaceDropdownProps) {
    return (
        <div className="px-5 pb-4">
            <label
                className="block text-xs font-medium text-gray-500 dark:text-gray-400"
                htmlFor="workspace-dropdown-trigger"
            >
                Organization
            </label>
            <Dropdown className="mt-2">
                <DropdownTrigger
                    id="workspace-dropdown-trigger"
                    className="w-full rounded-2xl border border-gray-200 bg-white px-3 py-2 text-left text-sm font-semibold shadow-sm hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-900 dark:hover:bg-gray-800"
                >
                    <div className="flex items-center justify-between">
                        <span className="truncate">My workspace</span>
                        <span className="text-gray-400">⌄</span>
                    </div>
                    <div className="mt-1 truncate text-xs text-gray-500 dark:text-gray-400">{userEmail}</div>
                </DropdownTrigger>

                <DropdownContent
                    align="full"
                    menuLabel="Workspaces"
                    className="py-2"
                >
                    <div className="px-3 py-2 text-xs font-medium text-gray-500 dark:text-gray-400">Current</div>
                    <div className="px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100">My workspace</div>
                </DropdownContent>
            </Dropdown>
        </div>
    );
}

export default function AppSidebar({
    isOpen,
    onClose,
    onToggleDarkMode,
    onLogout,
    isLoggingOut,
    userName,
    userEmail,
    navItems,
}: AppSidebarProps) {
    return (
        <SideBar
            isOpen={isOpen}
            onOverlayClick={onClose}
        >
            <div className="flex h-full flex-col">
                <div className="flex items-center justify-between px-5 py-4">
                    <div className="flex min-w-0 items-center gap-3">
                        <BrandMark />
                        <div className="min-w-0">
                            <div className="text-base font-bold tracking-tight">TaskForge</div>
                            <div className="text-xs text-gray-500 dark:text-gray-400">Multi-tenant workspace</div>
                        </div>
                    </div>

                    <Button
                        variant="secondary"
                        size="sm"
                        onClick={onClose}
                        className="lg:hidden"
                        aria-label="Close menu"
                    >
                        ✕
                    </Button>
                </div>

                <WorkspaceDropdown userEmail={userEmail} />

                <nav className="flex-1 space-y-1 px-3">
                    {navItems.map((item) => (
                        <SideBarItem
                            key={item.label}
                            href={item.href}
                            isActive={item.isActive}
                        >
                            <span>{item.icon}</span> {item.label}
                        </SideBarItem>
                    ))}

                    <div className="my-4 border-t border-gray-200 dark:border-gray-800" />

                    <SideBarItem
                        href="#"
                        isActive={false}
                    >
                        <span>⚙️</span> Settings
                    </SideBarItem>
                </nav>

                <div className="border-t border-gray-200 p-4 dark:border-gray-800">
                    <div className="flex items-center gap-3">
                        <div className="h-10 w-10 rounded-2xl bg-gray-200 dark:bg-gray-800" />
                        <div className="min-w-0 flex-1">
                            <div className="truncate text-sm font-semibold">{userName}</div>
                            <div className="truncate text-xs text-gray-500 dark:text-gray-400">Owner</div>
                        </div>
                        <Button
                            variant="secondary"
                            size="sm"
                            onClick={() => void onLogout()}
                            disabled={isLoggingOut}
                            aria-label="Log out"
                            className="hidden lg:inline-flex"
                        >
                            {isLoggingOut ? 'Logging out...' : 'Log out'}
                        </Button>
                        <Button
                            variant="secondary"
                            size="sm"
                            onClick={onToggleDarkMode}
                            className="hidden lg:inline-flex"
                            aria-label="Toggle dark mode"
                        >
                            🌓
                        </Button>
                    </div>
                </div>
            </div>
        </SideBar>
    );
}
