/**
 * Base visual theme for shared UI. Parent layouts should pass the active theme so
 * components do not rely on document-level `dark` class alone.
 */
export type SharedUiTheme = 'light' | 'dark';

/**
 * Extend every shared UI component’s public props with this so surfaces and text
 * colors stay consistent with the active theme.
 */
export interface SharedUiBaseProps {
    /** @default 'light' */
    theme?: SharedUiTheme;
}

export const DEFAULT_SHARED_UI_THEME: SharedUiTheme = 'light';

/**
 * Resolves the active theme. When `theme` is omitted, uses `<html class="dark">`
 * in the browser so shared UI matches the app shell; falls back to light during SSR.
 */
export function resolveSharedUiTheme(theme: SharedUiTheme | undefined): SharedUiTheme {
    if (theme !== undefined) {
        return theme;
    }

    if (typeof document !== 'undefined' && document.documentElement.classList.contains('dark')) {
        return 'dark';
    }

    return DEFAULT_SHARED_UI_THEME;
}

/**
 * Selects a class string for the resolved theme. Prefer this over `dark:` when
 * styling must follow the explicit {@link SharedUiTheme} prop.
 */
export function sharedUiThemeClass<T extends string>(
    theme: SharedUiTheme,
    classes: { light: T; dark: T },
): T {
    return classes[theme];
}
