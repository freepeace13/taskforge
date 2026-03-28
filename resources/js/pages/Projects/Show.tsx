import type { PageProps } from '@inertiajs/core';
import { Head, Link } from '@inertiajs/react';
import { route } from 'ziggy-js';
import { ProjectShowSection, projectPrimaryLinkClass, projectSecondaryLinkClass, type ProjectAttributes } from '@/features/projects';
import FlashMessages from '@/components/FlashMessages';

type ProjectsShowProps = PageProps & {
    organization: { slug: string; name: string };
    project: ProjectAttributes;
};

export default function ProjectsShow({ organization, project }: ProjectsShowProps) {
    const description = project.description ? (
        <p className="mt-2 max-w-2xl text-sm text-gray-600 dark:text-gray-300">{project.description}</p>
    ) : (
        <p className="mt-2 text-sm text-gray-500 dark:text-gray-400">No description.</p>
    );

    return (
        <>
            <Head title={project.name} />

            <FlashMessages />

            <ProjectShowSection
                organizationName={organization.name}
                title={project.name}
                description={description}
                archived={Boolean(project.archived_at)}
                actions={
                    <>
                        <Link href={route('projects.index')} className={projectSecondaryLinkClass}>
                            All projects
                        </Link>
                        <Link href={route('projects.edit', { project: project.id })} className={projectSecondaryLinkClass}>
                            Edit
                        </Link>
                        <Link href={route('projects.tasks.index', { project: project.id })} className={projectPrimaryLinkClass}>
                            Open tasks
                        </Link>
                    </>
                }
            />
        </>
    );
}
