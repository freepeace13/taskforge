import { render, screen } from '@testing-library/react';
import { describe, expect, it } from 'vitest';
import SideBarItem from './SideBarItem';

describe('SideBarItem', () => {
    it('applies active styles when isActive is true', () => {
        const { container } = render(
            <SideBarItem isActive>
                <span>Home</span>
            </SideBarItem>,
        );

        const el = container.firstChild as HTMLElement;
        expect(el.className).toContain('font-semibold');
    });

    it('applies active styles when isActive is a function returning true', () => {
        const { container } = render(
            <SideBarItem isActive={() => true}>
                <span>Home</span>
            </SideBarItem>,
        );

        const el = container.firstChild as HTMLElement;
        expect(el.className).toContain('font-semibold');
    });

    it('applies inactive styles when isActive is a function returning false', () => {
        const { container } = render(
            <SideBarItem isActive={() => false}>
                <span>Home</span>
            </SideBarItem>,
        );

        const el = container.firstChild as HTMLElement;
        expect(el.className).not.toContain('font-semibold');
    });

    it('renders anchor when href is set', () => {
        render(
            <SideBarItem href="/dash" isActive={false}>
                Dash
            </SideBarItem>,
        );

        expect(screen.getByRole('link', { name: 'Dash' })).toHaveAttribute('href', '/dash');
    });
});
