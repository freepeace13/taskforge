import { render, screen } from '@testing-library/react';
import { describe, expect, it } from 'vitest';
import FlashMessage from './FlashMessage';

describe('FlashMessage', () => {
    it('renders success copy', () => {
        render(<FlashMessage variant="success" message="Saved." />);
        expect(screen.getByRole('alert')).toHaveTextContent('Saved.');
    });

    it('renders error copy', () => {
        render(<FlashMessage variant="error" message="Failed." />);
        expect(screen.getByRole('alert')).toHaveTextContent('Failed.');
    });
});
