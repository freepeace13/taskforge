import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { describe, expect, it, vi } from 'vitest';
import { ThemeProvider } from '@/features/shared/context/ThemeContext';
import AppLayout from './AppLayout';

const { logout } = vi.hoisted(() => ({
    logout: vi.fn(),
}));

vi.mock('@/features/shared/context/AuthContext', () => ({
    useAuth: () => ({
        user: { id: 1, authId: null, name: 'Ada Lovelace', email: 'ada@example.test' },
        tenant: null,
        organizations: [],
        switchTenant: vi.fn(),
        logout,
    }),
}));

describe('AppLayout', () => {
    it('renders children and uses AuthContext logout', async () => {
        const user = userEvent.setup();

        render(
            <ThemeProvider>
                <AppLayout navItems={[{ href: '/workspaces', icon: '🏢', label: 'Workspaces' }]}>
                    <div>Page content</div>
                </AppLayout>
            </ThemeProvider>,
        );

        expect(screen.getByText('Page content')).toBeInTheDocument();

        const logoutButtons = screen.getAllByRole('button', { name: /log out/i });
        await user.click(logoutButtons[0]!);

        expect(logout).toHaveBeenCalledOnce();
    });
});

