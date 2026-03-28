import type { ReactNode } from 'react';

type ProjectsPaginationProps = {
    pageLabel: string;
    previous: ReactNode;
    next: ReactNode;
};

export default function ProjectsPagination({ pageLabel, previous, next }: ProjectsPaginationProps) {
    return (
        <div className="mt-6 flex items-center justify-between gap-3 text-sm text-gray-600 dark:text-gray-400">
            <span>{pageLabel}</span>
            <div className="flex gap-2">
                {previous}
                {next}
            </div>
        </div>
    );
}
