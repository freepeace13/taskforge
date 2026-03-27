import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import type { ComponentProps } from 'react';
import AppLayout from './AppLayout';

function renderLayout(overrides: Partial<ComponentProps<typeof AppLayout>> = {}) {
    const defaultProps: ComponentProps<typeof AppLayout> = {
        userName: 'Ada Lovelace',
        userEmail: 'ada@example.com',
        navItems: [{ href: '/dashboard', label: 'Dashboard', icon: '🏠', isActive: true }],
        onLogoutRequest: vi.fn(),
        children: <div>Page content</div>,
    };

    const props = { ...defaultProps, ...overrides };
    render(<AppLayout {...props} />);

    return props;
}

describe('AppLayout', () => {
    it('renders children and user identity from page-provided props', () => {
        renderLayout();

        expect(screen.getByText('Page content')).toBeInTheDocument();
        expect(screen.getByText('Ada Lovelace')).toBeInTheDocument();
    });

    it('calls the provided logout action', async () => {
        const user = userEvent.setup();
        const props = renderLayout();

        await user.click(screen.getByRole('button', { name: 'Log out' }));

        expect(props.onLogoutRequest).toHaveBeenCalledOnce();
    });
});
