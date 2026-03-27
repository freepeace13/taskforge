import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import type { ComponentProps } from 'react';
import TaskModal from './TaskModal';

function renderTaskModal(overrides: Partial<ComponentProps<typeof TaskModal>> = {}) {
    const defaultProps: ComponentProps<typeof TaskModal> = {
        isOpen: true,
        onClose: vi.fn(),
    };

    const props = { ...defaultProps, ...overrides };
    render(<TaskModal {...props} />);

    return props;
}

describe('TaskModal', () => {
    it('does not render content when closed', () => {
        renderTaskModal({ isOpen: false });

        expect(screen.queryByText('Create task')).not.toBeInTheDocument();
    });

    it('renders modal content when open', () => {
        renderTaskModal();

        expect(screen.getByText('Create task')).toBeInTheDocument();
        expect(screen.getByPlaceholderText('e.g. Implement invites')).toBeInTheDocument();
    });

    it('calls onClose when cancel button is clicked', async () => {
        const user = userEvent.setup();
        const props = renderTaskModal();

        await user.click(screen.getByRole('button', { name: 'Cancel' }));

        expect(props.onClose).toHaveBeenCalledOnce();
    });

    it('calls onClose when create button is clicked', async () => {
        const user = userEvent.setup();
        const props = renderTaskModal();

        await user.click(screen.getByRole('button', { name: 'Create' }));

        expect(props.onClose).toHaveBeenCalledOnce();
    });
});

