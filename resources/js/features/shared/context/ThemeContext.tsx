import {createContext, useCallback, useContext, useEffect, useState} from 'react';

type ThemeContextValue = {
    isDark: boolean;
    toggleDarkMode: () => void;
}

const ThemeContext = createContext<ThemeContextValue | undefined>(undefined);

export function ThemeProvider({children}: {children: React.ReactNode}) {
    const [isDark, setIsDark] = useState(false);

    useEffect(() => {
        const root = document.documentElement;
        const savedTheme = window.localStorage.getItem('theme');

        if (savedTheme === 'dark') {
            root.classList.add('dark');
            setIsDark(true);
        } else {
            root.classList.remove('dark');
            setIsDark(false);
        }
    }, []);

    const toggleDarkMode = useCallback(() => {
        const root = document.documentElement;
        const nextIsDark = !isDark;

        setIsDark(nextIsDark);
        root.classList.toggle('dark', nextIsDark);
        window.localStorage.setItem('theme', nextIsDark ? 'dark' : 'light');
    }, [isDark]);

    return (
        <ThemeContext.Provider value={{ isDark, toggleDarkMode }}>
            {children}
        </ThemeContext.Provider>
    );
}


export function useTheme(): ThemeContextValue {
    const context = useContext(ThemeContext);

    if (!context) {
        throw new Error('useTheme must be used within a ThemeProvider');
    }

    return context;
}
