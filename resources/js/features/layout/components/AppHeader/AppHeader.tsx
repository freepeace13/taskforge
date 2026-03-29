import { Button } from '@/features/shared/ui';
import { BrandMark } from '@/features/layout/components/BrandMark';
import AppBar, { appBarDesktopToolbarClasses, appBarMobileNavClasses } from '@/features/layout/components/shell/AppBar';

type AppHeaderProps = {
    onOpenSidebar: () => void;
    onToggleDarkMode: () => void;
    onOpenTaskModal: () => void;
};

export default function AppHeader({ onOpenSidebar, onToggleDarkMode, onOpenTaskModal }: AppHeaderProps) {
    return (
        <>
            <AppBar className={appBarMobileNavClasses}>
                <div className="flex items-center gap-3 px-4 py-3">
                    <Button
                        variant="secondary"
                        size="sm"
                        onClick={onOpenSidebar}
                        aria-label="Open menu"
                    >
                        ☰
                    </Button>

                    <div className="flex flex-1 items-center gap-3">
                        <BrandMark className="h-9 w-9 shrink-0 rounded-2xl object-contain" />
                        <div>
                            <div className="text-sm font-bold tracking-tight">TaskForge</div>
                            <div className="text-xs text-gray-500 dark:text-gray-400">Dashboard</div>
                        </div>
                    </div>

                    <Button
                        variant="secondary"
                        size="sm"
                        onClick={onToggleDarkMode}
                        aria-label="Toggle dark mode"
                    >
                        🌓
                    </Button>
                </div>
            </AppBar>

            <AppBar className={appBarDesktopToolbarClasses}>
                <div className="flex items-center gap-3 px-6 py-4">
                    <BrandMark className="h-9 w-9 shrink-0 rounded-2xl object-contain" />
                    <div className="flex-1">
                        <div className="relative">
                            <span className="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-400">
                                ⌘
                            </span>
                            <input
                                className="w-full rounded-2xl border border-gray-200 bg-white px-9 py-2 text-sm shadow-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500/40 dark:border-gray-800 dark:bg-gray-900 dark:placeholder:text-gray-500"
                                placeholder="Search (Cmd+K)"
                            />
                        </div>
                    </div>

                    <Button
                        size="md"
                        onClick={onOpenTaskModal}
                    >
                        + New
                    </Button>

                    <Button
                        variant="secondary"
                        size="sm"
                        aria-label="Notifications"
                    >
                        🔔
                    </Button>

                    <div className="h-9 w-9 rounded-2xl bg-gray-200 dark:bg-gray-800" />
                </div>
            </AppBar>
        </>
    );
}
