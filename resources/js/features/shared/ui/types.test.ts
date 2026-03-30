import { describe, expect, it } from 'vitest';
import {
    DEFAULT_SHARED_UI_THEME,
    resolveSharedUiTheme,
    sharedUiThemeClass,
} from './types';

describe('shared UI theme helpers', () => {
    it('defaults undefined theme to light when html has no dark class', () => {
        document.documentElement.classList.remove('dark');
        expect(resolveSharedUiTheme(undefined)).toBe(DEFAULT_SHARED_UI_THEME);
    });

    it('defaults undefined theme to dark when html has dark class', () => {
        document.documentElement.classList.add('dark');
        expect(resolveSharedUiTheme(undefined)).toBe('dark');
        document.documentElement.classList.remove('dark');
    });

    it('preserves explicit theme', () => {
        expect(resolveSharedUiTheme('dark')).toBe('dark');
        document.documentElement.classList.remove('dark');
        expect(resolveSharedUiTheme('light')).toBe('light');
    });

    it('picks class map entry for theme', () => {
        expect(
            sharedUiThemeClass('light', { light: 'a', dark: 'b' }),
        ).toBe('a');
        expect(
            sharedUiThemeClass('dark', { light: 'a', dark: 'b' }),
        ).toBe('b');
    });
});
