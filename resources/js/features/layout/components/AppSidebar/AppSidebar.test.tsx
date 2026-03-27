import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import type { ComponentProps } from 'react';
import AppSidebar from './AppSidebar';

function renderSidebar(overrides: Partial<ComponentProps<typeof AppSidebar>> = {}) {
    const defaultProps: ComponentProps<typeof AppSidebar> = {
        isOpen: true,
        onClose: vi.fn(),
        onToggleDarkMode: vi.fn(),
        onLogout: vi.fn(),
        isLoggingOut: false,
        userName: 'Ada Lovelace',
        userEmail: 'ada@example.com',
        navItems: [
            { href: '/dashboard', label: 'Dashboard', icon: '🏠', isActive: true },
            { href: '/tasks', label: 'Tasks', icon: '✅' },
        ],
    };

    const props = { ...defaultProps, ...overrides };
    render(<AppSidebar {...props} />);

    return props;
}

describe('AppSidebar', () => {
    it('calls onClose when the close menu button is clicked', async () => {
        const user = userEvent.setup();
        const props = renderSidebar();

        await user.click(screen.getByRole('button', { name: 'Close menu' }));

        expect(props.onClose).toHaveBeenCalledOnce();
    });

    it('shows logging out text when logout is in progress', () => {
        renderSidebar({ isLoggingOut: true });

        expect(screen.getByRole('button', { name: 'Log out' })).toHaveTextContent('Logging out...');
    });
});

