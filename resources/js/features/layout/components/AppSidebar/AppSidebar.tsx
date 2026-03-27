import { Button } from '@/features/shared/ui';

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
    onLogout: () => void;
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
            <label className="block text-xs font-medium text-gray-500 dark:text-gray-400">Organization</label>
            <button
                type="button"
                className="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-3 py-2 text-left text-sm font-semibold shadow-sm hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-900 dark:hover:bg-gray-800"
            >
                <div className="flex items-center justify-between">
                    <span className="truncate">My workspace</span>
                    <span className="text-gray-400">⌄</span>
                </div>
                <div className="mt-1 truncate text-xs text-gray-500 dark:text-gray-400">{userEmail}</div>
            </button>
        </div>
    );
}

function SidebarNavLink({ href, label, icon, isActive = false }: SidebarNavItem) {
    return (
        <a
            href={href}
            className={`flex items-center gap-3 rounded-2xl px-3 py-2 text-sm ${
                isActive
                    ? 'bg-gray-100 font-semibold text-gray-900 dark:bg-gray-900 dark:text-gray-100'
                    : 'font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900'
            }`}
        >
            <span>{icon}</span> {label}
        </a>
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
        <>
            <div
                className={`fixed inset-0 z-40 bg-black/40 lg:hidden ${isOpen ? '' : 'hidden'}`}
                aria-hidden={!isOpen}
                onClick={onClose}
            />

            <aside
                className={`fixed inset-y-0 left-0 z-50 w-72 border-r border-gray-200 bg-white transition-transform dark:border-gray-800 dark:bg-gray-950 lg:translate-x-0 ${
                    isOpen ? 'translate-x-0' : '-translate-x-full'
                }`}
            >
                <div className="flex h-full flex-col">
                    <div className="flex items-center justify-between px-5 py-4">
                        <div>
                            <div className="text-base font-bold tracking-tight">TaskForge</div>
                            <div className="text-xs text-gray-500 dark:text-gray-400">Multi-tenant workspace</div>
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
                            <SidebarNavLink
                                key={item.label}
                                {...item}
                            />
                        ))}

                        <div className="my-4 border-t border-gray-200 dark:border-gray-800" />

                        <SidebarNavLink
                            href="#"
                            icon="⚙️"
                            label="Settings"
                        />
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
                                onClick={onLogout}
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
            </aside>
        </>
    );
}

