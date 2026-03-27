import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import type { ComponentProps } from 'react';
import AppHeader from './AppHeader';

function renderHeader(overrides: Partial<ComponentProps<typeof AppHeader>> = {}) {
    const defaultProps: ComponentProps<typeof AppHeader> = {
        onOpenSidebar: vi.fn(),
        onToggleDarkMode: vi.fn(),
        onOpenTaskModal: vi.fn(),
    };

    const props = { ...defaultProps, ...overrides };
    render(<AppHeader {...props} />);

    return props;
}

describe('AppHeader', () => {
    it('calls onOpenSidebar when open menu button is clicked', async () => {
        const user = userEvent.setup();
        const props = renderHeader();

        await user.click(screen.getByRole('button', { name: 'Open menu' }));

        expect(props.onOpenSidebar).toHaveBeenCalledOnce();
    });

    it('calls onToggleDarkMode when toggle dark mode button is clicked', async () => {
        const user = userEvent.setup();
        const props = renderHeader();

        await user.click(screen.getByRole('button', { name: 'Toggle dark mode' }));

        expect(props.onToggleDarkMode).toHaveBeenCalledOnce();
    });

    it('calls onOpenTaskModal when new task button is clicked', async () => {
        const user = userEvent.setup();
        const props = renderHeader();

        await user.click(screen.getByRole('button', { name: '+ New' }));

        expect(props.onOpenTaskModal).toHaveBeenCalledOnce();
    });
});

