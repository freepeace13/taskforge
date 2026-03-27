import './bootstrap';
import '../css/app.css';
import { createRoot } from 'react-dom/client';
import { createInertiaApp } from '@inertiajs/react';
import AppLayout from '@/features/layout/AppLayout';

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob('./pages/**/*.tsx', { eager: true }) as Record<string, any>;
        let page = pages[`./pages/${name}.tsx`];
        page.default.layout = page.default.layout || ((page: any) => <AppLayout children={page} />);
        return page;
    },
    setup({ el, App, props }) {
        const root = createRoot(el);

        root.render(<App {...props} />);
    },
});

