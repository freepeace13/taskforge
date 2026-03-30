import type { PageProps } from '@inertiajs/core';
import { Head, Link, router } from '@inertiajs/react';
import { route } from 'ziggy-js';
import {
    projectPaginationLinkClass,
    projectPrimaryLinkClass,
    projectSecondaryLinkClass,
    projectTableLinkBrandClass,
    projectTableLinkMutedClass,
    ProjectsIndexHeader,
    ProjectsListTable,
    type ProjectListRow,
    ProjectsPagination,
    type PaginatedProjects,
    type ProjectAttributes,
} from '@/features/projects';
import FlashMessages from '@/components/FlashMessages';
import { projectRouteParam } from '@/utils/routeBindings';

type ProjectsIndexProps = PageProps & {
    organization: { slug: string; name: string };
    projects: PaginatedProjects;
};

function buildRows(orgSlug: string, projects: ProjectAttributes[]): ProjectListRow[] {
    return projects.map((project) => ({
        project,
        actions: (
            <>
                <Link href={route('projects.show', { org: orgSlug, project: projectRouteParam(project) })} className={projectTableLinkBrandClass}>
                    View
                </Link>
                <Link href={route('projects.tasks.index', { org: orgSlug, project: projectRouteParam(project) })} className={projectTableLinkMutedClass}>
                    Tasks
                </Link>
                <Link href={route('projects.edit', { org: orgSlug, project: projectRouteParam(project) })} className={projectTableLinkMutedClass}>
                    Edit
                </Link>
            </>
        ),
    }));
}

export default function ProjectsIndex({ organization, projects }: ProjectsIndexProps) {
    const handleDelete = (projectSlug: string) => {
        if (!window.confirm('Delete this project? Tasks under it may become inaccessible from this list.')) {
            return;
        }
        router.delete(route('projects.destroy', { org: organization.slug, project: projectSlug }));
    };

    const rows = buildRows(organization.slug, projects.data);

    return (
        <>
            <Head title="Projects" />

            <FlashMessages />

            <ProjectsIndexHeader
                organizationName={organization.name}
                title="Projects"
                description="Manage and track organization projects."
                actions={
                    <>
                        <Link href={route('tasks.hub', { org: organization.slug })} className={projectSecondaryLinkClass}>
                            Task hub
                        </Link>
                        <Link href={route('projects.create', { org: organization.slug })} className={projectPrimaryLinkClass}>
                            + New Project
                        </Link>
                    </>
                }
            />

            <ProjectsListTable
                rows={rows}
                emptyMessage="No projects yet. Create one to get started."
                onDeleteRequest={handleDelete}
            />

            {projects.last_page > 1 ? (
                <ProjectsPagination
                    pageLabel={`Page ${projects.current_page} of ${projects.last_page} (${projects.total} projects)`}
                    previous={
                        projects.prev_page_url ? (
                            <Link href={projects.prev_page_url} className={projectPaginationLinkClass}>
                                Previous
                            </Link>
                        ) : null
                    }
                    next={
                        projects.next_page_url ? (
                            <Link href={projects.next_page_url} className={projectPaginationLinkClass}>
                                Next
                            </Link>
                        ) : null
                    }
                />
            ) : null}
        </>
    );
}
