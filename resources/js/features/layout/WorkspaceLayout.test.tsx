import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import type { ReactNode } from 'react';
import { usePage } from '@inertiajs/react';
import WorkspaceLayout from './WorkspaceLayout';

vi.mock('@inertiajs/react', async () => {
    const actual = await vi.importActual<typeof import('@inertiajs/react')>('@inertiajs/react');

    return {
        ...actual,
        usePage: vi.fn(),
    };
});

function renderLayout(
    ui: ReactNode,
    overrides: Partial<{ name: string; email: string }> = {},
) {
    const name = overrides.name ?? 'Ada Lovelace';
    const email = overrides.email ?? 'ada@example.com';

    vi.mocked(usePage).mockReturnValue({
        props: {
            auth: {
                user: { name, email },
            },
        },
    } as ReturnType<typeof usePage>);

    return render(
        <WorkspaceLayout onLogoutRequest={vi.fn()}>{ui}</WorkspaceLayout>,
    );
}

describe('WorkspaceLayout', () => {
    beforeEach(() => {
        vi.mocked(usePage).mockReset();
    });

    it('renders children and user identity in the account menu', async () => {
        const user = userEvent.setup();
        renderLayout(<div>Workspace content</div>);

        expect(screen.getByText('Workspace content')).toBeInTheDocument();

        await user.click(screen.getByRole('button', { name: /open account menu/i }));

        expect(screen.getByText('Ada Lovelace')).toBeInTheDocument();
        expect(screen.getByText('ada@example.com')).toBeInTheDocument();
    });

    it('calls the provided logout action from the dropdown', async () => {
        const user = userEvent.setup();
        const onLogoutRequest = vi.fn();

        vi.mocked(usePage).mockReturnValue({
            props: {
                auth: {
                    user: { name: 'Test', email: 'test@example.com' },
                },
            },
        } as ReturnType<typeof usePage>);

        render(
            <WorkspaceLayout onLogoutRequest={onLogoutRequest}>
                <div>Page</div>
            </WorkspaceLayout>,
        );

        await user.click(screen.getByRole('button', { name: /open account menu/i }));
        await user.click(screen.getByRole('button', { name: 'Log out' }));

        expect(onLogoutRequest).toHaveBeenCalledOnce();
    });
});
