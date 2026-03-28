type ProjectPageHeaderProps = {
    organizationName: string;
    title: string;
    subtitle?: string;
};

export default function ProjectPageHeader({ organizationName, title, subtitle }: ProjectPageHeaderProps) {
    return (
        <div className="mb-6">
            <p className="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">{organizationName}</p>
            <h1 className="text-xl font-bold tracking-tight">{title}</h1>
            {subtitle ? <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">{subtitle}</p> : null}
        </div>
    );
}
