import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { useState } from 'react';
import { describe, expect, it, vi } from 'vitest';
import Dropdown, { DropdownContent, DropdownTrigger } from './Dropdown';

describe('Dropdown', () => {
    it('opens the panel when the trigger is activated', async () => {
        const user = userEvent.setup();

        render(
            <Dropdown>
                <DropdownTrigger>Open</DropdownTrigger>
                <DropdownContent menuLabel="Test menu">
                    <div>Panel content</div>
                </DropdownContent>
            </Dropdown>,
        );

        expect(screen.queryByText('Panel content')).not.toBeInTheDocument();

        await user.click(screen.getByRole('button', { name: 'Open' }));

        expect(screen.getByRole('menu', { name: 'Test menu' })).toBeInTheDocument();
        expect(screen.getByText('Panel content')).toBeInTheDocument();
    });

    it('closes when clicking outside', async () => {
        const user = userEvent.setup();

        render(
            <div>
                <Dropdown>
                    <DropdownTrigger>Open</DropdownTrigger>
                    <DropdownContent>
                        <div>Inside</div>
                    </DropdownContent>
                </Dropdown>
                <button type="button">Outside</button>
            </div>,
        );

        await user.click(screen.getByRole('button', { name: 'Open' }));
        expect(screen.getByText('Inside')).toBeInTheDocument();

        await user.click(screen.getByRole('button', { name: 'Outside' }));

        expect(screen.queryByText('Inside')).not.toBeInTheDocument();
    });

    it('closes on Escape', async () => {
        const user = userEvent.setup();

        render(
            <Dropdown>
                <DropdownTrigger>Open</DropdownTrigger>
                <DropdownContent>
                    <div>Inside</div>
                </DropdownContent>
            </Dropdown>,
        );

        await user.click(screen.getByRole('button', { name: 'Open' }));
        expect(screen.getByText('Inside')).toBeInTheDocument();

        await user.keyboard('{Escape}');

        expect(screen.queryByText('Inside')).not.toBeInTheDocument();
    });

    it('notifies controlled open changes', async () => {
        const user = userEvent.setup();
        const onOpenChange = vi.fn();

        function ControlledDropdown() {
            const [open, setOpen] = useState(false);

            return (
                <Dropdown
                    open={open}
                    onOpenChange={(next) => {
                        onOpenChange(next);
                        setOpen(next);
                    }}
                >
                    <DropdownTrigger>Open</DropdownTrigger>
                    <DropdownContent>
                        <div>Inside</div>
                    </DropdownContent>
                </Dropdown>
            );
        }

        render(<ControlledDropdown />);

        await user.click(screen.getByRole('button', { name: 'Open' }));

        expect(onOpenChange).toHaveBeenCalledWith(true);
        expect(screen.getByText('Inside')).toBeInTheDocument();
    });
});
