import type { ReactNode } from 'react';

type ProjectShowSectionProps = {
    organizationName: string;
    title: string;
    description: ReactNode;
    archived: boolean;
    actions: ReactNode;
};

export default function ProjectShowSection({ organizationName, title, description, archived, actions }: ProjectShowSectionProps) {
    return (
        <div className="mb-6 flex flex-wrap items-start justify-between gap-4">
            <div>
                <p className="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">{organizationName}</p>
                <h1 className="text-xl font-bold tracking-tight">{title}</h1>
                {description}
                {archived ? (
                    <p className="mt-3 text-xs font-medium uppercase tracking-wide text-amber-700 dark:text-amber-300">Archived</p>
                ) : null}
            </div>

            <div className="flex flex-wrap gap-2">{actions}</div>
        </div>
    );
}
