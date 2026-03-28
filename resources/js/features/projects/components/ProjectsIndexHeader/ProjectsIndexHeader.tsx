import type { ReactNode } from 'react';

type ProjectsIndexHeaderProps = {
    organizationName: string;
    title: string;
    description: string;
    actions: ReactNode;
};

export default function ProjectsIndexHeader({ organizationName, title, description, actions }: ProjectsIndexHeaderProps) {
    return (
        <div className="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div>
                <p className="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">{organizationName}</p>
                <h1 className="text-xl font-bold tracking-tight">{title}</h1>
                <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">{description}</p>
            </div>
            <div className="flex flex-wrap gap-2">{actions}</div>
        </div>
    );
}
