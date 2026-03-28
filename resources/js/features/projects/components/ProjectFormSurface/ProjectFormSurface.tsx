import type { ReactNode } from 'react';

type ProjectFormSurfaceProps = {
    children: ReactNode;
};

export default function ProjectFormSurface({ children }: ProjectFormSurfaceProps) {
    return (
        <div className="mx-auto max-w-2xl rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            {children}
        </div>
    );
}
