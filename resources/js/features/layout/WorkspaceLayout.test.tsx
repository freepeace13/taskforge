import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { describe, expect, it, vi } from 'vitest';
import { ThemeProvider } from '@/features/shared/context/ThemeContext';
import WorkspaceLayout from './WorkspaceLayout';

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

describe('WorkspaceLayout', () => {
    it('renders children and logs out via account menu', async () => {
        const user = userEvent.setup();

        render(
            <ThemeProvider>
                <WorkspaceLayout>
                    <div>Workspace page</div>
                </WorkspaceLayout>
            </ThemeProvider>,
        );

        expect(screen.getByText('Workspace page')).toBeInTheDocument();

        await user.click(screen.getByRole('button', { name: /open account menu/i }));
        await user.click(screen.getByRole('button', { name: /log out/i }));

        expect(logout).toHaveBeenCalledOnce();
    });
});

